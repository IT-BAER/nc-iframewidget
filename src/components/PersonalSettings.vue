<template>
    <div id="iframewidget-personal-settings" class="section">
        <!-- Header section -->
        <div class="iframewidget-header">
            <h2>{{ t('iframewidget', 'Personal iFrame Widget Settings') }}</h2>
            <div class="iframewidget-logo">
                <img src="../../img/baer4-100x100.png" alt="Logo" class="iframewidget-logo-image">
                <span class="iframewidget-version">v0.6.3</span>
            </div>
        </div>
        
        <p class="settings-hint">
            {{ t('iframewidget', 'Configure your personal iFrame Widget for the Dashboard.') }}
        </p>
        
        <div class="iframewidget-admin-container">
            <!-- Left side: Settings form -->
            <div class="iframewidget-admin-form">
                <div class="iframewidget-grid-form">
                    <!-- Widget Title -->
                    <label for="personal-iframe-widget-title">
                        {{ t('iframewidget', 'Widget Title') }}
                    </label>
                    <input id="personal-iframe-widget-title"
                        v-model="state.widgetTitle"
                        type="text"
                        :placeholder="t('iframewidget', 'Hidden')">
                    
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
                               :disabled="!state.widgetIcon || !state.widgetIcon.startsWith('si:')"
                               class="color-picker">
                                
                        <div class="color-button-container" :class="{'has-button': state.widgetIconColor && state.widgetIconColor !== ''}">
                            <transition name="fade-scale">
                                <button v-if="state.widgetIconColor && state.widgetIconColor !== ''" 
                                        type="button"
                                        class="icon-delete icon-reset-color" 
                                        @click="clearColor"
                                        :title="t('iframewidget', 'Reset color')">
                                </button>
                            </transition>
                        </div>
                    </div>

                    <!-- iFrame URL -->
                    <label for="personal-iframe-url">
                        {{ t('iframewidget', 'URL to Display') }}
                    </label>
                    <input id="personal-iframe-url"
                        :value="typedUrl"
                        @input="handleUrlInput"
                        type="text"
                        class="iframewidget-input"
                        :placeholder="t('iframewidget', 'https://example.org')">

                    <!-- Extra Wide Toggle -->
                    <label for="personal-extra-wide" class="checkbox-label">
                        {{ t('iframewidget', 'Extra Wide (2 Col)') }}
                    </label>
                    <div class="checkbox-container">
                        <label>
                            <input id="personal-extra-wide"
                                v-model="extraWideComputed"
                                type="checkbox"
                                class="checkbox">
                            <span class="checkbox-icon"></span>
                        </label>
                    </div>
                </div>

                <!-- Save Button -->
                <div class="iframewidget-button-group">
                    <button @click="saveSettings" 
                            :disabled="saving"
                            class="primary">
                        <span v-if="!saving">{{ t('iframewidget', 'Save') }}</span>
                        <span v-else class="icon icon-loading-small"></span>
                    </button>
                </div>
            </div>

            <!-- Right side: Widget preview -->
            <div class="iframewidget-admin-preview">
                <h4 class="preview-title">{{ t('iframewidget', 'Preview') }}</h4>
                <div class="preview-container" :style="{ width: isExtraWide ? '640px' : '320px' }">
                    <div class="preview-header" :class="{'preview-title-empty': !state.widgetTitle || state.widgetTitle.trim() === ''}">
                        <h2>
                            <span v-if="state.widgetIcon && state.widgetTitle && state.widgetTitle.trim() !== ''"
                                  class="widget-icon">
                                <img :src="getIconUrl(state.widgetIcon, state.widgetIconColor)" 
                                     :alt="state.widgetIcon" 
                                     @error="handleIconError"
                                     class="dashboard-icon">
                            </span>
                            <span class="icon-iframewidget" v-else-if="state.widgetTitle && state.widgetTitle.trim() !== ''"></span>
                            <span v-if="state.widgetTitle && state.widgetTitle.trim() !== ''">{{ state.widgetTitle }}</span>
                        </h2>
                    </div>
                    <div class="preview-content" :class="{'preview-title-empty': !state.widgetTitle || state.widgetTitle.trim() === ''}">
                        <div v-if="!state.iframeUrl" class="preview-empty">
                            {{ t('iframewidget', 'No URL configured. Please set a URL in the Settings.') }}
                        </div>

                        <!-- CSP Error state -->
                        <div v-else-if="iframeError && state.iframeUrl" class="widget-error csp-error">
                            <div class="error-title">{{ t('iframewidget', 'Failed to load content') }}</div>
                            <p>{{ t('iframewidget', 'This might be caused by Content Security Policy (CSP) restrictions.') }}</p>
                            <div class="error-actions">
                                <a href="https://github.com/IT-BAER/nc-iframewidget#csp-configuration" target="_blank" class="button primary">
                                    {{ t('iframewidget', 'View CSP Configuration Guide') }}
                                </a>
                            </div>
                        </div>
                        <iframe v-else
                                :src="state.iframeUrl"
                                :style="{ height: '100%' }"
                                class="preview-frame"
                                referrerpolicy="no-referrer"
                                allow="fullscreen"
                                @error="handleIframeError"
                                @load="iframeError = false"
                                sandbox="allow-same-origin allow-scripts allow-popups allow-forms">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'
