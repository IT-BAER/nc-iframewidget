## 0.8.1 - 2025-09-22

### Bug Fixes

- Fixed deletion of old-style group widgets from legacy storage system
- Added support for deleting widgets with IDs ending in '_default' by cleaning up old config keys
- Fixed JavaScript error in checkIframeLoaded by adding null checks for state and iframeUrl
- Improved group widget modal to show default title and icon in dashboard selection
- Enhanced widget cleanup process to handle both JSON and legacy configuration formats

## 0.8.0 - 2025-09-21

### New Features

- Added group widget support with full feature parity to personal and admin widgets
- Group widgets now support custom icons, colors, and all display options

### Enhancements

- Removed iframe height settings to fix inconsistent widget heights across different content
- Added `height: 100%` to iframe elements for proper content fitting without scrollbars
- Removed explicit width properties to let Nextcloud handle widget width naturally
- Added `scrolling="no"` attribute to all iframe elements to prevent unwanted scrollbars
- Improved responsive behavior by allowing Nextcloud's dashboard to control widget dimensions
- Added mutation observer for dynamic dashboard panel updates

### Bug Fixes

- Fixed GroupIframeWidget.php getIconClass method to properly return default icons
- Corrected updateGroupColor method property name from iconColor to proper field
- Added missing clearGroupColor method for color picker functionality
- Fixed icon display issues in group widgets by implementing applyPanelClasses method
- Resolved state loading issues for group widget configurations

### Technical Improvements

- Simplified iframe CSS by removing height and width constraints
- Improved content display consistency across all iframe widget types (public, personal, group)

## 0.7.7 - 2025-09-13

### Security Fixes

- Update axios from 1.11.0 to 1.12.1 to fix CVE vulnerability
- Update babel-loader to ^10.0.0 and node-polyfill-webpack-plugin to 4.0.0 for compatibility
- Update package.json, info.xml, and Vue components to version 0.7.7
- Rebuild all JavaScript bundles with updated dependencies

## 0.7.6 - 2025-07-30

### Security Fixes

- Updated form-data dependency to version >=4.0.4
- Updated linkifyjs dependency to version >=4.3.2
- Updated pbkdf2 dependency to version >=3.1.3
- Updated brace-expansion dependency to version >=2.0.2
- Updated vue-template-compiler dependency to version >=3.0.0
- Updated postcss dependency to version >=8.4.31

### Technical Improvements

- Resolved dependency conflicts with webpack configuration
- Fixed compatibility issues with Nextcloud 30 and 31

## 0.7.5 - 2025-07-07

### Enhancements

- Added complete localization support for the 14 most common Nextcloud languages:
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
- Improved translation file organization and documentation
- Added translation guide for contributors

### Bug Fixes

- Added missing iframe height setting in Personal Widget settings
- Fixed preview height in Personal Widget settings to properly show iframe height changes

## 0.7.0 - 2025-07-07

### New Features

- Added personal widget support: Users can now configure their own iFrame widget !
- Admin Widget may now be used as "Public" Widget for all Users
- Added full localization support with translation capabilities

### Enhancements

- Improved widget rendering and styling
- Added translation infrastructure with sample German translation

## 0.6.3 - 2025-03-13

### Documentation

- Optimized Nextcloud App Store Description


### Bug Fixes 

- Fixed a bug which throws false-positive CSP errors


## 0.6.2 - 2025-03-13

### Bug Fixes

- Fixed CSP Check behavior


## 0.6.1 - 2025-03-13

### New Features

- Added user-friendly CSP (Content Security Policy) error handling
- Added helpful guidance when iframe content is blocked by CSP restrictions
- Added direct links to documentation for solving CSP-related issues
- Implemented automatic detection of CSP blocking via content access checks


### Security Enhancements

- Fixed multiple security vulnerabilities in dependencies
- Updated PostCSS to version 8.4.31 to address moderate severity vulnerabilities
- Implemented proper Node.js polyfills for webpack 5
- Enhanced Content Security Policy handling for iframe content


### Technical Improvements

- Fixed compatibility with Nextcloud 30 and added 31
- Fixed webpack configuration to properly handle module resolution
- Improved error handling and reporting throughout the application


### Documentation

- Added comprehensive error messages with links to documentation


### Bug Fixes

- Improved error state display and fallback mechanisms

This update represents a significant improvement in security, stability, and user experience for the iFrame Widget, focusing on providing better guidance when configuration issues arise.
