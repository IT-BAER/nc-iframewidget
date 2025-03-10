/**
 * SPDX-FileCopyrightText: 2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */
module.exports = {
	files: ['**/*.vue'],
	parser: 'vue-eslint-parser',
	parserOptions: {
		parser: '@babel/eslint-parser',
	},
	extends: ['plugin:vue/vue3-recommended'],
	rules: {
		'vue/html-indent': ['error', 'tab'],
		// PascalCase components names for vuejs
		// https://vuejs.org/style-guide/rules-strongly-recommended.html#component-name-casing-in-templates
		'vue/component-name-in-template-casing': ['error', 'PascalCase'],
		// force name
		'vue/match-component-file-name': ['error', {
			extensions: ['jsx', 'vue', 'js', 'ts', 'tsx'],
			shouldMatchCase: true,
		}],
		// space before self-closing elements
		'vue/html-closing-bracket-spacing': 'error',
		// no ending html tag on a new line
		'vue/html-closing-bracket-newline': ['error', { multiline: 'never' }],
		// check vue files too
		'n/no-missing-import': ['error', {}],
		// code spacing with attributes
		'vue/max-attributes-per-line': ['error', {
			singleline: 3,
			multiline: 1,
		}],
		// See https://vuejs.org/style-guide/rules-strongly-recommended.html#multi-attribute-elements
		'vue/first-attribute-linebreak': ['error', {
			singleline: 'beside',
			multiline: 'beside',
		}],
		// Allow single-word components names
		'vue/multi-word-component-names': ['off'],
		// custom event naming convention
		'vue/custom-event-name-casing': 'warn',
	},
}
