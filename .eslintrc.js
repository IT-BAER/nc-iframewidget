module.exports = {
	root: true,
	globals: {
		APP_VERSION: 'readonly',
		OC: 'readonly',
		OCA: 'readonly',
		OCP: 'readonly',
		t: 'readonly',
		n: 'readonly',
		__webpack_nonce__: 'writable',
		__webpack_public_path__: 'writable',
	},
	extends: [
		'@nextcloud',
	],
	rules: {
		// Keep linting for correctness, but don't fail the build on large-scale formatting.
		indent: 'off',
		semi: 'off',
		'comma-dangle': 'off',
		'no-trailing-spaces': 'off',
		'no-multi-spaces': 'off',
		'object-property-newline': 'off',
		'object-shorthand': 'off',
		'operator-linebreak': 'off',
		'jsdoc/check-alignment': 'off',
		'jsdoc/check-tag-names': 'off',
		'jsdoc/check-types': 'off',
		'jsdoc/require-param': 'off',

		// Vue style rules can be very noisy when introducing linting to an existing codebase.
		'vue/html-indent': 'off',
		'vue/script-indent': 'off',
		'vue/order-in-component': 'off',
		'vue/max-attributes-per-line': 'off',
		'vue/attributes-order': 'off',
		'vue/singleline-html-element-content-newline': 'off',
		'vue/multiline-html-element-content-newline': 'off',
		'vue/html-self-closing': 'off',

		// Additional formatting rules
		'no-mixed-spaces-and-tabs': 'off',
		'no-multiple-empty-lines': 'off',
		quotes: 'off',
		'object-curly-spacing': 'off',
		'quote-props': 'off',
		'no-console': 'off',
		'n/handle-callback-err': 'off',
	},
}
