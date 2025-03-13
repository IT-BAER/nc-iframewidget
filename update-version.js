const fs = require('fs');
const path = require('path');

// Read version from our source of truth
const versionFile = path.join(__dirname, 'src', 'version.js');
const versionContent = fs.readFileSync(versionFile, 'utf8');
const versionMatch = versionContent.match(/appVersion\s*=\s*['"](.+)['"]/);
const version = versionMatch ? versionMatch[1] : null;

if (!version) {
    console.error('Could not find version in version.js');
    process.exit(1);
}

// Update package.json
const packagePath = path.join(__dirname, 'package.json');
const packageJson = require(packagePath);
packageJson.version = version;
fs.writeFileSync(packagePath, JSON.stringify(packageJson, null, '\t') + '\n');

// Update info.xml
const infoPath = path.join(__dirname, 'appinfo', 'info.xml');
let infoContent = fs.readFileSync(infoPath, 'utf8');
infoContent = infoContent.replace(/<version>([^<]+)<\/version>/, `<version>${version}</version>`);
fs.writeFileSync(infoPath, infoContent);

console.log(`Updated version to ${version} in package.json and info.xml`);
