## @nextcloud/stylelint-config

[![npm last version](https://img.shields.io/npm/v/@nextcloud/stylelint-config.svg?style=flat-square)](https://www.npmjs.com/package/@nextcloud/stylelint-config)
[![Dependabot status](https://img.shields.io/badge/Dependabot-enabled-brightgreen.svg?longCache=true&style=flat-square&logo=dependabot)](https://dependabot.com)


This is a package containing the unified global stylelint config used by all nextcloud apps.
It contains the necessary dependencies and peerDependencies so that other apps cannot update if this config does not support it.
Please always use dependabot to update your apps, OR pay attention to the peer dependencies error messages!


## Installation

```bash
npm install @nextcloud/stylelint-config --save-dev
```

## Usage

Add a file `stylelint.config.js` in the root directory of your app repository with the following content:

```js
const stylelintConfig = require('@nextcloud/stylelint-config')

module.exports = stylelintConfig
```

## Release new version

 1. Bump the package version with `npm version`
 2. Push version bump commit
 3. Create a new release with proper changelog https://github.com/nextcloud/stylelint-config/releases/new
