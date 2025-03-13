// This script initializes the iFrame Widget admin settings interface
import Vue from 'vue'
import AdminSettings from './components/AdminSettings.vue'
import { generateFilePath } from '@nextcloud/router'
import '@nextcloud/dialogs/style.css'
import { getRequestToken } from '@nextcloud/auth'

// CSP config for webpack dynamic chunk loading
// eslint-disable-next-line
__webpack_nonce__ = btoa(getRequestToken())

// Correct the root of the app for chunk loading
// eslint-disable-next-line
__webpack_public_path__ = generateFilePath('iframewidget', '', 'js/')

// Add global properties to Vue
Vue.prototype.t = t
Vue.prototype.n = n
Vue.prototype.OC = OC
Vue.prototype.OCA = OCA

// Initialize the Admin Settings when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    const View = Vue.extend(AdminSettings)
    new View().$mount('#iframewidget_prefs')
})
