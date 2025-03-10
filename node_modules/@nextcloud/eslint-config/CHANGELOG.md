<!--
  - SPDX-FileCopyrightText: 2019 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->	
# Changelog

## [v8.4.2](https://github.com/nextcloud-libraries/eslint-config/tree/v8.4.2) (2025-02-16)
### Fixed
* fix(typescript): do not require returns type in jsdoc by @ShGKme in https://github.com/nextcloud-libraries/eslint-config/pull/857

### Changed
* Updated development dependencies
* Add SPDX header  by @AndyScherzinger in https://github.com/nextcloud-libraries/eslint-config/pull/802
* enh(git): ignore formatting commits in git blame by @max-nextcloud in https://github.com/nextcloud-libraries/eslint-config/pull/854

## [v8.4.1](https://github.com/nextcloud-libraries/eslint-config/tree/v8.4.1) (2024-05-16)
[Full Changelog](https://github.com/nextcloud-libraries/eslint-config/compare/v8.4.0...v8.4.1)

### Fixes
* fix(vue3): align rules with Vue 2 rules by @ShGKme in https://github.com/nextcloud-libraries/eslint-config/pull/771
* fix(vue3): config crashes ESLint by @ShGKme in https://github.com/nextcloud-libraries/eslint-config/pull/770

### Changed
* build(deps-dev): Bump eslint-plugin-jsdoc from 48.2.4 to 48.2.5 by @dependabot in https://github.com/nextcloud-libraries/eslint-config/pull/769

## [v8.4.0](https://github.com/nextcloud-libraries/eslint-config/tree/v8.4.0) (2024-05-15)
### Features
* Vue 3 support: There is a new sub-configuration for projects using Vue 3

## [v8.3.0](https://github.com/nextcloud-libraries/eslint-config/tree/v8.3.0) (2023-09-25)
[Full Changelog](https://github.com/nextcloud-libraries/eslint-config/compare/v8.2.1...v8.3.0)

### Features
* Typescript 5 support
* Allow linting of typescript with module resolution set to `node16` or `nodenext` by @susnux in https://github.com/nextcloud-libraries/eslint-config/pull/527
* Support Composition API & Typescript + Vue projects by @susnux in https://github.com/nextcloud-libraries/eslint-config/pull/562
* Allow linting of TSX files by @susnux in https://github.com/nextcloud-libraries/eslint-config/pull/456

### Fixes
* Ignore camelcase rule for __webpack variables by @kesselb in https://github.com/nextcloud-libraries/eslint-config/pull/530
* Add Typescript overrides for all valid Typescript file extensions by @susnux in https://github.com/nextcloud-libraries/eslint-config/pull/567
* Allow empty functions by @skjnldsv in https://github.com/nextcloud-libraries/eslint-config/pull/570
* fix(typescript): Also set the typescript settings to the vue overrides for typescript import resolving by @susnux in https://github.com/nextcloud-libraries/eslint-config/pull/639
* fix(typescript): Allow generic type parameters on function calls by @susnux in https://github.com/nextcloud-libraries/eslint-config/pull/571
* Make sure vue files written in Typescript are linted correctly by @susnux in https://github.com/nextcloud-libraries/eslint-config/pull/579
* fix: Allow to import packages from `devDependencies` within config files by @susnux in https://github.com/nextcloud-libraries/eslint-config/pull/580
* fix: Allow `@jest-environment` docblock for jest tests by @susnux in https://github.com/nextcloud-libraries/eslint-config/pull/592

### Changed
* Various dependencies upgrades
* chore: Update `@nextcloud/eslint-plugin` to version 2.1.0 by @susnux in https://github.com/nextcloud-libraries/eslint-config/pull/605
* Drop webpack dependency by @susnux in https://github.com/nextcloud-libraries/eslint-config/pull/528
538
* Update node engines to next LTS (node 20 and NPM 9) by @nextcloud-command in https://github.com/nextcloud-libraries/eslint-config/pull/563
565
* Make eslint to take this config for linting the config by @susnux in https://github.com/nextcloud-libraries/eslint-config/pull/572
* chore: Fix URLs after package got transfered by @susnux in https://github.com/nextcloud-libraries/eslint-config/pull/602

## [v8.2.1](https://github.com/nextcloud-libraries/eslint-config/tree/v8.2.1) (2023-01-30)

[Full Changelog](https://github.com/nextcloud-libraries/eslint-config/compare/v8.2.0...v8.2.1)

**Fixed:**
- Fix resolving packages that use the package `exports` feature by @susnux in https://github.com/nextcloud-libraries/eslint-config/pull/452

**Merged pull requests:**
- Various dependencies upgrades

## [v8.2.0](https://github.com/nextcloud-libraries/eslint-config/tree/v8.2.0) (2023-01-11)

[Full Changelog](https://github.com/nextcloud-libraries/eslint-config/compare/v8.1.5...v8.2.0)

**Implemented enhancements:**
- feat: Add scripts for linting the config and fixed issues found [\#434](https://github.com/nextcloud-libraries/eslint-config/pull/434) ([susnux](https://github.com/susnux))
- feat: Add sub config for TypeScript projects [\#433](https://github.com/nextcloud-libraries/eslint-config/pull/433) ([susnux](https://github.com/susnux))

**Dependency updates:**
- Bump @babel/core from 7.20.7 to 7.20.12 [\#432](https://github.com/nextcloud-libraries/eslint-config/pull/432) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump eslint from 8.30.0 to 8.31.0 [\#431](https://github.com/nextcloud-libraries/eslint-config/pull/431) ([dependabot[bot]](https://github.com/apps/dependabot))

## [v8.1.5](https://github.com/nextcloud-libraries/eslint-config/tree/v8.1.5) (2023-01-02)

[Full Changelog](https://github.com/nextcloud-libraries/eslint-config/compare/v8.1.4...v8.1.5)

**Dependency updates:**
- Bump json5 from 1.0.1 to 1.0.2 by @PVince81 in https://github.com/nextcloud-libraries/eslint-config/pull/430
- Bump json5 from 2.2.1 to 2.2.3 by @PVince81 in https://github.com/nextcloud-libraries/eslint-config/pull/430

## [v8.1.4](https://github.com/nextcloud-libraries/eslint-config/tree/v8.1.4) (2022-11-17)

[Full Changelog](https://github.com/nextcloud-libraries/eslint-config/compare/v8.1.3...v8.1.4)

**Fixed:**
- Fix eslint-plugin-n conflict [\#421](https://github.com/nextcloud-libraries/eslint-config/pull/421) ([skjnldsv](https://github.com/skjnldsv))

## [v8.1.3](https://github.com/nextcloud-libraries/eslint-config/tree/v8.1.3) (2022-11-04)

[Full Changelog](https://github.com/nextcloud-libraries/eslint-config/compare/v8.1.2...v8.1.3)

**Fixed:**
- Allow TypeScript syntax in JSDoc comments by @Pytal in https://github.com/nextcloud-libraries/eslint-config/pull/413

**Dependency updates:**
- Bump @babel/core from 7.18.10 to 7.19.6 by @dependabot in https://github.com/nextcloud-libraries/eslint-config/pull/404
- Bump @babel/eslint-parser from 7.18.9 to 7.19.1 by @dependabot in https://github.com/nextcloud-libraries/eslint-config/pull/392
- Bump eslint from 8.21.0 to 8.26.0 by @dependabot in https://github.com/nextcloud-libraries/eslint-config/pull/406
- Bump eslint-plugin-jsdoc from 39.3.13 to 39.6.2 by @dependabot in https://github.com/nextcloud-libraries/eslint-config/pull/414
- Bump eslint-plugin-n from 15.2.4 to 15.4.0 by @dependabot in https://github.com/nextcloud-libraries/eslint-config/pull/409
- Bump eslint-plugin-promise from 6.0.0 to 6.1.1 by @dependabot in https://github.com/nextcloud-libraries/eslint-config/pull/405
- Bump eslint-plugin-vue from 9.3.0 to 9.7.0 by @dependabot in https://github.com/nextcloud-libraries/eslint-config/pull/410

## [v8.1.2](https://github.com/nextcloud-libraries/eslint-config/tree/v8.1.2) (2022-08-09)

[Full Changelog](https://github.com/nextcloud-libraries/eslint-config/compare/v8.0.0...v8.1.2)

**Dependency updates:**
- Use [`eslint-plugin-n`](https://www.npmjs.com/package/eslint-plugin-n) instead of [`eslint-plugin-node`](https://www.npmjs.com/package/eslint-plugin-node)

## [v8.0.0](https://github.com/nextcloud-libraries/eslint-config/tree/v8.0.0) (2022-04-20)

[Full Changelog](https://github.com/nextcloud-libraries/eslint-config/compare/v7.0.2...v8.0.0)

**Breaking:**
- Require file extensions on import [\#292](https://github.com/nextcloud-libraries/eslint-config/pull/292) ([Pytal](https://github.com/Pytal))

**Dependency updates:**
- Bump eslint from 8.7.0 to 8.8.0 [\#282](https://github.com/nextcloud-libraries/eslint-config/pull/282) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump eslint from 8.8.0 to 8.9.0 [\#296](https://github.com/nextcloud-libraries/eslint-config/pull/296) ([dependabot[bot]](https://github.com/apps/dependabot))

## [v7.0.2](https://github.com/nextcloud-libraries/eslint-config/tree/v7.0.2) (2022-01-20)

[Full Changelog](https://github.com/nextcloud-libraries/eslint-config/compare/v7.0.1...v7.0.2)

**Fixed:**
- Fix usual same-line vue attribute behaviour [\#272](https://github.com/nextcloud-libraries/eslint-config/pull/272) ([skjnldsv](https://github.com/skjnldsv))

**Dependency updates:**
- Bump eslint from 8.6.0 to 8.7.0 [\#271](https://github.com/nextcloud-libraries/eslint-config/pull/271) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump @babel/core from 7.16.7 to 7.16.10 [\#273](https://github.com/nextcloud-libraries/eslint-config/pull/273) ([dependabot[bot]](https://github.com/apps/dependabot))

## [v7.0.1](https://github.com/nextcloud-libraries/eslint-config/tree/v7.0.1) (2022-01-17)

[Full Changelog](https://github.com/nextcloud-libraries/eslint-config/compare/v7.0.0...v7.0.1)

**Fixed bugs:**
- Allow component names to be single-word [\#270](https://github.com/nextcloud-libraries/eslint-config/pull/270) ([skjnldsv](https://github.com/skjnldsv))

**Dependency updates:**
- Bump webpack from 5.65.0 to 5.66.0 [\#269](https://github.com/nextcloud-libraries/eslint-config/pull/269) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump eslint-plugin-vue from 8.2.0 to 8.3.0 [\#268](https://github.com/nextcloud-libraries/eslint-config/pull/268) ([dependabot[bot]](https://github.com/apps/dependabot))

## [v7.0.0](https://github.com/nextcloud-libraries/eslint-config/tree/v7.0.0) (2022-01-12)

[Full Changelog](https://github.com/nextcloud-libraries/eslint-config/compare/v6.1.2...v7.0.0)

**Merged pull requests:**
- Eslint 8 [\#266](https://github.com/nextcloud-libraries/eslint-config/pull/266) ([skjnldsv](https://github.com/skjnldsv))
- v7.0.0 [\#267](https://github.com/nextcloud-libraries/eslint-config/pull/267) ([skjnldsv](https://github.com/skjnldsv))
## [v6.1.2](https://github.com/nextcloud-libraries/eslint-config/tree/v6.1.2) (2021-12-20)

[Full Changelog](https://github.com/nextcloud-libraries/eslint-config/compare/v6.1.1...v6.1.2)

**Fixes:**
- Fix vue custom event name casing [\#249](https://github.com/nextcloud-libraries/eslint-config/pull/249) ([Pytal](https://github.com/Pytal))

## [v6.1.1](https://github.com/nextcloud-libraries/eslint-config/tree/v6.1.1) (2021-12-02)

[Full Changelog](https://github.com/nextcloud-libraries/eslint-config/compare/v6.1.0...v6.1.1)

**Fixes:**
- Disable tag rule [\#248](https://github.com/nextcloud-libraries/eslint-config/pull/248) ([skjnldsv](https://github.com/skjnldsv))

## [v6.1.0](https://github.com/nextcloud-libraries/eslint-config/tree/v6.1.0) (2021-08-11)

[Full Changelog](https://github.com/nextcloud-libraries/eslint-config/compare/v6.0.1...v6.1.0)

**Fix dependency:**
- Remove undesired eslint-webpack-plugin ([skjnldsv](https://github.com/skjnldsv))

## [v6.0.1](https://github.com/nextcloud-libraries/eslint-config/tree/v6.0.1) (2021-08-11)

[Full Changelog](https://github.com/nextcloud-libraries/eslint-config/compare/v6.0.0...v6.0.1)

**Merged pull requests:**
- Fix license header tags spacing [\#180](https://github.com/nextcloud-libraries/eslint-config/pull/180) ([skjnldsv](https://github.com/skjnldsv))

## [v6.0.0](https://github.com/nextcloud-libraries/eslint-config/tree/v6.0.0)

[Full Changelog](https://github.com/nextcloud-libraries/eslint-config/compare/v5.1.1...v6.0.0)

### Breaking

- Require a space before async arrow function parentheses [\#146](https://github.com/nextcloud-libraries/eslint-config/pull/146) ([Pytal](https://github.com/Pytal))

### Features

- Allow testing-library in src [\#138](https://github.com/nextcloud-libraries/eslint-config/pull/138) ([artonge](https://github.com/artonge))

### Fixes

- Use updated jsdoc rules [\#123](https://github.com/nextcloud-libraries/eslint-config/pull/123) ([artonge](https://github.com/artonge))

## [v5.1.1](https://github.com/nextcloud-libraries/eslint-config/tree/v5.1.1)

[Full Changelog](https://github.com/nextcloud-libraries/eslint-config/compare/v5.1.0...v5.1.1)

- fix version and changelog [\#111](https://github.com/nextcloud-libraries/eslint-config/pull/111) ([dartcafe](https://github.com/dartcafe))

## [v5.1.0](https://github.com/nextcloud-libraries/eslint-config/tree/v5.1.0) (2021-05-03)

[Full Changelog](https://github.com/nextcloud-libraries/eslint-config/compare/v5.1.0...v5.1.0)

**Merged pull requests:**

- remove eslint-plugin-standard [\#103](https://github.com/nextcloud-libraries/eslint-config/pull/103) ([dartcafe](https://github.com/dartcafe))

**Updated dependencies:**

- Bump webpack from 5.36.1 to 5.36.2 [\#112](https://github.com/nextcloud-libraries/eslint-config/pull/112) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump @babel/core from 7.13.16 to 7.14.0 [\#109](https://github.com/nextcloud-libraries/eslint-config/pull/109) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump webpack from 5.36.0 to 5.36.1 [\#108](https://github.com/nextcloud-libraries/eslint-config/pull/108) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump webpack from 5.35.0 to 5.36.0 [\#107](https://github.com/nextcloud-libraries/eslint-config/pull/107) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump eslint from 7.24.0 to 7.25.0 [\#104](https://github.com/nextcloud-libraries/eslint-config/pull/104) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump webpack from 5.34.0 to 5.35.0 [\#102](https://github.com/nextcloud-libraries/eslint-config/pull/102) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump @babel/core from 7.13.15 to 7.13.16 [\#100](https://github.com/nextcloud-libraries/eslint-config/pull/100) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump webpack from 5.33.2 to 5.34.0 [\#99](https://github.com/nextcloud-libraries/eslint-config/pull/99) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump eslint-webpack-plugin from 2.5.3 to 2.5.4 [\#98](https://github.com/nextcloud-libraries/eslint-config/pull/98) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump webpack from 5.32.0 to 5.33.2 [\#97](https://github.com/nextcloud-libraries/eslint-config/pull/97) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump webpack from 5.31.2 to 5.32.0 [\#96](https://github.com/nextcloud-libraries/eslint-config/pull/96) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump eslint-plugin-vue from 7.8.0 to 7.9.0 [\#95](https://github.com/nextcloud-libraries/eslint-config/pull/95) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump eslint from 7.23.0 to 7.24.0 [\#94](https://github.com/nextcloud-libraries/eslint-config/pull/94) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump webpack from 5.31.0 to 5.31.2 [\#93](https://github.com/nextcloud-libraries/eslint-config/pull/93) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump @babel/core from 7.13.14 to 7.13.15 [\#92](https://github.com/nextcloud-libraries/eslint-config/pull/92) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump webpack from 5.30.0 to 5.31.0 [\#91](https://github.com/nextcloud-libraries/eslint-config/pull/91) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump webpack from 5.28.0 to 5.30.0 [\#90](https://github.com/nextcloud-libraries/eslint-config/pull/90) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump @babel/eslint-parser from 7.13.10 to 7.13.14 [\#89](https://github.com/nextcloud-libraries/eslint-config/pull/89) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump eslint from 7.13.0 to 7.23.0 [\#88](https://github.com/nextcloud-libraries/eslint-config/pull/88) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump @babel/core from 7.13.13 to 7.13.14 [\#87](https://github.com/nextcloud-libraries/eslint-config/pull/87) ([dependabot[bot]](https://github.com/apps/dependabot))

## [v5.1.0](https://github.com/nextcloud-libraries/eslint-config/tree/v5.1.0) (2021-05-01)

[Full Changelog](https://github.com/nextcloud-libraries/eslint-config/compare/v5.0.0...v5.1.0)

**Closed issues:**

- Inconsistency between package.json and package-lock.json? [\#106](https://github.com/nextcloud-libraries/eslint-config/issues/106)
- Is eslint-plugin-standard still necessary as peerDependency? [\#101](https://github.com/nextcloud-libraries/eslint-config/issues/101)

## [v5.0.0](https://github.com/nextcloud-libraries/eslint-config/tree/v5.0.0) (2021-03-29)

[Full Changelog](https://github.com/nextcloud-libraries/eslint-config/compare/v5.0.0-beta.0...v5.0.0)

**Implemented enhancements:**

- Bump webpack to ^5.0.0 [\#79](https://github.com/nextcloud-libraries/eslint-config/pull/79) ([skjnldsv](https://github.com/skjnldsv))

**Merged pull requests:**

- Bump eslint from 7.22.0 to 7.23.0 [\#86](https://github.com/nextcloud-libraries/eslint-config/pull/86) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump @babel/core from 7.13.10 to 7.13.13 [\#85](https://github.com/nextcloud-libraries/eslint-config/pull/85) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump eslint-webpack-plugin from 2.5.2 to 2.5.3 [\#84](https://github.com/nextcloud-libraries/eslint-config/pull/84) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump eslint-plugin-vue from 7.7.0 to 7.8.0 [\#83](https://github.com/nextcloud-libraries/eslint-config/pull/83) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump eslint from 7.21.0 to 7.22.0 [\#82](https://github.com/nextcloud-libraries/eslint-config/pull/82) ([dependabot[bot]](https://github.com/apps/dependabot))

## [v4.0.0](https://github.com/nextcloud-libraries/eslint-config/tree/v4.0.0)

[Full Changelog](https://github.com/nextcloud-libraries/eslint-config/compare/v3.0.0...v4.0.0)

**Breaking dependency upgrade:**

- Replace babel-eslint with @babel/eslint-parser [\#80](https://github.com/nextcloud-libraries/eslint-config/pull/80) ([st3iny](https://github.com/st3iny))

**Merged pull requests:**

- Bump elliptic from 6.5.3 to 6.5.4 [\#81](https://github.com/nextcloud-libraries/eslint-config/pull/81) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump eslint-plugin-vue from 7.6.0 to 7.7.0 [\#78](https://github.com/nextcloud-libraries/eslint-config/pull/78) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump eslint from 7.20.0 to 7.21.0 [\#77](https://github.com/nextcloud-libraries/eslint-config/pull/77) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump eslint-webpack-plugin from 2.5.1 to 2.5.2 [\#76](https://github.com/nextcloud-libraries/eslint-config/pull/76) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump eslint-webpack-plugin from 2.5.0 to 2.5.1 [\#75](https://github.com/nextcloud-libraries/eslint-config/pull/75) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump eslint-plugin-vue from 7.5.0 to 7.6.0 [\#74](https://github.com/nextcloud-libraries/eslint-config/pull/74) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump eslint from 7.19.0 to 7.20.0 [\#73](https://github.com/nextcloud-libraries/eslint-config/pull/73) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump eslint-plugin-promise from 4.2.1 to 4.3.1 [\#72](https://github.com/nextcloud-libraries/eslint-config/pull/72) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump eslint-webpack-plugin from 2.4.3 to 2.5.0 [\#71](https://github.com/nextcloud-libraries/eslint-config/pull/71) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump eslint from 7.18.0 to 7.19.0 [\#70](https://github.com/nextcloud-libraries/eslint-config/pull/70) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump eslint-plugin-vue from 7.4.1 to 7.5.0 [\#69](https://github.com/nextcloud-libraries/eslint-config/pull/69) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump eslint-webpack-plugin from 2.4.1 to 2.4.3 [\#68](https://github.com/nextcloud-libraries/eslint-config/pull/68) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump eslint from 7.17.0 to 7.18.0 [\#67](https://github.com/nextcloud-libraries/eslint-config/pull/67) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump webpack from 4.45.0 to 4.46.0 [\#66](https://github.com/nextcloud-libraries/eslint-config/pull/66) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump webpack from 4.44.2 to 4.45.0 [\#65](https://github.com/nextcloud-libraries/eslint-config/pull/65) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump eslint-plugin-vue from 7.4.0 to 7.4.1 [\#64](https://github.com/nextcloud-libraries/eslint-config/pull/64) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump eslint from 7.16.0 to 7.17.0 [\#63](https://github.com/nextcloud-libraries/eslint-config/pull/63) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump eslint-plugin-vue from 7.3.0 to 7.4.0 [\#62](https://github.com/nextcloud-libraries/eslint-config/pull/62) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump eslint from 7.15.0 to 7.16.0 [\#61](https://github.com/nextcloud-libraries/eslint-config/pull/61) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump eslint-plugin-vue from 7.2.0 to 7.3.0 [\#60](https://github.com/nextcloud-libraries/eslint-config/pull/60) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump eslint-plugin-vue from 7.1.0 to 7.2.0 [\#59](https://github.com/nextcloud-libraries/eslint-config/pull/59) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump eslint from 7.14.0 to 7.15.0 [\#58](https://github.com/nextcloud-libraries/eslint-config/pull/58) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump eslint-webpack-plugin from 2.4.0 to 2.4.1 [\#57](https://github.com/nextcloud-libraries/eslint-config/pull/57) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump eslint-webpack-plugin from 2.3.0 to 2.4.0 [\#56](https://github.com/nextcloud-libraries/eslint-config/pull/56) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump eslint from 7.13.0 to 7.14.0 [\#55](https://github.com/nextcloud-libraries/eslint-config/pull/55) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump eslint-config-standard from 16.0.1 to 16.0.2 [\#54](https://github.com/nextcloud-libraries/eslint-config/pull/54) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump eslint-webpack-plugin from 2.2.1 to 2.3.0 [\#53](https://github.com/nextcloud-libraries/eslint-config/pull/53) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump eslint-plugin-standard from 4.0.2 to 4.1.0 [\#52](https://github.com/nextcloud-libraries/eslint-config/pull/52) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump eslint from 7.12.1 to 7.13.0 [\#51](https://github.com/nextcloud-libraries/eslint-config/pull/51) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump eslint-webpack-plugin from 2.2.0 to 2.2.1 [\#50](https://github.com/nextcloud-libraries/eslint-config/pull/50) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump eslint-webpack-plugin from 2.1.0 to 2.2.0 [\#49](https://github.com/nextcloud-libraries/eslint-config/pull/49) ([dependabot[bot]](https://github.com/apps/dependabot))

## [v3.0.0](https://github.com/nextcloud-libraries/eslint-config/tree/v3.0.0) (2020-11-03)

[Full Changelog](https://github.com/nextcloud-libraries/eslint-config/compare/v3.0.0-beta.1...v3.0.0)

## [v3.0.0-beta.1](https://github.com/nextcloud-libraries/eslint-config/tree/v3.0.0-beta.1) (2020-11-03)

[Full Changelog](https://github.com/nextcloud-libraries/eslint-config/compare/v2.2.0...v3.0.0-beta.1)

**Security fixes:**

- \[Security\] Bump lodash from 4.17.15 to 4.17.19 [\#41](https://github.com/nextcloud-libraries/eslint-config/pull/41) ([dependabot-preview[bot]](https://github.com/apps/dependabot-preview))

**Merged pull requests:**

- Feat/eslint 7 [\#48](https://github.com/nextcloud-libraries/eslint-config/pull/48) ([skjnldsv](https://github.com/skjnldsv))
- Bump eslint-plugin-standard from 4.0.1 to 4.0.2 [\#47](https://github.com/nextcloud-libraries/eslint-config/pull/47) ([dependabot-preview[bot]](https://github.com/apps/dependabot-preview))
- Bump @nextcloud/eslint-plugin from 1.4.0 to 1.5.0 [\#46](https://github.com/nextcloud-libraries/eslint-config/pull/46) ([dependabot-preview[bot]](https://github.com/apps/dependabot-preview))
- Bump eslint-plugin-import from 2.22.0 to 2.22.1 [\#45](https://github.com/nextcloud-libraries/eslint-config/pull/45) ([dependabot-preview[bot]](https://github.com/apps/dependabot-preview))
- Bump webpack from 4.44.1 to 4.44.2 [\#44](https://github.com/nextcloud-libraries/eslint-config/pull/44) ([dependabot-preview[bot]](https://github.com/apps/dependabot-preview))
- Bump webpack from 4.44.0 to 4.44.1 [\#43](https://github.com/nextcloud-libraries/eslint-config/pull/43) ([dependabot-preview[bot]](https://github.com/apps/dependabot-preview))
- Bump webpack from 4.43.0 to 4.44.0 [\#42](https://github.com/nextcloud-libraries/eslint-config/pull/42) ([dependabot-preview[bot]](https://github.com/apps/dependabot-preview))

## [v2.2.0](https://github.com/nextcloud-libraries/eslint-config/tree/v2.2.0) (2020-07-08)

[Full Changelog](https://github.com/nextcloud-libraries/eslint-config/compare/v2.1.0...v2.2.0)

## [v2.1.0](https://github.com/nextcloud-libraries/eslint-config/tree/v2.1.0) (2020-07-07)

[Full Changelog](https://github.com/nextcloud-libraries/eslint-config/compare/v2.0.0...v2.1.0)

**Implemented enhancements:**

- Add object-shorthand rule [\#36](https://github.com/nextcloud-libraries/eslint-config/pull/36) ([raimund-schluessler](https://github.com/raimund-schluessler))

**Merged pull requests:**

- Bump eslint-plugin-import from 2.21.2 to 2.22.0 [\#40](https://github.com/nextcloud-libraries/eslint-config/pull/40) ([dependabot-preview[bot]](https://github.com/apps/dependabot-preview))
- Bump eslint-plugin-import from 2.21.1 to 2.21.2 [\#39](https://github.com/nextcloud-libraries/eslint-config/pull/39) ([dependabot-preview[bot]](https://github.com/apps/dependabot-preview))
- Bump eslint-plugin-import from 2.21.0 to 2.21.1 [\#38](https://github.com/nextcloud-libraries/eslint-config/pull/38) ([dependabot-preview[bot]](https://github.com/apps/dependabot-preview))
- Bump eslint-plugin-import from 2.20.2 to 2.21.0 [\#37](https://github.com/nextcloud-libraries/eslint-config/pull/37) ([dependabot-preview[bot]](https://github.com/apps/dependabot-preview))
- Bump webpack from 4.42.1 to 4.43.0 [\#34](https://github.com/nextcloud-libraries/eslint-config/pull/34) ([dependabot-preview[bot]](https://github.com/apps/dependabot-preview))
- Bump @nextcloud/eslint-plugin from 1.3.0 to 1.4.0 [\#33](https://github.com/nextcloud-libraries/eslint-config/pull/33) ([dependabot-preview[bot]](https://github.com/apps/dependabot-preview))
- Bump @nextcloud/eslint-plugin from 1.2.0 to 1.3.0 [\#32](https://github.com/nextcloud-libraries/eslint-config/pull/32) ([dependabot-preview[bot]](https://github.com/apps/dependabot-preview))
- Bump @nextcloud/eslint-plugin from 1.1.0 to 1.2.0 [\#31](https://github.com/nextcloud-libraries/eslint-config/pull/31) ([dependabot-preview[bot]](https://github.com/apps/dependabot-preview))
- Bump eslint-loader from 3.0.3 to 3.0.4 [\#30](https://github.com/nextcloud-libraries/eslint-config/pull/30) ([dependabot-preview[bot]](https://github.com/apps/dependabot-preview))
- Bump eslint-plugin-import from 2.20.1 to 2.20.2 [\#29](https://github.com/nextcloud-libraries/eslint-config/pull/29) ([dependabot-preview[bot]](https://github.com/apps/dependabot-preview))
- Bump eslint-plugin-node from 11.0.0 to 11.1.0 [\#28](https://github.com/nextcloud-libraries/eslint-config/pull/28) ([dependabot-preview[bot]](https://github.com/apps/dependabot-preview))

## [v2.0.0](https://github.com/nextcloud-libraries/eslint-config/tree/v2.0.0) (2020-03-25)

[Full Changelog](https://github.com/nextcloud-libraries/eslint-config/compare/v1.2.0...v2.0.0)

**Implemented enhancements:**

- Update eslint and eslint-plugin-vue packages [\#27](https://github.com/nextcloud-libraries/eslint-config/pull/27) ([ChristophWurst](https://github.com/ChristophWurst))

## [v1.2.0](https://github.com/nextcloud-libraries/eslint-config/tree/v1.2.0) (2020-03-24)

[Full Changelog](https://github.com/nextcloud-libraries/eslint-config/compare/v1.1.1...v1.2.0)

**Merged pull requests:**

- Allow jest [\#26](https://github.com/nextcloud-libraries/eslint-config/pull/26) ([ma12-co](https://github.com/ma12-co))
- Bump webpack from 4.42.0 to 4.42.1 [\#25](https://github.com/nextcloud-libraries/eslint-config/pull/25) ([dependabot-preview[bot]](https://github.com/apps/dependabot-preview))

## [v1.1.1](https://github.com/nextcloud-libraries/eslint-config/tree/v1.1.1) (2020-03-19)

[Full Changelog](https://github.com/nextcloud-libraries/eslint-config/compare/v1.1.0...v1.1.1)

**Security fixes:**

- \[Security\] Bump acorn from 6.4.0 to 6.4.1 [\#24](https://github.com/nextcloud-libraries/eslint-config/pull/24) ([dependabot-preview[bot]](https://github.com/apps/dependabot-preview))

## [v1.1.0](https://github.com/nextcloud-libraries/eslint-config/tree/v1.1.0) (2020-03-03)

[Full Changelog](https://github.com/nextcloud-libraries/eslint-config/compare/v1.0.0...v1.1.0)

**Merged pull requests:**

- Release on github tagging [\#23](https://github.com/nextcloud-libraries/eslint-config/pull/23) ([skjnldsv](https://github.com/skjnldsv))
- Bump eslint-plugin-node to ^11 [\#22](https://github.com/nextcloud-libraries/eslint-config/pull/22) ([skjnldsv](https://github.com/skjnldsv))
- Bump webpack from 4.41.6 to 4.42.0 [\#21](https://github.com/nextcloud-libraries/eslint-config/pull/21) ([dependabot-preview[bot]](https://github.com/apps/dependabot-preview))
- Bump babel-eslint from 10.0.3 to 10.1.0 [\#20](https://github.com/nextcloud-libraries/eslint-config/pull/20) ([dependabot-preview[bot]](https://github.com/apps/dependabot-preview))
- rename to @nextcloud/eslint-config [\#19](https://github.com/nextcloud-libraries/eslint-config/pull/19) ([korelstar](https://github.com/korelstar))

## [v1.0.0](https://github.com/nextcloud-libraries/eslint-config/tree/v1.0.0) (2020-02-11)

[Full Changelog](https://github.com/nextcloud-libraries/eslint-config/compare/v0.1.1...v1.0.0)

**Merged pull requests:**

- Bump eslint-plugin-import from 2.20.0 to 2.20.1 [\#18](https://github.com/nextcloud-libraries/eslint-config/pull/18) ([dependabot-preview[bot]](https://github.com/apps/dependabot-preview))
- Bump eslint-plugin-import from 2.19.1 to 2.20.0 [\#17](https://github.com/nextcloud-libraries/eslint-config/pull/17) ([dependabot-preview[bot]](https://github.com/apps/dependabot-preview))
- Bump webpack from 4.41.4 to 4.41.5 [\#16](https://github.com/nextcloud-libraries/eslint-config/pull/16) ([dependabot-preview[bot]](https://github.com/apps/dependabot-preview))
- switch to @nextcloud/eslint-plugin [\#15](https://github.com/nextcloud-libraries/eslint-config/pull/15) ([korelstar](https://github.com/korelstar))

## [v0.1.1](https://github.com/nextcloud-libraries/eslint-config/tree/v0.1.1) (2019-12-21)

[Full Changelog](https://github.com/nextcloud-libraries/eslint-config/compare/v0.1.0...v0.1.1)

**Merged pull requests:**

- Allow spaces after tabs for alignment [\#14](https://github.com/nextcloud-libraries/eslint-config/pull/14) ([danxuliu](https://github.com/danxuliu))
- Bump webpack from 4.41.3 to 4.41.4 [\#13](https://github.com/nextcloud-libraries/eslint-config/pull/13) ([dependabot-preview[bot]](https://github.com/apps/dependabot-preview))
- Bump webpack from 4.41.2 to 4.41.3 [\#12](https://github.com/nextcloud-libraries/eslint-config/pull/12) ([dependabot-preview[bot]](https://github.com/apps/dependabot-preview))
- Bump eslint-plugin-import from 2.18.2 to 2.19.1 [\#11](https://github.com/nextcloud-libraries/eslint-config/pull/11) ([dependabot-preview[bot]](https://github.com/apps/dependabot-preview))
- Bump eslint-loader from 3.0.2 to 3.0.3 [\#10](https://github.com/nextcloud-libraries/eslint-config/pull/10) ([dependabot-preview[bot]](https://github.com/apps/dependabot-preview))

## [v0.1.0](https://github.com/nextcloud-libraries/eslint-config/tree/v0.1.0) (2019-10-26)

[Full Changelog](https://github.com/nextcloud-libraries/eslint-config/compare/v0.0.6...v0.1.0)

**Merged pull requests:**

- consolidate config [\#8](https://github.com/nextcloud-libraries/eslint-config/pull/8) ([korelstar](https://github.com/korelstar))
- Bump webpack from 4.41.0 to 4.41.2 [\#7](https://github.com/nextcloud-libraries/eslint-config/pull/7) ([dependabot-preview[bot]](https://github.com/apps/dependabot-preview))

## [v0.0.6](https://github.com/nextcloud-libraries/eslint-config/tree/v0.0.6) (2019-10-02)

[Full Changelog](https://github.com/nextcloud-libraries/eslint-config/compare/v0.0.5...v0.0.6)

**Merged pull requests:**

- Bump eslint-loader from 3.0.0 to 3.0.2 [\#5](https://github.com/nextcloud-libraries/eslint-config/pull/5) ([dependabot-preview[bot]](https://github.com/apps/dependabot-preview))
- Bump webpack from 4.40.2 to 4.41.0 [\#3](https://github.com/nextcloud-libraries/eslint-config/pull/3) ([dependabot-preview[bot]](https://github.com/apps/dependabot-preview))
- remove globals [\#2](https://github.com/nextcloud-libraries/eslint-config/pull/2) ([korelstar](https://github.com/korelstar))

## [v0.0.5](https://github.com/nextcloud-libraries/eslint-config/tree/v0.0.5) (2019-09-24)

[Full Changelog](https://github.com/nextcloud-libraries/eslint-config/compare/v0.0.4...v0.0.5)

## [v0.0.4](https://github.com/nextcloud-libraries/eslint-config/tree/v0.0.4) (2019-09-24)

[Full Changelog](https://github.com/nextcloud-libraries/eslint-config/compare/v0.0.3...v0.0.4)

## [v0.0.3](https://github.com/nextcloud-libraries/eslint-config/tree/v0.0.3) (2019-09-24)

[Full Changelog](https://github.com/nextcloud-libraries/eslint-config/compare/v0.0.2...v0.0.3)

## [v0.0.2](https://github.com/nextcloud-libraries/eslint-config/tree/v0.0.2) (2019-09-24)

[Full Changelog](https://github.com/nextcloud-libraries/eslint-config/compare/6632a4d11d27651dd75f3402a6e12b4ae21dc81b...v0.0.2)

**Implemented enhancements:**

- Create first config [\#1](https://github.com/nextcloud-libraries/eslint-config/pull/1) ([skjnldsv](https://github.com/skjnldsv))



\* *This Changelog was automatically generated by [github_changelog_generator](https://github.com/github-changelog-generator/github-changelog-generator)*