import { loadState } from '@nextcloud/initial-state'
import { showSuccess, showError } from '@nextcloud/dialogs'
import debounce from 'debounce'

export default {
    name: 'PersonalSettings',
    data() {
        return {
            state: {
                widgetTitle: '',
                widgetIcon: '',
                widgetIconColor: '',
                iframeUrl: '',
                extraWide: false,
            },
            saving: false,
            typedIcon: '',
            typedUrl: '',
            urlUpdateTimer: null,
            iconUpdateTimer: null,
            iframeError: false,
            originalState: null
        }
    },
    computed: {
        isExtraWide() {
            return this.state.extraWide === true || this.state.extraWide === 'true'
        },
        extraWideComputed: {
            get() {
                return this.state.extraWide === true;
            },
            set(value) {
                this.state.extraWide = value;
                this.$nextTick(() => {
                    const container = this.$el.querySelector('.preview-container');
                    if (container) {
                        container.style.width = value ? '640px' : '320px';
                    }
                });
            }
        },
        colorValue() {
            if (!this.state.widgetIcon || !this.state.widgetIcon.startsWith('si:')) {
                return '#000000'
            }
            return this.state.widgetIconColor || '#000000'
        }
    },
    created() {
        try {
            const config = loadState('iframewidget', 'personal-iframewidget-config')
            this.state = { ...config }
            this.originalState = { ...config }
            this.typedUrl = this.state.iframeUrl
            this.typedIcon = this.state.widgetIcon
        } catch (e) {
            console.error('Failed to load personal widget config:', e)
            showError(t('iframewidget', 'Failed to load settings'))
        }
    },
    mounted() {
        this.$nextTick(() => {
            const container = this.$el.querySelector('.preview-container');
            if (container && (this.state.extraWide === true)) {
                container.style.width = '640px';
            }
        });

        window.addEventListener('securitypolicyviolation', this.handleCSPViolation);
    },
    beforeDestroy() {
        if (this.urlUpdateTimer) {
            clearTimeout(this.urlUpdateTimer);
        }
        if (this.iconUpdateTimer) {
            clearTimeout(this.iconUpdateTimer);
        }
        window.removeEventListener('securitypolicyviolation', this.handleCSPViolation);
    },
    methods: {
        updateColor(event) {
            this.state.widgetIconColor = event.target.value;
            this.debounceIconUpdate();
        },
        handleUrlInput(event) {
            this.typedUrl = event.target.value;
            this.debounceUrlUpdate();
        },
        debounceUrlUpdate: debounce(function() {
            this.iframeError = false;
            this.state.iframeUrl = this.typedUrl;
        }, 500),
        debounceIconUpdate: debounce(function() {
            this.state.widgetIcon = this.typedIcon;
            this.validateIcon();
        }, 500),
        validateIcon() {
            if (!this.state.widgetIcon || !this.state.widgetIcon.startsWith('si:')) {
                return;
            }
            
            const iconName = this.state.widgetIcon.substring(3).toLowerCase();
            let proxyUrl = generateUrl('/apps/iframewidget/proxy-icon/' + encodeURIComponent(iconName));
            
            if (this.state.widgetIconColor) {
                proxyUrl += '?color=' + encodeURIComponent(this.state.widgetIconColor.replace('#', ''));
            }
            
            axios.get(proxyUrl)
                .then(response => {
                    if (!response.data.exists) {
                        console.warn(`Icon not found: ${iconName}`);
                        showError(t('iframewidget', 'Icon not found: {name}', {name: iconName}));
                    }
                })
                .catch(error => {
                    console.warn(`Icon not found: ${iconName}`);
                    showError(t('iframewidget', 'Icon not found: {name}', {name: iconName}));
                });
        },
        getIconUrl(iconName, color = null) {
            if (!iconName) {
                const prefersDarkMode = window.matchMedia && 
                                    window.matchMedia('(prefers-color-scheme: dark)').matches;
                return prefersDarkMode ? 
                    OC.filePath('iframewidget', 'img', 'iframewidget-dark.svg') : 
                    OC.filePath('iframewidget', 'img', 'iframewidget.svg');
            }
            
            if (iconName.startsWith('si:')) {
                const simpleIconName = iconName.substring(3).toLowerCase();
                
                if (color) {
                    color = color.replace('#', '');
                    return `https://cdn.simpleicons.org/${simpleIconName}/${color}`;
                }
                
                return `https://cdn.simpleicons.org/${simpleIconName}`;
            }
            
            const prefersDarkMode = window.matchMedia && 
                                window.matchMedia('(prefers-color-scheme: dark)').matches;
            return prefersDarkMode ? 
                OC.filePath('iframewidget', 'img', 'iframewidget-dark.svg') : 
                OC.filePath('iframewidget', 'img', 'iframewidget.svg');
        },
        handleIframeError(event) {
            console.warn('Failed to load iframe content: ', event);
            this.iframeError = true;
        },
        handleIconError(event) {
            console.warn(`Icon not found or blocked by CSP: ${event.target.alt}`);
            
            const prefersDarkMode = window.matchMedia && 
                window.matchMedia('(prefers-color-scheme: dark)').matches;
            
            const fallbackIcon = prefersDarkMode ? 
                OC.filePath('iframewidget', 'img', 'iframewidget-dark.svg') : 
                OC.filePath('iframewidget', 'img', 'iframewidget.svg');
            
            event.target.src = fallbackIcon;
        },
        handleCSPViolation(e) {
            if (e.blockedURI && this.state.iframeUrl && 
                (e.blockedURI === this.state.iframeUrl || 
                this.state.iframeUrl.startsWith(e.blockedURI))) {
                this.iframeError = true;
            }
        },
        clearColor() {
            this.state.widgetIconColor = '';
            this.$forceUpdate();
        },
        async saveSettings() {
            this.saving = true;
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
        }
    }
}
</script>

