# Changelog

All notable changes to the iFrame Widget for Nextcloud will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.9.1] - 2026-01-21

### Fixed

- **fetchConfig overwrite bug**: Fixed issue where `fetchConfig()` was overwriting valid slot-based widget configurations loaded from initial state, causing public and group widgets to fail loading their iframe URLs

## [0.9.0] - 2026-01-19

### Added

- **Multi-widget slot system**: Support for up to 5 public widgets and 5 group widgets per group
- **Enable/Disable toggles**: Individual widgets can be enabled or disabled without deleting them
- **Iframe security settings**: New `sandbox` and `allow` fields for granular iframe permission control
  - `iframeSandbox`: Configure sandbox permissions (default: `allow-same-origin allow-scripts allow-popups allow-forms`)
  - `iframeAllow`: Configure feature policy permissions (e.g., `microphone; camera; geolocation`)
- **Slot-based architecture**: Each widget type now uses dedicated slot classes for better isolation
  - `PublicWidgetSlot1-5.php` for public widgets
  - `GroupWidgetSlot1-5.php` for group widgets
- **Version sync tooling**: Automatic version synchronization between `package.json` and `appinfo/info.xml`
  - `npm run version:sync` - Sync from package.json to info.xml
  - `npm run version:bump` - Bump patch version
  - `npm run version:bump:minor` - Bump minor version
  - `npm run version:bump:major` - Bump major version
  - `npm run version:show` - Display current version status
- **Dynamic version display**: Version shown in admin/personal settings now dynamically reads from package.json at build time
- **Portuguese (pt) translations**: Added complete Portuguese localization
- **URL validation**: All widget URLs are now validated to start with `http://` or `https://`
- **Public widget management UI**: New admin interface for managing multiple public widgets with slot badges

### Changed

- **Widget registration**: Replaced single widget classes with slot-based system in `Application.php`
- **Admin settings UI**: Complete redesign with public widgets list, slot badges, and enable/disable toggles
- **Data storage**: Migrated from individual config keys to JSON-based storage (`publicWidgetsJson`, `groupWidgetsJson`)
- **API endpoints**: Updated `/public-widgets` and `/group-widgets` endpoints for multi-widget support
- **DashboardWidget component**: Now slot-aware with `slotNumber` prop for proper state management
- **Backward compatibility**: First slot (slot 1) maintains original widget IDs for seamless upgrades

### Fixed

- **Proper widget isolation**: Each widget slot now has independent configuration and state
- **Config controller**: Fixed request parameter parsing using `$this->request->getParams()` instead of `php://input`
- **User session handling**: Improved `IUserSession` injection in controllers
- **Slot assignment**: Automatic slot number assignment when creating new widgets

### Security

- **URL validation**: Added server-side validation rejecting non-HTTP(S) URLs
- **Sandbox defaults**: All iframes now have secure default sandbox permissions

### Dependencies

- Updated `webpack` from 5.98.0 to 5.104.0
- Updated `@nextcloud/vue` from 8.23.1 to 8.35.2
- Updated `@nextcloud/dialogs` from 5.3.6 to 5.3.8
- Updated `@nextcloud/axios` to 2.5.2
- Updated `@nextcloud/router` to 3.1.0
- Updated various Nextcloud internal packages

### Removed

- **Legacy widget classes**: `IframeWidget.php` and `GroupIframeWidget.php` replaced by slot system
- **`isDefault` field**: Replaced by `enabled` field for clearer semantics
- **Single-widget paradigm**: Old single-widget admin interface replaced by multi-widget management

### Migration Notes

- Existing single-widget configurations are automatically migrated to slot 1
- Legacy config keys are preserved for backward compatibility with slot 1
- No manual migration required - the app handles upgrades transparently

---

## [0.8.7] - 2025-XX-XX

### Changed

- Version synchronization between package.json and info.xml

---

## [0.8.6] - 2025-10-21

### Security

- Fixed node-forge vulnerability (CVE-2025-12816)

### Changed

- Bumped version to 0.8.6
 
---

## [0.8.5] - 2025-10-20

### Added

- Native compatibility with Nextcloud 32 via Content Security Policy manager service injection

### Changed

- Refined admin UI metadata with latest release info and updated compatibility
- Bumped application metadata and build artifacts to version 0.8.5
- Regenerated production bundles for admin and personal settings panels

---

## [0.8.4] - 2025-10-02

### Fixed

- Hidden headers for empty iframe and personal iframe widgets
- Updated GroupIframeWidget to return static title 'Group iFrame' for widget picker
- Modified IframeWidget to return 'Public iFrame' as title for widget picker
- Changed PersonalIframeWidget to return 'Personal iFrame' for widget picker
- Adjusted default configuration in PersonalDashboardWidget.vue to have empty widgetTitle
- Widget title retrieval for user-specific configurations

---

## [0.8.3] - 2025-10-01

### Fixed

- Localization crashes: Updated outdated translation keys in pt_BR, sv, ja, and nb localization files

---

## [0.8.2] - 2025-09-25

### Fixed

- Iframe scrolling: Enabled scrolling functionality while hiding scrollbars for cleaner appearance

---

## [0.8.0] - 2025-09-21

### Added

- Group widget support with full feature parity to personal and admin widgets
- Custom icons and colors support for group widgets
- Mutation observer for dynamic dashboard panel updates

### Changed

