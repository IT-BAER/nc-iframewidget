# Translation Guide for iFrame Widget

This document explains how to contribute translations to the iFrame Widget app for Nextcloud.

## Available Languages

The app currently includes:
- English (default)
- German (de)
- French (fr)
- Spanish (es)
- Italian (it)
- Dutch (nl)
- Russian (ru)
- Polish (pl)
- Portuguese (pt)
- Brazilian Portuguese (pt_BR)
- Simplified Chinese (zh_CN)
- Japanese (ja)
- Czech (cs)
- Swedish (sv)
- Norwegian Bokmål (nb)

## Translation Directory Structure

The app uses the following directory structure for translations:

- `translationfiles/templates/iframewidget.pot` - The POT template file with all translatable strings
- `translationfiles/<language_code>/iframewidget.po` - PO translation files for each language
- `l10n/<language_code>.js` - Compiled JavaScript translation files
- `l10n/<language_code>.json` - JSON translation files used by the app

## Contributing Translations

If you would like to contribute translations in other languages, please follow these steps:

### Manual Translation

1. Copy the file `translationfiles/templates/iframewidget.pot` to `translationfiles/<language_code>/iframewidget.po`
   (Replace `<language_code>` with your language code, e.g., `fr` for French)
2. Translate the strings in the .po file
3. Generate JSON/JS files (see "Building Translation Files" section below)
4. Submit a pull request with your translation

#### PO File Format

PO files contain message pairs of original English strings and their translations. Each entry looks like:

```
#: /path/to/file.js:123
msgid "Original string"
msgstr "Translated string"
```

For plural forms:

```
#: /path/to/file.js:123
msgid "Original singular form"
msgid_plural "Original plural form"
msgstr[0] "Translated singular form"
msgstr[1] "Translated plural form"
```

Different languages have different numbers of plural forms. Consult the [gettext manual](https://www.gnu.org/software/gettext/manual/html_node/Plural-forms.html) for language-specific plural rules.

## Building Translation Files

After editing PO files, you need to generate the JSON and JS files used by the app:

1. Install the required tools:
   ```bash
   npm install -g @nextcloud/l10n
   ```

2. Generate JSON files from PO files:
   ```bash
   l10n-builder -i translationfiles/<language_code>/iframewidget.po -o l10n/<language_code>.json
   ```

3. Generate JS files from JSON files:
   ```bash
   l10n-builder -i l10n/<language_code>.json -o l10n/<language_code>.js -t js
   ```

## Updating Translations

When new strings are added to the app:

1. Update the POT template:
   ```bash
   xgettext --from-code=UTF-8 --keyword=t --keyword=n:1,2 --keyword=pt:1c,2 --keyword=ptn:1c,2,3 -o translationfiles/templates/iframewidget.pot src/**/*.js src/**/*.vue
   ```

2. Update existing PO files:
   ```bash
   msgmerge --update translationfiles/<language_code>/iframewidget.po translationfiles/templates/iframewidget.pot
   ```

3. Rebuild the JSON and JS files as described above

## Testing Translations

To test your translations:

1. Place the updated PO, JSON and JS files in their respective directories
2. Rebuild the app:
   ```bash
   npm run build
   ```
3. Install the app in your Nextcloud instance or reload if already installed
4. Set your Nextcloud language to the one you've translated
5. Verify that all strings appear correctly in the interface

### Key Features to Test

When testing translations, pay special attention to these key areas:

1. **Admin Settings**: Check widget title, URL, icon, height, and extra-wide settings
2. **Personal Settings**: Check widget title, URL, icon, height, and extra-wide settings
3. **Dashboard Widgets**: Verify both public and personal widgets display correctly
4. **Error Messages**: Test the "No URL configured" message and other error states

Make sure the translations are consistent between similar fields in both the admin and personal settings.

## How Translations Work in Nextcloud Apps

Nextcloud apps use gettext-based translation system:

1. In JavaScript files, strings are marked for translation using:
   - `t('iframewidget', 'String to translate')` for simple translations
   - `n('iframewidget', 'Singular', 'Plural', count)` for plural forms

2. In Vue components, strings can be translated using:
   - `{{ t('iframewidget', 'String to translate') }}`
   - Or the `translate` mixin

3. The build process extracts these strings into the POT template
4. Translators create PO files for each language
5. The PO files are compiled into JSON/JS files that are loaded by the app at runtime

## Reporting Translation Issues

If you find issues with existing translations, please report them in the [GitHub issue tracker](https://github.com/IT-BAER/nc-iframewidget/issues) with the label "translation".

Thank you for helping make iFrame Widget accessible to more users in their native languages!
