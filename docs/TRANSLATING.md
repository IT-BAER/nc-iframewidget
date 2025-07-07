# Translation Guide for iFrame Widget

This document explains how to contribute translations to the iFrame Widget app for Nextcloud.

## Available Languages

The app currently includes:
- English (default)
- German (de)

## Contributing Translations

If you would like to contribute translations in other languages, please follow these steps:

### Via Transifex (Recommended)

1. Visit the [Nextcloud Transifex page](https://www.transifex.com/nextcloud/nextcloud/)
2. Find the iframewidget resource
3. Select your language or request a new language
4. Translate the strings

The Nextcloud translation bot will automatically incorporate your translations into the app.

### Manual Translation

If you prefer to submit a manual translation:

1. Copy the file `translationfiles/templates/iframewidget.pot` to `translationfiles/<language_code>/iframewidget.po`
   (Replace `<language_code>` with your language code, e.g., `fr` for French)
2. Translate the strings in the .po file
3. Submit a pull request with your translation

## Translation Guidelines

- Keep translations consistent with Nextcloud terminology
- Maintain placeholders like `{name}` or `%s` in your translations
- Preserve HTML tags if they appear in the original string
- Test your translations in the actual app if possible

## Reporting Translation Issues

If you find issues with existing translations, please report them in the [GitHub issue tracker](https://github.com/IT-BAER/nc-iframewidget/issues) with the label "translation".

Thank you for helping make iFrame Widget accessible to more users in their native languages!
