// This script initializes the iFrame Widget admin settings interface
import { createApp } from 'vue'
import AdminSettings from './components/AdminSettings.vue'
import { generateFilePath } from '@nextcloud/router'
import '@nextcloud/dialogs/style.css'

// CSP config for webpack dynamic chunk loading
// eslint-disable-next-line
__webpack_nonce__ = btoa(OC?.requestToken || '')

// Correct the root of the app for chunk loading
// eslint-disable-next-line
__webpack_public_path__ = generateFilePath('iframewidget', '', 'js/')

// Initialize the Admin Settings when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    const app = createApp(AdminSettings)
    // Expose the Nextcloud globals that templates rely on
    app.config.globalProperties.t = t
    app.config.globalProperties.n = n
    app.config.globalProperties.OC = OC
    app.config.globalProperties.OCA = OCA
    app.mount('#iframewidget_prefs')
})
