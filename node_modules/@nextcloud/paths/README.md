<!--
  - SPDX-FileCopyrightText: 2019 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: GPL-3.0-or-later
-->
# @nextcloud/paths

[![REUSE status](https://api.reuse.software/badge/github.com/nextcloud-libraries/nextcloud-paths)](https://api.reuse.software/info/github.com/nextcloud-libraries/nextcloud-paths)
[![npm](https://img.shields.io/npm/v/@nextcloud/paths.svg)](https://www.npmjs.com/package/@nextcloud/paths)
[![Documentation](https://img.shields.io/badge/Documentation-online-brightgreen)](https://nextcloud-libraries.github.io/nextcloud-paths/)

Path helpers for Nextcloud apps.

## Installation

```
npm i -S @nextcloud/paths
```

## Usage

```js
import { basename, dirname, encodePath, isSamePath, joinPaths } from '@nextcloud/paths'

basename('/my/file.txt')
// -> 'file.txt'

dirname('/my/file.txt')
// -> '/my'

encodePath('/my/other file.txt')
// -> '/my/other%20file'

isSamePath('/my/file.txt', 'my/file.txt')
// -> true

joinPaths('/my', 'folder', 'file.txt')
// -> '/my/folder/file.txt'
```

