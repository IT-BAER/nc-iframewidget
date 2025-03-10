"use strict"

const { createVersionValidator } = require('../utils/version-parser.js')

//------------------------------------------------------------------------------
// Rule Definition
//------------------------------------------------------------------------------

const global = {
    autosize: '29.0.0',
    escapeHTML: '20.0.0',
    fileDownloadPath: '15.0.0',
    formatDate: '19.0.0',
    getScrollBarWidth: '15.0.0',
    getURLParameter: '19.0.0',
    humanFileSize: '19.0.0',
    marked: '19.0.0',
    relative_modified_date: '19.0.0',
}

const oc = {
    getScrollBarWidth: '15.0.0',
    addTranslations: '26.0.0',
    appSettings: '28.0.0',
    loadScript: '28.0.0',
    loadStyle: '28.0.0',
}

const oc_sub = {
    AppConfig: {
        hasKey: '15.0.0',
        deleteApp: '15.0.0',
    },
    Util: {
        hasSVGSupport: '15.0.0',
        replaceSVGIcon: '15.0.0',
        replaceSVG: '15.0.0',
        scaleFixForIE8: '15.0.0',
        isIE8: '15.0.0',
    },
}

const oca = {
    // ref: https://github.com/nextcloud/server/commit/6eced42b7a40f5b0ea0489244583219d0ee2e7af
    Search: '20.0.0',
}

// TODO: handle OC.x.y.z like OC.Share.ShareConfigModel.areAvatarsEnabled()
//       ref https://github.com/nextcloud/server/issues/11045

module.exports = {
    meta: {
        docs: {
            description: "Removed Nextcloud APIs",
            category: "Nextcloud",
            recommended: true
        },
        fixable: null,  // or "code" or "whitespace"
        schema: [
            {
                // We accept one option which is an object
                type: "object",
                properties: {
                    // if we should try to find an appinfo and only handle APIs removed before the max-version
                    parseAppInfo: {
                        type: "boolean"
                    },
                    // Set a Nextcloud target version, only APIs removed before that versions are checked
                    targetVersion: {
                        type: "string"
                    }
                },
                "additionalProperties": false
            }
        ],
        messages: {
            removedGlobal: "The global property or function {{name}} was removed in Nextcloud {{version}}"
        }
    },

    create: function (context) {
       const checkTargetVersion = createVersionValidator(context)

        return {
            MemberExpression: function (node) {
                // OCA.x
                if (node.object.name === 'OCA'
                    && oca.hasOwnProperty(node.property.name)) {
                    context.report(node, "The property or function OCA." + node.property.name + " was removed in Nextcloud " + oc[node.property.name]);
                }

                // OC.x
                if (node.object.name === 'OC'
                    && oc.hasOwnProperty(node.property.name)
                    && checkTargetVersion(oc[node.property.name])) {
                    context.report(node, "The property or function OC." + node.property.name + " was removed in Nextcloud " + oc[node.property.name])
                }

                // OC.x.y
                if (node.object.type === 'MemberExpression'
                    && node.object.object.name === 'OC'
                    && oc_sub.hasOwnProperty(node.object.property.name)
                    && oc_sub[node.object.property.name].hasOwnProperty(node.property.name)) {
                    const version = oc_sub[node.object.property.name][node.property.name]
                    if (checkTargetVersion(version)) {
                        const prop = [
                            "OC",
                            node.object.property.name,
                            node.property.name,
                        ].join('.')
                        context.report(node, "The property or function " + prop + " was removed in Nextcloud " + version)
                    }
                }
            },
            Program() {
                // Logic adapted from https://github.com/eslint/eslint/blob/master/lib/rules/no-restricted-globals.js
                const scope = context.getScope()
                const report = ref => {
                    const node = ref.identifier
                    if (checkTargetVersion(global[node.name])) {
                        context.report({
                            node,
                            messageId: 'removedGlobal',
                            data: {
                                name: node.name,
                                version: global[node.name]
                            },
                        })
                    }
                }

                // Report variables declared elsewhere (ex: variables defined as "global" by eslint)
                scope.variables.forEach(variable => {
                    if (!variable.defs.length && global.hasOwnProperty(variable.name)) {
                        variable.references.forEach(report)
                    }
                })

                // Report variables not declared at all
                scope.through.forEach(reference => {
                    if (global.hasOwnProperty(reference.identifier.name)) {
                        report(reference)
                    }
                })
            }
        }
    }
}
