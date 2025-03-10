## @nextcloud/babel-config

[![npm last version](https://img.shields.io/npm/v/@nextcloud/babel-config.svg?style=flat-square)](https://www.npmjs.com/package/@nextcloud/babel-config)
[![Dependabot status](https://img.shields.io/badge/Dependabot-enabled-brightgreen.svg?longCache=true&style=flat-square&logo=dependabot)](https://dependabot.com)


This is a package containing the unified global babel config used by all nextcloud apps.
It contains the necessary dependencies and peerDependencies so that other apps cannot update if this config does not support it.
Please always use dependabot to update your apps, OR pay attention to the peer dependencies error messages!


## Installation

```bash
npm install @nextcloud/babel-config --save-dev
```

## Usage

Add a file `babel.config.js` in the root directory of your app repository with the following content:

```js
const babelConfig = require('@nextcloud/babel-config')

module.exports = babelConfig
```

## Release new version

 1. Bump the package version with `npm version`
 2. Push version bump commit
 3. Create a new release with proper changelog https://github.com/nextcloud-libraries/babel-config/releases/new
