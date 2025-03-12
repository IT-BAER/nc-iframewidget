<!--
  - SPDX-FileCopyrightText: 2021 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->
# Changelog

## [v6.2.0](https://github.com/nextcloud/webpack-vue-config/tree/v6.2.0) (2024-10-04)

[Full Changelog](https://github.com/nextcloud-libraries/webpack-vue-config/compare/v6.1.1...v6.2.0)

### ⚠️ Breaking

* `sass-loader` is upgraded from `v14` to `v16` with modern Sass JS API.
  This is not expected to be a breaking change. But it might affect configs with custom `sass` configuration.
  See SASS docs for details: https://sass-lang.com/documentation/js-api/interfaces/legacystringoptions/

### Dependencies

* chore(deps-dev): Bump @babel/core from 7.25.2 to 7.25.7 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/618
* chore(deps-dev): Bump sass from 1.79.3 to 1.79.4 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/617
* chore(deps-dev): Bump webpack from 5.94.0 to 5.95.0 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/615
* chore(deps-dev): Bump sass-loader from 14.2.1 to 16.0.2 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/612

## [v6.1.1](https://github.com/nextcloud/webpack-vue-config/tree/v6.1.1) (2024-09-23)

### 🐛 Bug Fix

* fix: include `process` Node polyfill by @ShGKme in https://github.com/nextcloud-libraries/webpack-vue-config/pull/613

## [v6.1.0](https://github.com/nextcloud/webpack-vue-config/tree/v6.1.0) (2024-09-17)
* Update package npm version by @skjnldsv in https://github.com/nextcloud-libraries/webpack-vue-config/pull/608
* Fix npm audit by @nextcloud-command in https://github.com/nextcloud-libraries/webpack-vue-config/pull/609
* Dependencies update

## [v6.0.1](https://github.com/nextcloud/webpack-vue-config/tree/v6.0.1) (2024-01-29)

[Full Changelog](https://github.com/nextcloud/webpack-vue-config/compare/v5.5.1...v6.0.0)

### 🐛 Bug Fix

* fix: ignore `./locale` import from `moment/min/moment-with-locales` by @ShGKme in https://github.com/nextcloud-libraries/webpack-vue-config/pull/533

### Dependencies

* chore(deps-dev): Bump sass from 1.65.1 to 1.66.1 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/492
* chore(deps-dev): Bump @babel/core from 7.22.10 to 7.22.11 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/494
* chore(deps-dev): Bump vue-loader from 15.10.1 to 15.10.2 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/495
* chore(deps-dev): Bump @babel/core from 7.22.11 to 7.22.17 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/496
* chore(deps-dev): Bump @babel/core from 7.22.17 to 7.22.19 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/497
* chore(deps-dev): Bump sass from 1.66.1 to 1.67.0 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/498
* chore(deps-dev): Bump @babel/core from 7.22.19 to 7.22.20 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/499
* chore(deps-dev): Bump sass from 1.67.0 to 1.68.0 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/500
* chore(deps-dev): Bump @nextcloud/eslint-config from 8.2.1 to 8.3.0 in /tests by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/501
* chore(deps-dev): Bump @babel/core from 7.22.20 to 7.23.0 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/502
* chore(deps-dev): Bump sass from 1.68.0 to 1.69.0 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/503
* chore(deps): Bump postcss from 8.4.26 to 8.4.31 in /tests by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/504
* chore(deps-dev): Bump @babel/core from 7.23.0 to 7.23.2 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/505
* chore(deps-dev): Bump sass from 1.69.0 to 1.69.3 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/506
* chore(deps-dev): Bump ts-loader from 9.4.4 to 9.5.0 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/508
* chore(deps-dev): Bump webpack from 5.88.2 to 5.89.0 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/507
* chore(deps-dev): Bump @babel/traverse from 7.23.0 to 7.23.2 in /tests by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/509
* chore(deps-dev): Bump sass from 1.69.3 to 1.69.4 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/511
* chore(deps-dev): Bump vue-loader from 15.10.2 to 15.11.1 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/510
* chore(deps-dev): Bump browserify-sign from 4.2.1 to 4.2.2 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/512
* chore(deps): Bump vue from 2.7.14 to 2.7.15 in /tests by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/513
* chore(deps-dev): Bump sass from 1.69.4 to 1.69.5 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/515
* chore(deps-dev): Bump vue-template-compiler from 2.7.14 to 2.7.15 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/514
* chore(deps-dev): Bump @babel/core from 7.23.2 to 7.23.3 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/516
* chore(deps-dev): Bump ts-loader from 9.5.0 to 9.5.1 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/517
* chore(deps-dev): Bump @babel/core from 7.23.3 to 7.23.5 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/519
* chore(deps-dev): Bump node-polyfill-webpack-plugin from 2.0.1 to 3.0.0 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/520
* chore(deps-dev): Bump @babel/core from 7.23.5 to 7.23.6 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/521
* chore: update workflows from templates by @nextcloud-command in https://github.com/nextcloud-libraries/webpack-vue-config/pull/522
* chore(deps-dev): Bump vue-template-compiler from 2.7.15 to 2.7.16 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/524
* chore(deps-dev): Bump @babel/core from 7.23.6 to 7.23.7 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/525
* chore(deps-dev): Bump sass-loader from 13.3.2 to 13.3.3 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/526
* chore(deps-dev): Bump sass from 1.69.5 to 1.69.6 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/527
* chore(deps): Bump vue from 2.7.15 to 2.7.16 in /tests by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/528
* chore(deps-peer): Bump Vue from 2.7.14 to 2.7.16 by @ShGKme in https://github.com/nextcloud-libraries/webpack-vue-config/pull/523
* chore(deps-dev): Bump sass from 1.69.6 to 1.69.7 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/529
* chore(deps-dev): Bump follow-redirects from 1.15.2 to 1.15.4 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/530
* chore(deps-dev): Bump @nextcloud/browserslist-config from 2.3.0 to 3.0.0 in /tests by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/493
* chore(deps-dev): Bump style-loader from 3.3.3 to 3.3.4 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/531
* chore(deps-dev): Bump css-loader from 6.8.1 to 6.9.0 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/532
* chore(deps-dev): Bump css-loader from 6.9.0 to 6.9.1 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/536
* chore(deps-dev): Bump sass from 1.69.7 to 1.70.0 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/535
* chore(deps-dev): Bump @nextcloud/stylelint-config from 2.3.1 to 2.4.0 in /tests by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/537
* chore(deps-dev): Bump webpack from 5.89.0 to 5.90.0 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/538
* chore(deps-dev): Bump @babel/core from 7.23.7 to 7.23.9 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/539

## [v6.0.0](https://github.com/nextcloud/webpack-vue-config/tree/v6.0.0) (2023-08-16)

[Full Changelog](https://github.com/nextcloud/webpack-vue-config/compare/v5.5.1...v6.0.0)

### Breaking
* chore: update node engines to next LTS by @nextcloud-command in https://github.com/nextcloud-libraries/webpack-vue-config/pull/470
  We now supports node `^20.0.0` and npm `^9.0.0`

### :bug: Bug Fix
* fix: pass ts to ts-loader before babel by @skjnldsv in https://github.com/nextcloud-libraries/webpack-vue-config/pull/472
* fix: polyfill node:Buffer by default by @ShGKme in https://github.com/nextcloud-libraries/webpack-vue-config/pull/489
* Revert "Disable cache in dev mode" by @pulsejet in https://github.com/nextcloud-libraries/webpack-vue-config/pull/456
* Update rules to prevent prettier to run by @christianlupus in https://github.com/nextcloud-libraries/webpack-vue-config/pull/475

### Dependencies
* chore: Fix URLs after package was transfered by @susnux in https://github.com/nextcloud-libraries/webpack-vue-config/pull/484
* chore(deps-dev): Bump @babel/core from 7.22.1 to 7.22.10 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/488
* chore(deps-dev): Bump @nextcloud/stylelint-config from 2.3.0 to 2.3.1 in /tests by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/477
* chore(deps-dev): Bump babel-loader from 9.1.2 to 9.1.3 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/473
* chore(deps-dev): Bump css-loader from 6.7.4 to 6.8.1 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/463
* chore(deps-dev): Bump sass from 1.62.1 to 1.65.1 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/487
* chore(deps-dev): Bump sass-loader from 13.3.0 to 13.3.2 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/467
* chore(deps-dev): Bump webpack from 5.88.1 to 5.88.2 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/482
* chore(deps-dev): Bump webpack-cli from 5.1.1 to 5.1.4 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/466
* chore(deps-dev): Bump webpack-dev-server from 4.11.1 to 4.15.1 by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/483
* chore(deps-dev): Bump word-wrap from 1.2.3 to 1.2.4 in /tests by @dependabot in https://github.com/nextcloud-libraries/webpack-vue-config/pull/478
* fix: dependencies and audit by @skjnldsv in https://github.com/nextcloud-libraries/webpack-vue-config/pull/485

## [v5.5.1](https://github.com/nextcloud-libraries/webpack-vue-config/tree/v5.5.1) (2023-04-25)

[Full Changelog](https://github.com/nextcloud-libraries/webpack-vue-config/compare/v5.5.0...v5.5.1)

#### :bug: Bug Fix
* [#449](https://github.com/nextcloud-libraries/webpack-vue-config/pull/449) Do not polyfill available in a web browser Node modules ([@ShGKme](https://github.com/ShGKme))

#### Committers: 1
- Grigorii K. Shartsev ([@ShGKme](https://github.com/ShGKme))

## [v5.5.0](https://github.com/nextcloud-libraries/webpack-vue-config/tree/v5.5.0) (2023-03-15)

[Full Changelog](https://github.com/nextcloud-libraries/webpack-vue-config/compare/v5.4.0...v5.5.0)

**Implemented enhancements:**

- Add ts support [\#421](https://github.com/nextcloud-libraries/webpack-vue-config/pull/421) ([skjnldsv](https://github.com/skjnldsv))
- Add alias for resolving vue [\#432](https://github.com/nextcloud-libraries/webpack-vue-config/pull/432) ([raimund-schluessler](https://github.com/raimund-schluessler))
- Add public path config information for HMR [\#431](https://github.com/nextcloud-libraries/webpack-vue-config/pull/431) ([artonge](https://github.com/artonge))
- Resolve ts files [\#424](https://github.com/nextcloud-libraries/webpack-vue-config/pull/424) ([skjnldsv](https://github.com/skjnldsv))

**Fixed bugs:**

- Put vue as dependency to sync with vue-template-compiler [\#371](https://github.com/nextcloud-libraries/webpack-vue-config/pull/371) ([skjnldsv](https://github.com/skjnldsv))

**Merged dependencies:**

- Bump @babel/core from 7.19.6 to 7.20.12 
- Bump babel-loader from 8.2.5 to 9.1.2 
- Bump css-loader from 6.7.1 to 6.7.3 
- Bump json5 from 1.0.1 to 1.0.2 
- Bump loader-utils from 1.4.0 to 1.4.2 
- Bump sass from 1.55.0 to 1.59.2 
- Bump sass-loader from 13.1.0 to 13.2.0 
- Bump vue-loader from 15.10.0 to 15.10.1 
- Bump vue-template-compiler from 2.7.13 to 2.7.14 
- Bump webpack-cli from 4.10.0 to 5.0.1

## [v5.4.0](https://github.com/nextcloud-libraries/webpack-vue-config/tree/v5.4.0) (2022-10-25)

[Full Changelog](https://github.com/nextcloud-libraries/webpack-vue-config/compare/v5.3.0...v5.4.0)

**Changed:**

* Upgrade packages minors and fix deep selector support [\#399](https://github.com/nextcloud-libraries/webpack-vue-config/pull/399) ([skjnldsv](https://github.com/skjnldsv))
  * @babel/core from `^7.16.7` to `^7.19.6`
  * babel-loader from `^8.2.3` to `^8.2.5`
  * css-loader from `^6.5.1` to `^6.7.1`
  * sass from `^1.47.0` to `^1.55.0`
  * sass-loader from `^13.0.1` to `^13.1.0`
  * vue-loader from `^15.9.8` to `^15.10.0`
  * vue-template-compiler from `^2.7.0` to `^2.7.13`
  * webpack from `^5.66.0` to `^5.74.0`
  * webpack-cli from `^4.9.1` to `^4.10.0`
  * webpack-dev-server from `^4.7.2` to `^4.11.1`

## [v5.3.0](https://github.com/nextcloud-libraries/webpack-vue-config/tree/v5.3.0) (2022-08-10)

[Full Changelog](https://github.com/nextcloud-libraries/webpack-vue-config/compare/v5.2.1...v5.3.0)

**Changed:**

- Update engines versions to Node 16 with Npm 7 or Npm 8

## [v5.2.1](https://github.com/nextcloud-libraries/webpack-vue-config/tree/v5.2.1) (2022-07-02)

[Full Changelog](https://github.com/nextcloud-libraries/webpack-vue-config/compare/v5.2.0...v5.2.1)

**Fixed bugs:**

- Fix vue-template-compiler version [\#356](https://github.com/nextcloud-libraries/webpack-vue-config/pull/356) ([skjnldsv](https://github.com/skjnldsv))

## [v5.2.0](https://github.com/nextcloud-libraries/webpack-vue-config/tree/v5.2.0) (2022-07-02)

[Full Changelog](https://github.com/nextcloud-libraries/webpack-vue-config/compare/v5.1.0...v5.2.0)

**Merged pull requests:**

- Dependencies updates
- Bump vue-template-compiler from 2.6.14 to 2.7.0

## [v5.1.0](https://github.com/nextcloud-libraries/webpack-vue-config/tree/v5.1.0) (2022-04-30)

[Full Changelog](https://github.com/nextcloud-libraries/webpack-vue-config/compare/v5.0.0...v5.1.0)

**Implemented enhancements:**

- Do not lint during build [\#318](https://github.com/nextcloud-libraries/webpack-vue-config/pull/318) ([skjnldsv](https://github.com/skjnldsv))

**Merged pull requests:**

- @nextcloud/eslint-config^8.0.0 compatibility
- Bump @babel/core to 7.17.10
- Bump async to 2.6.4
- Bump babel-loader to 8.2.5
- Bump css-loader to 6.7.1
- Bump eslint to 8.11.0
- Bump minimist to 1.2.6
- Bump node-forge to 1.3.0
- Bump sass to 1.51.0
- Bump stylelint to 14.6.0
## [v5.0.0](https://github.com/nextcloud-libraries/webpack-vue-config/tree/v5.0.0) (2022-01-18)

[Full Changelog](https://github.com/nextcloud-libraries/webpack-vue-config/compare/v4.3.2...v5.0.0)

**Fixed bugs:**

- Fix devDependencies [\#279](https://github.com/nextcloud-libraries/webpack-vue-config/pull/279) ([skjnldsv](https://github.com/skjnldsv))

**Merged pull requests:**

- ⚠️ Eslint 8 [\#274](https://github.com/nextcloud-libraries/webpack-vue-config/pull/274) ([skjnldsv](https://github.com/skjnldsv))

## [v4.3.2](https://github.com/nextcloud-libraries/webpack-vue-config/tree/v4.3.2) (2022-01-18)

[Full Changelog](https://github.com/nextcloud-libraries/webpack-vue-config/compare/v4.3.1...v4.3.2)

**Fixed bugs:**

- Fix devDependencies [\#279](https://github.com/nextcloud-libraries/webpack-vue-config/pull/279) ([skjnldsv](https://github.com/skjnldsv))

## [v4.3.1](https://github.com/nextcloud-libraries/webpack-vue-config/tree/v4.3.1) (2022-01-17)

[Full Changelog](https://github.com/nextcloud-libraries/webpack-vue-config/compare/v4.3.0...v4.3.1)

**Dependencies updates:**

- Unpin webpack version [\#275](https://github.com/nextcloud-libraries/webpack-vue-config/pull/275) ([artonge](https://github.com/artonge))
- Bump stylelint-webpack-plugin from 3.1.0 to 3.1.1 [\#277](https://github.com/nextcloud-libraries/webpack-vue-config/pull/277) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump sass from 1.47.0 to 1.48.0 [\#276](https://github.com/nextcloud-libraries/webpack-vue-config/pull/276) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump sass to 1.47.0 [\#273](https://github.com/nextcloud-libraries/webpack-vue-config/pull/273) ([skjnldsv](https://github.com/skjnldsv))
- Bump sass from 1.46.0 to 1.47.0 [\#271](https://github.com/nextcloud-libraries/webpack-vue-config/pull/271) ([dependabot[bot]](https://github.com/apps/dependabot))

## [v4.3.0](https://github.com/nextcloud-libraries/webpack-vue-config/tree/v4.3.0) (2022-01-07)

[Full Changelog](https://github.com/nextcloud-libraries/webpack-vue-config/compare/v4.2.0...v4.3.0)

**Fixed bugs:**

- Bump dependencies [\#269](https://github.com/nextcloud-libraries/webpack-vue-config/pull/269) ([skjnldsv](https://github.com/skjnldsv))

**Merged pull requests:**

- 4.3.0 [\#270](https://github.com/nextcloud-libraries/webpack-vue-config/pull/270) ([skjnldsv](https://github.com/skjnldsv))
- Change webpack config for more platform independency [\#268](https://github.com/nextcloud-libraries/webpack-vue-config/pull/268) ([dartcafe](https://github.com/dartcafe))
## [v4.2.0](https://github.com/nextcloud-libraries/webpack-vue-config/tree/v4.2.0) (2021-11-29)

[Full Changelog](https://github.com/nextcloud-libraries/webpack-vue-config/compare/v4.1.2...v4.2.0)

- 4.2.0 [\#253](https://github.com/nextcloud-libraries/webpack-vue-config/pull/253) ([artonge](https://github.com/artonge))
- Update npm and node engines versions by @nextcloud-command in <https://github.com/nextcloud-libraries/webpack-vue-config/pull/241>
- Bump eslint-webpack-plugin from 3.1.0 to 3.1.1 by @dependabot in <https://github.com/nextcloud-libraries/webpack-vue-config/pull/244>
- Fix HMR by @jotoeri in <https://github.com/nextcloud-libraries/webpack-vue-config/pull/246>
- Bump webpack-dev-server from 4.4.0 to 4.5.0 by @dependabot in <https://github.com/nextcloud-libraries/webpack-vue-config/pull/249>
- Bump sass from 1.43.4 to 1.43.5 by @dependabot in <https://github.com/nextcloud-libraries/webpack-vue-config/pull/252>
- Move stylelint to 14 by @skjnldsv in <https://github.com/nextcloud-libraries/webpack-vue-config/pull/242>

## [v4.1.4](https://github.com/nextcloud-libraries/webpack-vue-config/tree/v4.1.4) (2021-11-08)

[Full Changelog](https://github.com/nextcloud-libraries/webpack-vue-config/compare/v4.1.3...v4.1.4)

- Limit webpack update to patch versions of 5.46 by @artonge in <https://github.com/nextcloud-libraries/webpack-vue-config/pull/238>

## [v4.1.3](https://github.com/nextcloud-libraries/webpack-vue-config/tree/v4.1.3) (2021-11-08)

[Full Changelog](https://github.com/nextcloud-libraries/webpack-vue-config/compare/v4.1.2...v4.1.3)

**Merged pull requests:**

- Change webpack version in peerDep to match devDep by @artonge in <https://github.com/nextcloud-libraries/webpack-vue-config/pull/233>
- Bump webpack from 5.46.0 to 5.62.1 by @dependabot in <https://github.com/nextcloud-libraries/webpack-vue-config/pull/235>
- Bump css-loader from 6.5.0 to 6.5.1 by @dependabot in <https://github.com/nextcloud-libraries/webpack-vue-config/pull/236>

## [v4.1.2](https://github.com/nextcloud-libraries/webpack-vue-config/tree/v4.1.2) (2021-11-03)

[Full Changelog](https://github.com/nextcloud-libraries/webpack-vue-config/compare/v4.1.1...v4.1.2)

**Merged pull requests:**

- Revert "Bump webpack from 5.46.0 to 5.61.0" [\#232](https://github.com/nextcloud-libraries/webpack-vue-config/pull/232) ([skjnldsv](https://github.com/skjnldsv))
- Revert "Bump eslint from 7.31.0 to 8.1.0" [\#231](https://github.com/nextcloud-libraries/webpack-vue-config/pull/231) ([skjnldsv](https://github.com/skjnldsv))

## [v4.1.1](https://github.com/nextcloud-libraries/webpack-vue-config/tree/v4.1.1) (2021-11-03)

[Full Changelog](https://github.com/nextcloud-libraries/webpack-vue-config/compare/v4.1.0...v4.1.1)

**Implemented enhancements:**

- Add style testing [\#230](https://github.com/nextcloud-libraries/webpack-vue-config/pull/230) ([skjnldsv](https://github.com/skjnldsv))
- Use Asset Modules plus upgrade to npm 7 [\#165](https://github.com/nextcloud-libraries/webpack-vue-config/pull/165) ([Pytal](https://github.com/Pytal))

**Merged pull requests:**

- Don't fail build by linter on development [\#163](https://github.com/nextcloud-libraries/webpack-vue-config/pull/163) ([jotoeri](https://github.com/jotoeri))
