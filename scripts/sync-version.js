#!/usr/bin/env node
/**
 * Version Sync Script
 * 
 * Synchronizes the version number from package.json to appinfo/info.xml
 * This ensures both files always have the same version.
 * 
 * Usage:
 *   node scripts/sync-version.js
 *   npm run version:sync
 * 
 * The script also supports syncing in the opposite direction:
 *   node scripts/sync-version.js --from-xml
 *   npm run version:sync:from-xml
 */

const fs = require('fs');
const path = require('path');

// File paths
const packageJsonPath = path.join(__dirname, '..', 'package.json');
const infoXmlPath = path.join(__dirname, '..', 'appinfo', 'info.xml');

/**
 * Read and parse package.json
 */
function readPackageJson() {
    const content = fs.readFileSync(packageJsonPath, 'utf8');
    return JSON.parse(content);
}

/**
 * Write package.json
 */
function writePackageJson(data) {
    const content = JSON.stringify(data, null, '\t');
    fs.writeFileSync(packageJsonPath, content + '\n', 'utf8');
}

/**
 * Read info.xml content
 */
function readInfoXml() {
    return fs.readFileSync(infoXmlPath, 'utf8');
}

/**
 * Write info.xml content
 */
function writeInfoXml(content) {
    fs.writeFileSync(infoXmlPath, content, 'utf8');
}

/**
 * Extract version from info.xml
 */
function getVersionFromXml(xmlContent) {
    const match = xmlContent.match(/<version>([^<]+)<\/version>/);
    return match ? match[1] : null;
}

/**
 * Update version in info.xml
 */
function updateVersionInXml(xmlContent, newVersion) {
    return xmlContent.replace(
        /<version>[^<]+<\/version>/,
        `<version>${newVersion}</version>`
    );
}

/**
 * Sync version from package.json to info.xml
 */
function syncFromPackageJson() {
    const packageJson = readPackageJson();
    const xmlContent = readInfoXml();
    const currentXmlVersion = getVersionFromXml(xmlContent);
    
    console.log(`📦 package.json version: ${packageJson.version}`);
    console.log(`📄 info.xml version: ${currentXmlVersion}`);
    
    if (packageJson.version === currentXmlVersion) {
        console.log('✅ Versions are already in sync!');
        return;
    }
    
    const updatedXml = updateVersionInXml(xmlContent, packageJson.version);
    writeInfoXml(updatedXml);
    
    console.log(`✅ Updated info.xml version: ${currentXmlVersion} → ${packageJson.version}`);
}

/**
 * Sync version from info.xml to package.json
 */
function syncFromInfoXml() {
    const packageJson = readPackageJson();
    const xmlContent = readInfoXml();
    const xmlVersion = getVersionFromXml(xmlContent);
    
    console.log(`📄 info.xml version: ${xmlVersion}`);
    console.log(`📦 package.json version: ${packageJson.version}`);
    
    if (packageJson.version === xmlVersion) {
        console.log('✅ Versions are already in sync!');
        return;
    }
    
    packageJson.version = xmlVersion;
    writePackageJson(packageJson);
    
    console.log(`✅ Updated package.json version: ${packageJson.version} → ${xmlVersion}`);
}

/**
 * Show current versions
 */
function showVersions() {
    const packageJson = readPackageJson();
    const xmlContent = readInfoXml();
    const xmlVersion = getVersionFromXml(xmlContent);
    
    console.log('📊 Current Version Status:');
    console.log(`   📦 package.json: ${packageJson.version}`);
    console.log(`   📄 info.xml: ${xmlVersion}`);
    
    if (packageJson.version === xmlVersion) {
        console.log('   ✅ Versions are in sync!');
    } else {
        console.log('   ⚠️  Versions are OUT OF SYNC!');
    }
}

/**
 * Bump version (major, minor, patch)
 */
function bumpVersion(type) {
    const packageJson = readPackageJson();
    const currentVersion = packageJson.version;
    const [major, minor, patch] = currentVersion.split('.').map(Number);
    
    let newVersion;
    switch (type) {
        case 'major':
            newVersion = `${major + 1}.0.0`;
            break;
        case 'minor':
            newVersion = `${major}.${minor + 1}.0`;
            break;
        case 'patch':
        default:
            newVersion = `${major}.${minor}.${patch + 1}`;
            break;
    }
    
    // Update package.json
    packageJson.version = newVersion;
    writePackageJson(packageJson);
    
    // Update info.xml
    const xmlContent = readInfoXml();
    const updatedXml = updateVersionInXml(xmlContent, newVersion);
    writeInfoXml(updatedXml);
    
    console.log(`🚀 Version bumped: ${currentVersion} → ${newVersion}`);
    console.log('   ✅ Updated package.json');
    console.log('   ✅ Updated info.xml');
}

// Parse command line arguments
const args = process.argv.slice(2);
const command = args[0];

switch (command) {
    case '--from-xml':
        syncFromInfoXml();
        break;
    case '--show':
    case '--status':
        showVersions();
        break;
    case '--bump-major':
        bumpVersion('major');
        break;
    case '--bump-minor':
        bumpVersion('minor');
        break;
    case '--bump-patch':
    case '--bump':
        bumpVersion('patch');
        break;
    case '--help':
        console.log(`
Version Sync Script for Nextcloud iFrame Widget

Usage:
  node scripts/sync-version.js [option]
  npm run version:[command]

Options:
  (no option)     Sync version from package.json to info.xml (default)
  --from-xml      Sync version from info.xml to package.json
  --show          Show current version status
  --bump-patch    Bump patch version (e.g., 0.8.7 → 0.8.8)
  --bump-minor    Bump minor version (e.g., 0.8.7 → 0.9.0)
  --bump-major    Bump major version (e.g., 0.8.7 → 1.0.0)
  --help          Show this help message

NPM Scripts:
  npm run version:sync          Sync from package.json to info.xml
  npm run version:sync:from-xml Sync from info.xml to package.json
  npm run version:show          Show current version status
  npm run version:bump          Bump patch version
  npm run version:bump:minor    Bump minor version
  npm run version:bump:major    Bump major version
`);
        break;
    default:
        syncFromPackageJson();
        break;
}
