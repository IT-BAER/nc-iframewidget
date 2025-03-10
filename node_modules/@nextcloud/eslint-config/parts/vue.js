/**
 * SPDX-FileCopyrightText: 2023 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */
module.exports = {
	files: ['**/*.vue'],
	parser: 'vue-eslint-parser',
	parserOptions: {
		parser: '@babel/eslint-parser',
	},
	extends: ['plugin:vue/recommended'],
	rules: {
		'vue/html-indent': ['error', 'tab'],
		// PascalCase components names for vuejs
		// https://vuejs.org/v2/style-guide/#Single-file-component-filename-casing-strongly-recommended
		'vue/component-name-in-template-casing': ['error', 'PascalCase'],
		// force name
		'vue/match-component-file-name': ['error', {
			extensions: ['jsx', 'vue', 'js'],
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
		'vue/first-attribute-linebreak': ['error', {
			singleline: 'beside',
			multiline: 'beside',
		}],
		// Allow single-word components names
		'vue/multi-word-component-names': ['off'],
		// custom event naming convention
		'vue/custom-event-name-casing': ['error', 'kebab-case', {
			// allows custom xxxx:xxx events formats
			ignores: ['/^[a-z]+(?:-[a-z]+)*:[a-z]+(?:-[a-z]+)*$/u'],
		}],
	},
}
