<!--
  - SPDX-FileCopyrightText: 2019 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->	
## @nextcloud/eslint-config

[![REUSE status](https://api.reuse.software/badge/github.com/nextcloud-libraries/eslint-config)](https://api.reuse.software/info/github.com/nextcloud-libraries/eslint-config)
[![npm last version](https://img.shields.io/npm/v/@nextcloud/eslint-config.svg?style=flat-square)](https://www.npmjs.com/package/@nextcloud/eslint-config)
[![Lint status](https://img.shields.io/github/actions/workflow/status/nextcloud-libraries/eslint-config/lint-eslint.yml?label=lint&style=flat-square)](https://github.com/nextcloud-libraries/eslint-config/actions/workflows/lint.yml)
[![Dependabot status](https://img.shields.io/badge/Dependabot-enabled-brightgreen.svg?longCache=true&style=flat-square&logo=dependabot)](https://dependabot.com)


This is a package containing the unified global eslint config used by all nextcloud apps.
It contains the necessary dependencies and peerDependencies so that other apps cannot update if this config does not support it.
Please always use dependabot to update your apps, OR pay attention to the peer dependencies error messages!


## Installation

```bash
npm install @nextcloud/eslint-config --save-dev
```

## Usage

Add a file `.eslintrc.js` in the root directory of your app repository with the following content:

```js
module.exports = {
	extends: [
		'@nextcloud',
	],
}
```

### Usage with Typescript projects

If your projects uses Typescript for vue files, like `<script lang="ts">` then use the Typescript config instead:

Add a file `.eslintrc.js` in the root directory of your app repository with the following content:

```js
module.exports = {
	extends: [
		'@nextcloud/eslint-config/typescript',
	],
}
```

### Usage with Vue 3 projects

If your projects uses Vue 3 you have to use the `vue3` sub-configuration.
This configuration also includes Typescript support by default.

Add a file `.eslintrc.js` in the root directory of your app repository with the following content:

```js
module.exports = {
	extends: [
		'@nextcloud/eslint-config/vue3',
	],
}
```

## Release new version

 1. Update CHANGELOG.md file with the latest changes
 2. Bump the package version with `npm version`
 3. Push version bump commit
 4. Create a new release with proper changelog https://github.com/nextcloud-libraries/eslint-config/releases/new
