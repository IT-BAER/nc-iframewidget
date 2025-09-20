// This script initializes the iFrame Widget Dashboard interface
import Vue from 'vue'
import DashboardWidget from './components/DashboardWidget.vue'
import PersonalDashboardWidget from './components/PersonalDashboardWidget.vue'
import GroupDashboardWidget from './components/GroupDashboardWidget.vue'
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

// Register all widgets when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Register admin/public widget
    OCA.Dashboard.register('iframewidget', (el) => {
        const Widget = Vue.extend(DashboardWidget)
        return new Widget({}).$mount(el)
    })

    // Register personal widget
    OCA.Dashboard.register('personal-iframewidget', (el) => {
        const Widget = Vue.extend(PersonalDashboardWidget)
        return new Widget({}).$mount(el)
    })

    // Register group widget
    OCA.Dashboard.register('group-iframewidget', (el) => {
        const Widget = Vue.extend(GroupDashboardWidget)
        return new Widget({}).$mount(el)
    })
})
