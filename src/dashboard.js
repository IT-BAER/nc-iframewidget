// This script initializes the iFrame Widget Dashboard interface
import Vue from 'vue'
import DashboardWidget from './components/DashboardWidget.vue'
import PersonalDashboardWidget from './components/PersonalDashboardWidget.vue'
import GroupDashboardWidget from './components/GroupDashboardWidget.vue'
import axios from '@nextcloud/axios'
import { generateFilePath, generateOcsUrl } from '@nextcloud/router'

// CSP config for webpack dynamic chunk loading
// eslint-disable-next-line
__webpack_nonce__ = btoa(OC?.requestToken || '')

// Correct the root of the app for chunk loading
// eslint-disable-next-line
__webpack_public_path__ = generateFilePath('iframewidget', '', 'js/')

// Add global properties to Vue
Vue.prototype.t = t
Vue.prototype.n = n
Vue.prototype.OC = OC
Vue.prototype.OCA = OCA

const PERSONAL_WIDGET_ID = 'personal-iframewidget'

const getAdvertisedDashboardWidgetIds = async () => {
    try {
        const url = generateOcsUrl('/apps/dashboard/api/v1/widgets')
        const response = await axios.get(url, {
            params: {
                format: 'json',
            },
            headers: {
                'OCS-APIRequest': 'true',
            },
        })

        const widgets = response?.data?.ocs?.data
        if (Array.isArray(widgets)) {
            return new Set(
                widgets
                    .map((widget) => widget?.id)
                    .filter((id) => typeof id === 'string' && id.length > 0),
            )
        }

        // Some Nextcloud versions return an object keyed by widget id.
        if (widgets && typeof widgets === 'object') {
            return new Set(
                Object.keys(widgets).filter((id) => typeof id === 'string' && id.length > 0),
            )
        }

        return null
    } catch (error) {
        // If we can't determine the server-side widget list, it's safer to not register
        // anything than to potentially crash the dashboard by registering unknown IDs.
        console.warn('[iframewidget] Failed to load dashboard widget list, skipping registration', error)
        return null
    }
}

const safeRegister = (widgetId, callback) => {
    try {
        OCA?.Dashboard?.register?.(widgetId, callback)
    } catch (error) {
        console.error(`[iframewidget] Failed to register widget ${widgetId}`, error)
    }
}

const registerWidgets = async () => {
    if (!OCA?.Dashboard?.register) {
        return
    }

    const advertised = await getAdvertisedDashboardWidgetIds()
    if (advertised === null) {
        return
    }

    // Register public widget slots (1-5)
    for (let slot = 1; slot <= 5; slot++) {
        const widgetId = slot === 1 ? 'iframewidget' : `iframewidget-${slot}`
        if (!advertised.has(widgetId)) {
            continue
        }

        safeRegister(widgetId, (el) => {
            const Widget = Vue.extend(DashboardWidget)
            return new Widget({
                propsData: { slotNumber: slot },
            }).$mount(el)
        })
    }

    // Register personal widget
    if (advertised.has(PERSONAL_WIDGET_ID)) {
        safeRegister(PERSONAL_WIDGET_ID, (el) => {
            const Widget = Vue.extend(PersonalDashboardWidget)
            return new Widget({}).$mount(el)
        })
    }

    // Register group widget slots (1-5)
    for (let slot = 1; slot <= 5; slot++) {
        const widgetId = slot === 1 ? 'group-iframewidget' : `group-iframewidget-${slot}`
        if (!advertised.has(widgetId)) {
            continue
        }

        safeRegister(widgetId, (el) => {
            const Widget = Vue.extend(GroupDashboardWidget)
            return new Widget({
                propsData: { slotNumber: slot },
            }).$mount(el)
        })
    }
}

// Nextcloud may lazy-load widget bundles after DOMContentLoaded; register immediately if so.
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        registerWidgets().catch((error) => console.error('[iframewidget] Widget registration failed', error))
    })
} else {
    registerWidgets().catch((error) => console.error('[iframewidget] Widget registration failed', error))
}

