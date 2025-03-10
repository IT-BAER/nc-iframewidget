/**
 * SPDX-FileCopyrightText: 2023 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */
/** Base rules */
module.exports = {
	root: true,
	env: {
		browser: true,
		commonjs: true,
		es6: true,
		node: true,
		// Allow jest syntax in the src folder
		jest: true,
	},
	parser: '@babel/eslint-parser',
	parserOptions: {
		requireConfigFile: false,
	},
	extends: [
		'eslint:recommended',
		'plugin:import/errors',
		'plugin:import/warnings',
		'plugin:n/recommended',
		'plugin:@nextcloud/recommended',
		'plugin:jsdoc/recommended',
		'standard',
	],
	settings: {
		'import/resolver': {
			node: {
				paths: ['src'],
				extensions: ['.js', '.vue'],
			},
			exports: {
				conditions: ['import'],
			},
		},
		jsdoc: {
			tagNamePreference: {
				returns: 'return',
			},
			mode: 'typescript',
		},
	},
	plugins: ['vue', 'n', 'jsdoc'],
	rules: {
		// space before function ()
		'space-before-function-paren': ['error', {
			anonymous: 'never',
			named: 'never',
			asyncArrow: 'always',
		}],
		// stay consistent with array brackets
		'array-bracket-newline': ['error', 'consistent'],
		// tabs only for indentation
		indent: ['error', 'tab'],
		'no-tabs': ['error', { allowIndentationTabs: true }],
		// allow spaces after tabs for alignment
		'no-mixed-spaces-and-tabs': ['error', 'smart-tabs'],
		// only debug console
		'no-console': ['error', { allow: ['error', 'warn', 'info', 'debug'] }],
		// classes blocks
		'padded-blocks': ['error', { classes: 'always' }],
		// always have the operator in front
		'operator-linebreak': ['error', 'before'],
		// ternary on multiline
		'multiline-ternary': ['error', 'always-multiline'],
		// force proper JSDocs
		'jsdoc/check-tag-names': [
			'warn', {
				definedTags: [
					// for jest
					'jest-environment',
				],
			},
		],
		'jsdoc/require-returns': 0,
		'jsdoc/require-returns-description': 0,
		'jsdoc/tag-lines': ['off'],
		// disallow use of "var"
		'no-var': 'error',
		// suggest using const
		'prefer-const': 'error',
		// es6 import/export and require
		'n/no-unpublished-require': ['off'],
		'n/no-unsupported-features/es-syntax': ['off'],
		// always add a trailing comma (for diff readability)
		'comma-dangle': ['warn', 'always-multiline'],
		// Allow shallow import of @vue/test-utils and @testing-library/vue in order to be able to use it in
		// the src folder
		'n/no-unpublished-import': ['error', {
			allowModules: ['@vue/test-utils', '@testing-library/vue'],
		}],
		// require object literal shorthand syntax
		'object-shorthand': ['error', 'always'],
		// Warn when file extensions are not used on import paths
		'import/extensions': ['warn', 'always', {
			ignorePackages: true,
		}],
		// ignore camelcase for __webpack variables
		camelcase: ['error', {
			allow: ['^UNSAFE_', '^__webpack_'],
			properties: 'never',
			ignoreGlobals: true,
		}],
	},
	overrides: [
		{
			// Allow "unpublished" == devDependencies to be imported in config files
			files: ['*.config.*', 'cypress/**/*', 'tests/**/*', '__tests__/**/*', '__mocks__/**/*'],
			rules: {
				'n/no-unpublished-import': 'off',
			},
		},
	],
}
