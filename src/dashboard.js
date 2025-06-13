// This script initializes the iFrame Widget Dashboard interface
import Vue from 'vue'
import DashboardWidget from './components/DashboardWidget.vue'
import PersonalDashboardWidget from './components/PersonalDashboardWidget.vue'
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

// Register both widgets
document.addEventListener('DOMContentLoaded', () => {
    OCA.Dashboard.register('iframewidget', (el) => {
        const Widget = Vue.extend(DashboardWidget)
        return new Widget({}).$mount(el)
    })

    OCA.Dashboard.register('personal-iframewidget', (el) => {
        const Widget = Vue.extend(PersonalDashboardWidget)
        return new Widget({}).$mount(el)
    })
})

// Initialize the Dashboard Widget when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    OCA.Dashboard.register('iframewidget', (el) => {
        const View = Vue.extend(DashboardWidget)
        new View().$mount(el)
    })
})
