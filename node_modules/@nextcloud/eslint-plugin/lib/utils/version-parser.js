const { XMLParser } = require('fast-xml-parser')
const fs = require('node:fs')
const path = require('node:path')
const semver = require('semver')

/**
 * Check if a given path exists and is a directory
 *
 * @param {string} filePath The path
 * @return {boolean}
 */
const isDirectory = (filePath) => {
	const stats = fs.lstatSync(filePath, { throwIfNoEntry: false })
	return stats !== undefined && stats.isDirectory()
}

/**
 * Check if a given path exists and is a directory
 *
 * @param {string} filePath The path
 * @return {boolean}
 */
const isFile = (filePath) => {
	const stats = fs.lstatSync(filePath, { throwIfNoEntry: false })
	return stats !== undefined && stats.isFile()
}

/**
 * Find the path of nearest `appinfo/info.xml` relative to given path
 *
 * @param {string} currentPath
 * @return {string|undefined} Either the full path including the `info.xml` part or `undefined` if no found
 */
const findAppinfo = (currentPath) => {
	while (currentPath && currentPath !== path.sep) {
		const appinfoPath = `${currentPath}${path.sep}appinfo`
		if (isDirectory(appinfoPath) && isFile(`${appinfoPath}${path.sep}info.xml`)) {
			return `${appinfoPath}${path.sep}info.xml`
		}
		currentPath = path.resolve(currentPath, '..')
	}
	return undefined
}

/**
 * Make sure that versions like '25' can be handled by semver
 *
 * @param {string} version The pure version string
 * @return {string} Sanitized version string
 */
const sanitizeTargetVersion = (version) => {
	let sanitizedVersion = version
	const sections = sanitizedVersion.split('.').length
	if (sections < 3) {
		sanitizedVersion = sanitizedVersion + '.0'.repeat(3 - sections)
	}
	// now version should look like '25.0.0'
	if (!semver.valid(sanitizedVersion)) {
		throw Error(`[@nextcloud/eslint-plugin] Invalid target version ${version} found`)
	}
	return sanitizedVersion
}

function createVersionValidator({ cwd, physicalFilename, options }) {
	const settings = options[0]

	if (settings?.targetVersion) {
		// check if the rule version is lower than the current target version
		const maxVersion = sanitizeTargetVersion(settings.targetVersion)
		return (version) => semver.lte(version, maxVersion)
	}

	// Try to find appinfo and parse the supported version
	if (settings?.parseAppInfo !== false) {
		// Current working directory, either the filename (can be empty) or the cwd property
		const currentDirectory = path.isAbsolute(physicalFilename)
			? path.resolve(path.dirname(physicalFilename))
			: path.dirname(path.resolve(cwd, physicalFilename))

		// The nearest appinfo
		const appinfoPath = findAppinfo(currentDirectory)
		if (appinfoPath) {
			const parser = new XMLParser({
				attributeNamePrefix: '@',
				ignoreAttributes: false,
			})
			const xml = parser.parse(fs.readFileSync(appinfoPath))
			let maxVersion = xml?.info?.dependencies?.nextcloud?.['@max-version']
			if (typeof maxVersion !== 'string') {
				throw Error(`[@nextcloud/eslint-plugin] AppInfo does not contain a max-version (location: ${appinfoPath})`)
			}
			maxVersion = sanitizeTargetVersion(maxVersion)
			return (version) => semver.lte(version, maxVersion)
		}

		if (settings?.parseAppInfo === true) {
			// User enforced app info parsing, so we throw an error - otherwise just fallback to default
			throw Error('[@nextcloud/eslint-plugin] AppInfo parsing was enabled, but no `appinfo/info.xml` was found.')
		}
	}

	// If not configured or parsing is disabled, every rule should be handled
	return () => true
}

module.exports = {
	createVersionValidator,
	findAppinfo,
	isDirectory,
	isFile,
	sanitizeTargetVersion,
}