- Removed iframe height settings to fix inconsistent widget heights
- Added `height: 100%` to iframe elements for proper content fitting
- Removed explicit width properties to let Nextcloud handle widget width naturally
- Added `scrolling="no"` attribute to all iframe elements
- Improved responsive behavior by allowing Nextcloud's dashboard to control widget dimensions

### Fixed

- GroupIframeWidget.php getIconClass method to properly return default icons
- updateGroupColor method property name from iconColor to proper field
- Added missing clearGroupColor method for color picker functionality
- Icon display issues in group widgets via applyPanelClasses method
- State loading issues for group widget configurations

---

## [0.7.7] - 2025-09-13

### Security

- Updated axios from 1.11.0 to 1.12.1 to fix CVE vulnerability

### Changed

- Updated babel-loader to ^10.0.0 and node-polyfill-webpack-plugin to 4.0.0
- Rebuilt all JavaScript bundles with updated dependencies

---

## [0.7.6] - 2025-07-30

### Security

- Updated form-data dependency to version >=4.0.4
- Updated linkifyjs dependency to version >=4.3.2
- Updated pbkdf2 dependency to version >=3.1.3
- Updated brace-expansion dependency to version >=2.0.2
- Updated vue-template-compiler dependency to version >=3.0.0
- Updated postcss dependency to version >=8.4.31

### Fixed

- Resolved dependency conflicts with webpack configuration
- Fixed compatibility issues with Nextcloud 30 and 31

---

## [0.7.5] - 2025-07-07

### Added

- Complete localization support for 14 most common Nextcloud languages:
  - English (en), German (de), French (fr), Spanish (es), Italian (it)
  - Dutch (nl), Russian (ru), Polish (pl), Portuguese (pt), Brazilian Portuguese (pt_BR)
  - Chinese Simplified (zh_CN), Japanese (ja), Czech (cs), Swedish (sv), Norwegian Bokmål (nb)
- Translation guide for contributors

### Fixed

- Missing iframe height setting in Personal Widget settings
- Preview height in Personal Widget settings to properly show iframe height changes

---

## [0.7.0] - 2025-07-07

### Added

- Personal widget support: Users can now configure their own iFrame widget
- Admin Widget may now be used as "Public" Widget for all users
- Full localization support with translation capabilities
- Translation infrastructure with sample German translation

### Changed

- Improved widget rendering and styling

---

## [0.6.3] - 2025-03-13

### Changed

- Optimized Nextcloud App Store Description

### Fixed

- Bug which throws false-positive CSP errors

---

## [0.6.2] - 2025-03-13

### Fixed

- CSP Check behavior

---

## [0.6.1] - 2025-03-13

### Added

- User-friendly CSP (Content Security Policy) error handling
- Helpful guidance when iframe content is blocked by CSP restrictions
- Direct links to documentation for solving CSP-related issues
- Automatic detection of CSP blocking via content access checks

### Security

- Fixed multiple security vulnerabilities in dependencies
- Updated PostCSS to version 8.4.31 to address moderate severity vulnerabilities
- Implemented proper Node.js polyfills for webpack 5
- Enhanced Content Security Policy handling for iframe content

### Changed

- Improved error handling and reporting throughout the application
- Added comprehensive error messages with links to documentation

### Fixed

- Compatibility with Nextcloud 30 and added 31
- Webpack configuration to properly handle module resolution
- Error state display and fallback mechanisms

---

[Unreleased]: https://github.com/IT-BAER/nc-iframewidget/compare/v0.9.1...HEAD
[0.9.1]: https://github.com/IT-BAER/nc-iframewidget/compare/v0.9.0...v0.9.1
[0.9.0]: https://github.com/IT-BAER/nc-iframewidget/compare/v0.8.7...v0.9.0
[0.8.7]: https://github.com/IT-BAER/nc-iframewidget/compare/v0.8.6...v0.8.7
[0.8.6]: https://github.com/IT-BAER/nc-iframewidget/compare/v0.8.5...v0.8.6
[0.8.5]: https://github.com/IT-BAER/nc-iframewidget/compare/v0.8.4...v0.8.5
[0.8.4]: https://github.com/IT-BAER/nc-iframewidget/compare/v0.8.3...v0.8.4
[0.8.3]: https://github.com/IT-BAER/nc-iframewidget/compare/v0.8.2...v0.8.3
[0.8.2]: https://github.com/IT-BAER/nc-iframewidget/compare/v0.8.0...v0.8.2
[0.8.0]: https://github.com/IT-BAER/nc-iframewidget/compare/v0.7.7...v0.8.0
[0.7.7]: https://github.com/IT-BAER/nc-iframewidget/compare/v0.7.6...v0.7.7
[0.7.6]: https://github.com/IT-BAER/nc-iframewidget/compare/v0.7.5...v0.7.6
[0.7.5]: https://github.com/IT-BAER/nc-iframewidget/compare/v0.7.0...v0.7.5
[0.7.0]: https://github.com/IT-BAER/nc-iframewidget/compare/v0.6.3...v0.7.0
[0.6.3]: https://github.com/IT-BAER/nc-iframewidget/compare/v0.6.2...v0.6.3
[0.6.2]: https://github.com/IT-BAER/nc-iframewidget/compare/v0.6.1...v0.6.2
[0.6.1]: https://github.com/IT-BAER/nc-iframewidget/releases/tag/v0.6.1
