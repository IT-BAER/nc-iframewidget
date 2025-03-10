/**
 * SPDX-FileCopyrightText: 2019 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */
const base = require('./parts/base.js')
const typescriptOverrides = require('./parts/typescript.js')
const vueOverrides = require('./parts/vue.js')

/**
 * Config for Vue + Javascript projects (optionally with parts, except vue files, written in Typescript)
 */
module.exports = {
	// Base rules
	...base,
	// basic Typescript rules
	overrides: [
		{
			...typescriptOverrides,
		},
		// Setup different vue parser to support `<script setup>` correctly
		{
			...vueOverrides,
		},
	],
}
