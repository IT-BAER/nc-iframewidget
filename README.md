# 🖼️ iFrame Widget for Nextcloud

<div align="center">

![Nextcloud App Store Version](https://img.shields.io/badge/Nextcloud-30%2B-blue?logo=nextcloud&logoColor=white)
![License](https://img.shields.io/github/license/it-baer/nc-iframewidget?color=blue)
![GitHub stars](https://img.shields.io/github/stars/it-baer/nc-iframewidget?style=social)

**Display external websites directly in your Nextcloud dashboard with this customizable widget**

<img src="https://github.com/user-attachments/assets/86405272-4543-4f3d-b861-30c49ea0d414"/>

</div>

## ✨ Features

- 🌐 Embed any website in your Nextcloud dashboard
- 🔤 Customizable widget title
- 🎨 Support for custom icons using [Simple Icons](https://simpleicons.org/) with the `si:` prefix
- 🎭 Custom icon coloring
- 📏 Adjustable iframe height
- 🖥️ Extra-wide display option (2 columns)
- 🎯 Clean, responsive design that integrates with Nextcloud themes
- 👤 Separate Personal and Public iFrame Widgets
- 👥 **NEW:** Group-based iFrame Widgets (admin-configured, group-specific)
- 🌍 Multi-language support with translations


## 🚀 TODO

- 3-Column Size
- Refresh Button (or Refresh Timer Option)
- Add missing Translations
- Enhanced Widget Management (Drag&Drop, modernized UI, Categories)

## 📸 Screenshots

<div align="center">

🖥️ Widget in Dashboard

<br>

<img width="1318" height="680" alt="image" src="https://github.com/user-attachments/assets/471d5bb0-9478-41ec-93f1-a6e70aaea351" />

The iFrame Widget provides three types of widgets: admin/public, personal, and group-based configuration options, allowing administrators to set up shared widgets for all users, enable individual users to create their own personalized widgets, and create group-specific widgets that are only visible to members of selected user groups.

<br>

⚙️ Admin Settings
<br>

<img src="nc-iframewidget-settings.png"/>
</div>



## ⚙️ Configuration

### 👥 Admin/Public Widget Settings

Access the administrator settings from:

1. Settings → Administration → iFrame Widget
2. Configure the following options:
   - **🔤 Widget Title**: Set a custom title (or leave empty to hide the header)
   - **🎨 Widget Icon**: Enter an icon name with `si:` prefix (e.g., `si:github`)
   - **🎭 Icon Color**: Choose a custom color for the icon
   - **🌐 URL to Display**: The website URL to embed
   - **📏 iFrame Height**: Set a fixed height or use 100% (default)
   - **🖥️ Extra Wide**: Toggle to span two dashboard columns

These settings apply to the public widget that appears on all users' dashboards.

### 👤 Personal Widget Settings

Each user can configure their own personal iFrame widget:

1. Settings → Personal → iFrame Widget
2. Configure the following options:
   - **🔤 Widget Title**: Set a custom title for your personal widget
   - **🎨 Widget Icon**: Choose your preferred icon with `si:` prefix
   - **🎭 Icon Color**: Set a custom color for your widget icon
   - **🌐 URL to Display**: Add your personal website URL to embed
   - **📏 iFrame Height**: Adjust the height to fit your needs
   - **🖥️ Extra Wide**: Enable for a wider widget view

Personal widgets are visible only to the user who configured them and don't affect other users.

### 👥 Group-based Widget Settings

Administrators can create iFrame widgets that are only visible to specific user groups:

1. Settings → Administration → iFrame Widget
2. Scroll down to the "Group-based iFrame Widgets" section
3. Click "Add Group Widget" to create a new group-specific widget
4. Configure the following options:
   - **👥 Select Group**: Choose which user group should see this widget
   - **🔤 Widget Title**: Set a custom title for the group widget
   - **🎨 Widget Icon**: Choose an icon with `si:` prefix
   - **🎭 Icon Color**: Set a custom color for the widget icon
   - **🌐 URL to Display**: Add the website URL to embed for this group
   - **📏 iFrame Height**: Adjust the height for the group widget
   - **🖥️ Extra Wide**: Enable for a wider widget view

Group widgets are visible only to users who are members of the selected group. You can create multiple group widgets for different groups, and users can be members of multiple groups to see multiple group widgets.

## ⚠️ Current Limitations

**Important Notes on Group Widget Functionality:**

- **🔸 Single Widget Per Group**: Currently, only **one widget can be shown per group** at a time. If multiple widgets are configured for the same group, only the one marked as "shown" will be visible to group members.
- **👤 Single Widget Per User**: Each user can only see **one group widget** at a time, even if they belong to multiple groups. The system prioritizes the first group widget they are eligible for.
- **🔄 No Multi-Group Support**: Users cannot see multiple group widgets simultaneously - only one group widget is displayed per user.

These limitations are planned to be addressed in future updates. See the TODO List.

## 🎨 Icon System

This widget uses Simple Icons for custom icons:

`si:iconname`

For example:

- `si:github` - <img src="https://cdn.simpleicons.org/github/grey" width="16" height="16" style="vertical-align: middle"> GitHub icon
- `si:youtube` - <img src="https://cdn.simpleicons.org/youtube" width="16" height="16" style="vertical-align: middle"> YouTube icon
- `si:nextcloud` - <img src="https://cdn.simpleicons.org/nextcloud" width="16" height="16" style="vertical-align: middle"> Nextcloud icon

Browse available icons at [SimpleIcons.org](https://simpleicons.org/).

## 📋 Requirements

- 📦 Nextcloud 30+
- 🌐 Website to be embedded must allow iframe embedding (not all sites do)
- 🔒 Content Security Policy (CSP) configuration to allow external domains in iframes


### 🌍 Translations

iFrame Widget supports multiple languages and can be translated. Currently, the following languages are available:

- 🇬🇧 English (en)
- 🇩🇪 German (de)
- 🇫🇷 French (fr)
- 🇪🇸 Spanish (es)
- 🇮🇹 Italian (it)
- 🇳🇱 Dutch (nl)
- 🇷🇺 Russian (ru)
- 🇵🇱 Polish (pl)
- 🇵🇹 Portuguese (pt)
- 🇧🇷 Brazilian Portuguese (pt_BR)
- 🇨🇳 Chinese (Simplified) (zh_CN)
- 🇯🇵 Japanese (ja)
- 🇨🇿 Czech (cs)
- 🇸🇪 Swedish (sv)
- 🇳🇴 Norwegian Bokmål (nb)

If you'd like to contribute translations, please see the [translation guide](docs/TRANSLATING.md).


### 🔒 CSP Configuration

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


### ℹ️ Note on External Websites

Some websites explicitly block being embedded in iframes using their own CSP headers (`X-Frame-Options: DENY` or `frame-ancestors: 'none'`). These sites cannot be embedded even if you configure your server correctly. In these cases, consider using the External Sites app with the redirect option instead.

## 🔐 Security Notes

- 🛡️ Websites embedded through iframes operate within their own security context
- 🚫 Some websites block embedding using X-Frame-Options headers
- ✅ Use trusted sources for embedded content

## ❓ FAQ

### 🔍 Personal widget settings cannot be saved after upgrading to v0.7.0

If you experience issues with saving personal widget settings after upgrading to v0.7.0, try the following solutions:

1. **🗑️ Clear the Nextcloud cache**:
   ```bash
   php occ maintenance:mode --on
   php occ memcache:clear
   php occ maintenance:mode --off
   ```

2. **🔄 Restart your web server**:
   For Apache:
   ```bash
   sudo systemctl restart apache2
   ```
   For Nginx:
   ```bash
   sudo systemctl restart nginx
   sudo systemctl restart php-fpm
   ```

3. **♻️ Disable and re-enable the app**:
   ```bash
   php occ app:disable iframewidget
   php occ app:enable iframewidget
   ```

4. **🔍 Check your browser console for JavaScript errors**:
   If you see CSRF token errors, clearing your browser cache might help.

### 🔍 Widget doesn't appear on the dashboard

If the widget doesn't appear on your dashboard after installation:

1. Make sure you've added it from the dashboard customization screen (+ button)
2. Verify that there are no JavaScript errors in your browser console
3. Check that the app is properly enabled: `php occ app:list | grep iframe`

### ❓ What's the difference between admin, personal, and group widgets?

- **Admin/Public Widget**: Configured by administrators and appears on all users' dashboards with the same content
- **Personal Widget**: Each user can configure their own widget that's visible only to them
- **Group Widget**: Configured by administrators and appears only on dashboards of users who are members of the selected group
- You can use all three simultaneously - users can have the admin-configured widget, their own personal widget, and any group widgets they're eligible for

All widgets need to be added to your dashboard using the "+" button in dashboard customization.

## 💜 Support Development

If you find this app useful, consider supporting this and future developments, which heavily relies on coffee:

<div align="center">
<a href="https://www.buymeacoffee.com/itbaer" target="_blank"><img src="https://github.com/user-attachments/assets/64107f03-ba5b-473e-b8ad-f3696fe06002" alt="Buy Me A Coffee" style="height: 60px !important;max-width: 217px !important;" ></a>
</div>

## 📄 License

This project is licensed under the [AGPL-3.0-or-later](LICENSE) license.

## 👏 Credits

- [Simple Icons](https://simpleicons.org/) - Used for widget icons

