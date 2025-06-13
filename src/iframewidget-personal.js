import { generateFilePath } from '@nextcloud/router'
import Vue from 'vue'
import PersonalSettings from './components/PersonalSettings.vue'

// CSP config for webpack dynamic chunk loading
// eslint-disable-next-line
__webpack_nonce__ = btoa(OC.requestToken)

// Correct the root of the app for chunk loading
// OC.linkTo matches the apps folders
// eslint-disable-next-line
__webpack_public_path__ = generateFilePath('iframewidget', '', 'js/')

Vue.mixin({ methods: { t, n } })

export default new Vue({
    el: '#iframewidget-personal-settings',
    render: h => h(PersonalSettings),
})
