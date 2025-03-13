// This script initializes the iFrame Widget Dashboard interface
import Vue from 'vue'
import DashboardWidget from './components/DashboardWidget.vue'
import { generateFilePath } from '@nextcloud/router'
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

// Initialize the Dashboard Widget when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    OCA.Dashboard.register('iframewidget', (el) => {
        const View = Vue.extend(DashboardWidget)
        new View().$mount(el)
    })
})
