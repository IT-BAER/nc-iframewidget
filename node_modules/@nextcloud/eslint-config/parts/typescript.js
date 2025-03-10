/**
 * SPDX-FileCopyrightText: 2023 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */
/** Rules for typescript */
module.exports = {
	files: ['**/*.ts', '**/*.cts', '**/*.mts', '**/*.tsx'],
	extends: [
		'@vue/eslint-config-typescript/recommended',
		'plugin:import/typescript',
	],
	parser: '@typescript-eslint/parser',
	parserOptions: {},
	rules: {
		// allow for generic type parameters on function calls
		'func-call-spacing': 'off',
		'@typescript-eslint/func-call-spacing': 'error',
		//
		'n/no-missing-import': 'off',
		'import/extensions': 'off',
		'jsdoc/check-tag-names': [
			'warn', {
				definedTags: [
					// for projects using typedoc
					'notExported',
					'packageDocumentation',
					// for jest
					'jest-environment',
				],
			},
		],
		// Does not make sense with TypeScript
		'jsdoc/require-param-type': 'off',
		'jsdoc/require-returns-type': 'off',
		//
		'@typescript-eslint/no-empty-function': 'off',
	},
	settings: {
		'import/resolver': {
			typescript: {
				alwaysTryTypes: true,
			},
			node: {
				paths: ['src'],
				extensions: ['.(m|c)?js', '.ts', '.tsx', '.vue'],
			},
		},
	},
}
