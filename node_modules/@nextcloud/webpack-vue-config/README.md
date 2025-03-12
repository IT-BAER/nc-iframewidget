<!--
  - SPDX-FileCopyrightText: 2019 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->
# Webpack vue base config

[![REUSE status](https://api.reuse.software/badge/github.com/nextcloud-libraries/webpack-vue-config)](https://api.reuse.software/info/github.com/nextcloud-libraries/webpack-vue-config)
[![npm last version](https://img.shields.io/npm/v/@nextcloud/webpack-vue-config.svg?style=flat-square)](https://www.npmjs.com/package/@nextcloud/webpack-vue-config)
[![Dependabot status](https://img.shields.io/badge/Dependabot-enabled-brightgreen.svg?longCache=true&style=flat-square&logo=dependabot)](https://dependabot.com)

Use this base config package to cleanup all your complicated setups and rely on automated dependencies updates.

## How-to
```js
// webpack.config.js

const webpackConfig = require('@nextcloud/webpack-vue-config')

module.exports = webpackConfig
```

```js
// package.json

...
	"scripts": {
		"build": "webpack --node-env production --progress",
		"dev": "webpack --node-env development --progress",
		"watch": "webpack --node-env development --progress --watch",
		"serve": "webpack --node-env development serve --progress",
	}
...
```

## Hot module replacement

To enjoy hot module replacement, follow those instructions:

- Install the [`HMREnabler`](https://github.com/nextcloud/hmr_enabler) server app. This will tweak the CSP header so do not use it in production !
- Add the `serve` script to your `package.json` as listed above.
- Add `js/*hot-update.*` to you `.gitignore`. This is necessary because we write every files on disk so the nextcloud server can serve them.
- Add the following line in your Vue app entry file so Webpack knows where to fetch updates, [see this example](https://github.com/nextcloud/app-tutorial/blob/master/src/main.js). You might not need it as it default to `/apps/<your_app_name>/js/`.

```js
__webpack_public_path__ = generateFilePath(appName, '', 'js/')
```

You can then start you dev serve with `npm serve` or `make serve-js` if you added this step in your makefile.

- Your Nextcloud server hostname will probably be different than `localhost`. In that case, you will need specify it with `--allowed-hosts`.
- Your public path will probably not be `/apps/{app-name}/js`. In that case, you will need to specify it with `--static-public-path`.

```shell
npm run serve -- --allowed-hosts your-hostname.example [other-hostname.example ...] --static-public-path /your/custom/public/path
```

## Extend with your own configs

Here is an example on how to add your own  config to the base one

```js
// webpack.config.js

const path = require('path')
const webpack = require('webpack')
const webpackConfig = require('@nextcloud/webpack-vue-config')

webpackConfig.entry['files-action'] = path.join(__dirname, 'src', 'files_action.js')
webpackConfig.plugins.push(new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/))

module.exports = webpackConfig
```
### Replace/edit existing rule
All the rules are available individually on the `@nextcloud/webpack-vue-config/rules` file. You can import them and use the one you want.

If you want to override a rule that is already provided by this package, you can use the following to replace it:

```js
// webpack.config.js

const webpackConfig = require('@nextcloud/webpack-vue-config')
const webpackRules = require('@nextcloud/webpack-vue-config/rules')

const BabelLoaderExcludeNodeModulesExcept = require('babel-loader-exclude-node-modules-except')

// Edit JS rule
webpackRules.RULE_JS.test = /\.m?js$/
webpackRules.RULE_JS.exclude = BabelLoaderExcludeNodeModulesExcept([
	'@nextcloud/dialogs',
	'@nextcloud/event-bus',
	'camelcase',
	'fast-xml-parser',
	'hot-patcher',
	'semver',
	'vue-plyr',
	'webdav',
	'toastify-js',
])

// Replaces rules array
webpackConfig.module.rules = Object.values(webpackRules)

module.exports = webpackConfig
```
