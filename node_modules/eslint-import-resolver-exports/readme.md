# eslint-import-resolver-exports

This package adds [`package.json#exports`](https://nodejs.org/api/packages.html#exports) support to [`eslint-plugin-import`](https://www.npmjs.com/package/eslint-plugin-import) using the [resolve.exports](https://github.com/lukeed/resolve.exports) package.

## Usage

Install with `npm install -D eslint-import-resolver-exports` and add it as a resolver to your ESLint configuration. You should always include another resolver (e.g. `eslint-import-resolver-node` or `eslint-import-resolver-typescript`) since this resolver only supports `package.json#exports` and not the other Node.js resolution features.

Example config:

```js
module.exports = {
  // ... other configuration options
  settings: {
    "import/resolver": {
      typescript: {
        project: [__dirname + "/tsconfig.json"],
      },
      exports: {
        // Accepts the same options as the `resolve.exports` package
        // See: https://github.com/lukeed/resolve.exports#optionsunsafe

        // All optional, default values are shown

        // Add "require" field to the conditions
        require: false,
        // Add "browser" field to the conditions
        browser: false,
        // List of additional/custom conditions
        conditions: [],
        // Ignore everything except the `conditions` option
        unsafe: false,
      },
    },
  },
};
```

## Credits and license

- By Fatih Aygün under [MIT license](./LICENSE)
