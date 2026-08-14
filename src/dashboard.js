// This script initializes the iFrame Widget Dashboard interface
import { createApp } from 'vue'
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

// Create a Vue 3 app for a widget component and expose the Nextcloud globals
// (t, n, OC, OCA) that templates rely on. Each dashboard slot mounts its own app.
const mountWidget = (Component, props, el) => {
    // The dashboard mount element is unstyled; without a definite height the
    // widget's height:100% chain collapses to the browser's default iframe size.
    el.style.height = '100%'

    const app = createApp(Component, props ?? {})
    app.config.globalProperties.t = t
    app.config.globalProperties.n = n
    app.config.globalProperties.OC = OC
    app.config.globalProperties.OCA = OCA
    return app.mount(el)
}

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

        safeRegister(widgetId, (el) => mountWidget(DashboardWidget, { slotNumber: slot }, el))
    }

    // Register personal widget
    if (advertised.has(PERSONAL_WIDGET_ID)) {
        safeRegister(PERSONAL_WIDGET_ID, (el) => mountWidget(PersonalDashboardWidget, {}, el))
    }

    // Register group widget slots (1-5)
    for (let slot = 1; slot <= 5; slot++) {
        const widgetId = slot === 1 ? 'group-iframewidget' : `group-iframewidget-${slot}`
        if (!advertised.has(widgetId)) {
            continue
        }

        safeRegister(widgetId, (el) => mountWidget(GroupDashboardWidget, { slotNumber: slot }, el))
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

