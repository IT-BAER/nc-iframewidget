<template>
    <div id="iframewidget-personal-settings" class="section">
        <h2>{{ t('iframewidget', 'Personal iFrame Widget Settings') }}</h2>
        
        <p class="settings-hint">
            {{ t('iframewidget', 'Configure your personal iFrame Widget for the Dashboard.') }}
        </p>
        
        <div class="iframewidget-settings-form">
            <div class="iframewidget-grid-form">
                <!-- Widget Title -->
                <label for="personal-iframe-widget-title">
                    {{ t('iframewidget', 'Widget Title') }}
                </label>
                <input id="personal-iframe-widget-title"
                    v-model="state.widgetTitle"
                    type="text"
                    :placeholder="t('iframewidget', 'Personal iFrame')">
                
                <!-- Widget Icon -->
                <label for="personal-widget-icon">
                    {{ t('iframewidget', 'Widget Icon') }}
                    <span class="icon-finder">
                        - {{ t('iframewidget', 'Find icons at') }} 
                        <a href="https://simpleicons.org/" 
                           target="_blank" 
                           rel="noopener noreferrer">
                           simpleicons.org
                        </a>
                    </span>
                </label>
                <div class="icon-input-container">
                    <input id="personal-widget-icon"
                        v-model="typedIcon"
                        type="text"
                        @input="debounceIconUpdate"
                        :placeholder="t('iframewidget', 'si:github or si:nextcloud')">
                    <input type="color" 
                           :value="colorValue" 
                           @input="updateColor" 
                           :disabled="!state.widgetIcon || !state.widgetIcon.startsWith('si:')">
                    <div class="icon-preview" :style="iconPreviewStyle">
                        <div :class="state.widgetIcon || 'icon-iframe'"></div>
                    </div>
                </div>

                <!-- iFrame URL -->
                <label for="personal-iframe-url">
                    {{ t('iframewidget', 'iFrame URL') }}
                </label>
                <input id="personal-iframe-url"
                    v-model="state.iframeUrl"
                    type="url"
                    :placeholder="t('iframewidget', 'https://example.com')">

                <!-- Extra Wide Toggle -->
                <label for="personal-extra-wide">
                    {{ t('iframewidget', 'Extra Wide Widget') }}
                </label>
                <input id="personal-extra-wide"
                    v-model="state.extraWide"
                    type="checkbox">
            </div>

            <div class="button-container">
                <button @click="saveSettings" 
                        :disabled="saving"
                        class="primary">
                    <span v-if="!saving">{{ t('iframewidget', 'Save') }}</span>
                    <span v-else class="icon icon-loading-small"></span>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import { loadState } from '@nextcloud/initial-state'
import { generateUrl } from '@nextcloud/router'
import axios from '@nextcloud/axios'
import debounce from 'debounce'
import { showSuccess, showError } from '@nextcloud/dialogs'

export default {
    name: 'PersonalSettings',
    data() {
        return {
            state: {
                widgetTitle: '',
                widgetIcon: '',
                iframeUrl: '',
                extraWide: false,
            },
            saving: false,
            typedIcon: '',
            originalState: null,
        }
    },
    computed: {
        colorValue() {
            if (!this.state.widgetIcon || !this.state.widgetIcon.startsWith('si:')) {
                return '#000000'
            }
            const match = this.state.widgetIcon.match(/--color:([^;]+)/)
            return match ? match[1] : '#000000'
        },
        iconPreviewStyle() {
            return {
                backgroundColor: this.state.widgetIcon?.includes('--invert')
                    ? '#000000'
                    : 'transparent',
            }
        },
    },
    created() {
        try {
            const config = loadState('iframewidget', 'personal-iframewidget-config')
            this.state = { ...config }
            this.originalState = { ...config }
            this.typedIcon = this.state.widgetIcon
        } catch (e) {
            console.error('Failed to load personal widget config:', e)
            showError(t('iframewidget', 'Failed to load settings'))
        }
    },
    methods: {
        async saveSettings() {
            this.saving = true
            try {
                await axios.post(generateUrl('/apps/iframewidget/personal-settings'), this.state)
                this.originalState = { ...this.state }
                showSuccess(t('iframewidget', 'Settings saved successfully'))
            } catch (e) {
                console.error('Failed to save settings:', e)
                showError(t('iframewidget', 'Failed to save settings'))
            } finally {
                this.saving = false
            }
        },
        debounceIconUpdate: debounce(function() {
            this.state.widgetIcon = this.typedIcon
        }, 300),
        updateColor(e) {
            const color = e.target.value
            let icon = this.state.widgetIcon || ''
            
            if (icon.startsWith('si:')) {
                icon = icon.replace(/--color:[^;]+/, `--color:${color}`)
                if (!icon.includes('--color:')) {
                    icon += `;--color:${color}`
                }
                this.state.widgetIcon = icon
                this.typedIcon = icon
            }
        },
    },
}
</script>

<style scoped>
.iframewidget-settings-form {
    max-width: 800px;
}

.iframewidget-grid-form {
    display: grid;
    grid-template-columns: 200px 1fr;
    gap: 1em;
    align-items: center;
    margin: 1em 0;
}

.icon-input-container {
    display: flex;
    gap: 0.5em;
    align-items: center;
}

.icon-preview {
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 22px;
}

.settings-hint {
    color: var(--color-text-lighter);
}

.button-container {
    margin-top: 1em;
}

input[type="url"],
input[type="text"] {
    width: 100%;
    max-width: 400px;
}
</style>
