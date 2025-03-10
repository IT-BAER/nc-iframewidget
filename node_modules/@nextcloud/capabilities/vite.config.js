import { createLibConfig } from '@nextcloud/vite-config'
import { join } from 'node:path'

export default createLibConfig({
	index: join(__dirname, 'lib/index.ts'),
}, {
	libraryFormats: ['cjs', 'es'],
})