<style>
.iframewidget-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2em;
}

.iframewidget-logo {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.iframewidget-logo-image {
    width: 100px;
    height: 100px;
}

.iframewidget-version {
    font-size: 0.8em;
    color: var(--color-text-lighter);
    margin-top: 0.5em;
}

.iframewidget-admin-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2em;
    max-width: 1200px;
}

.iframewidget-admin-form {
    max-width: 600px;
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

.iframewidget-button-group {
    margin-top: 2em;
}

input[type="url"],
input[type="text"] {
    width: 100%;
    max-width: 400px;
}

.icon-finder {
    font-size: 0.9em;
    color: var(--color-text-lighter);
    margin-left: 0.5em;
}

.preview-title {
    margin-bottom: 1em;
}

.preview-container {
    border: 2px solid var(--color-border);
    border-radius: 8px;
    overflow: hidden;
    transition: width 0.3s ease;
}

.preview-header {
    padding: 1em;
    border-bottom: 1px solid var(--color-border);
    background: var(--color-main-background);
}

.preview-header h2 {
    display: flex;
    align-items: center;
    gap: 0.5em;
    margin: 0;
    font-size: 1em;
}

.preview-content {
    height: 300px;
    background: var(--color-main-background);
    position: relative;
}

.preview-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    text-align: center;
    color: var(--color-text-lighter);
}

.preview-frame {
    width: 100%;
    height: 100%;
    border: none;
}

.widget-icon {
    display: inline-flex;
    align-items: center;
}

.dashboard-icon {
    width: 20px;
    height: 20px;
    object-fit: contain;
}

.color-picker {
    width: 44px !important;
    height: 44px;
    padding: 0;
    border: none;
    border-radius: 22px;
    cursor: pointer;
}

.icon-reset-color {
    background-position: center;
    width: 44px;
    height: 44px;
    border: none;
    border-radius: 22px;
    cursor: pointer;
    opacity: 0.7;
}

.icon-reset-color:hover {
    opacity: 1;
}

.checkbox-container {
    display: flex;
    align-items: center;
}

.checkbox-container label {
    display: flex;
    align-items: center;
    cursor: pointer;
}

.checkbox {
    margin-right: 0.5em;
}

.fade-scale-enter-active, .fade-scale-leave-active {
    transition: all 0.3s ease;
}

.fade-scale-enter, .fade-scale-leave-to {
    opacity: 0;
    transform: scale(0.5);
}

.widget-error {
    padding: 2em;
    text-align: center;
}

.error-title {
    font-weight: bold;
    margin-bottom: 1em;
}

.error-actions {
    margin-top: 2em;
}

.csp-error {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    background: var(--color-background-darker);
}
</style>
