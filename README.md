# iFrame Widget for Nextcloud

Display external websites directly in your Nextcloud dashboard with this customizable widget.<br><br><br>

<p align="center"><img src="https://github.com/user-attachments/assets/86405272-4543-4f3d-b861-30c49ea0d414"/></p>
<p align="center">In this Example i added my Glance Dashboard as an iFrame</p>

## Features

- Embed any website in your Nextcloud dashboard
- Customizable widget title
- Support for custom icons using [Simple Icons](https://simpleicons.org/) with the `si:` prefix
- Custom icon coloring
- Adjustable iframe height
- Extra-wide display option (2 columns)
- Clean, responsive design that integrates with Nextcloud themes
- Seperate Personal and Public iFrame Widgets
- Multi-language support with translations


### TODO

- 3-Column Size
- Refresh Button (or Refresh Timer Option)


## Screenshots

| Widget in Dashboard | Admin Settings |
| :--: | :--: |
| <img src="nc-iframewidget-dashboard.png"/> | <img src="nc-iframewidget-settings.png"/> |


## Configuration

Access the widget settings from:

1. Settings → Administration → iFrame Widget
2. Configure the following options:
    - **Widget Title**: Set a custom title (or leave empty to hide the header)
    - **Widget Icon**: Enter an icon name with `si:` prefix (e.g., `si:github`)
    - **Icon Color**: Choose a custom color for the icon
    - **URL to Display**: The website URL to embed
    - **iFrame Height**: Set a fixed height or use 100% (default)
    - **Extra Wide**: Toggle to span two dashboard columns

## Icon System

This widget uses Simple Icons for custom icons:

`si:iconname`

For example:

- `si:github` - GitHub icon
- `si:youtube` - YouTube icon
- `si:nextcloud` - Nextcloud icon

Browse available icons at [SimpleIcons.org](https://simpleicons.org/).

## Requirements

- Nextcloud 30+
- Website to be embedded must allow iframe embedding (not all sites do)
- Content Security Policy (CSP) configuration to allow external domains in iframes


### Translations

iFrame Widget supports multiple languages and can be translated. Currently, the following languages are available:

  - English (en)
  - German (de)
  - French (fr)
  - Spanish (es)
  - Italian (it)
  - Dutch (nl)
  - Russian (ru)
  - Polish (pl)
  - Portuguese (pt)
  - Brazilian Portuguese (pt_BR)
  - Chinese (Simplified) (zh_CN)
  - Japanese (ja)
  - Czech (cs)
  - Swedish (sv)
  - Norwegian Bokmål (nb)

If you'd like to contribute translations, please see the [translation guide](docs/TRANSLATING.md).


### CSP Configuration

By default, Nextcloud restricts which websites can be embedded in iframes for security reasons. To embed external websites in your dashboard widget, you'll need to add them to your server's Content Security Policy configuration:

#### For Apache

Add the following to your Apache configuration or .htaccess file:

```apache
# Allow specific domain in iframes
Header set Content-Security-Policy "frame-src 'self' https://example.com;"

# If you need to allow multiple domains
Header set Content-Security-Policy "frame-src 'self' https://example.com https://another-site.org;"
```


#### For Nginx

Add the following to your Nginx server block:

```nginx
# Allow specific domain in iframes
add_header Content-Security-Policy "frame-src 'self' https://example.com;";

# If you need to allow multiple domains
add_header Content-Security-Policy "frame-src 'self' https://example.com https://another-site.org;";
```


### Note on External Websites

Some websites explicitly block being embedded in iframes using their own CSP headers (`X-Frame-Options: DENY` or `frame-ancestors: 'none'`). These sites cannot be embedded even if you configure your server correctly. In these cases, consider using the External Sites app with the redirect option instead.

## Security Notes

- Websites embedded through iframes operate within their own security context
- Some websites block embedding using X-Frame-Options headers
- Use trusted sources for embedded content

## FAQ

### Personal widget settings cannot be saved after upgrading to v0.7.5

If you experience issues with saving personal widget settings after upgrading to v0.7.5, try the following solutions:

1. **Clear the Nextcloud cache**:
   ```bash
   php occ maintenance:mode --on
   php occ memcache:clear
   php occ maintenance:mode --off
   ```

2. **Restart your web server**:
   For Apache:
   ```bash
   sudo systemctl restart apache2
   ```
   For Nginx:
   ```bash
   sudo systemctl restart nginx
   sudo systemctl restart php-fpm
   ```

3. **Disable and re-enable the app**:
   ```bash
   php occ app:disable iframewidget
   php occ app:enable iframewidget
   ```

4. **Check your browser console for JavaScript errors**:
   If you see CSRF token errors, clearing your browser cache might help.

### Widget doesn't appear on the dashboard

If the widget doesn't appear on your dashboard after installation:

1. Make sure you've added it from the dashboard customization screen (+ button)
2. Verify that there are no JavaScript errors in your browser console
3. Check that the app is properly enabled: `php occ app:list | grep iframe`

## Support Development

If you find this app useful, consider supporting this and future developments, which heavily relies on coffee:

<a href="https://www.buymeacoffee.com/itbaer" target="_blank"><img src="https://github.com/user-attachments/assets/64107f03-ba5b-473e-b8ad-f3696fe06002" alt="Buy Me A Coffee" style="height: 60px !important;max-width: 217px !important;" ></a>


## License

This project is licensed under the [AGPL-3.0-or-later](LICENSE) license.

## Credits

- [Simple Icons](https://simpleicons.org/) - Used for widget icons

