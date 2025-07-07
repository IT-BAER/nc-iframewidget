## 0.7.5 - 2025-08-15

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