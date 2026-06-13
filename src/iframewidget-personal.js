import { generateFilePath } from '@nextcloud/router'
import { createApp } from 'vue'
import PersonalSettings from './components/PersonalSettings.vue'

// CSP config for webpack dynamic chunk loading
// eslint-disable-next-line
__webpack_nonce__ = btoa(OC.requestToken)

// Correct the root of the app for chunk loading
// OC.linkTo matches the apps folders
// eslint-disable-next-line
__webpack_public_path__ = generateFilePath('iframewidget', '', 'js/')

const app = createApp(PersonalSettings)
// Expose the Nextcloud globals that templates rely on
app.config.globalProperties.t = t
app.config.globalProperties.n = n
app.config.globalProperties.OC = OC
app.config.globalProperties.OCA = OCA
app.mount('#iframewidget-personal-settings')
