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