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
                        placeholder="Hidden">
                    
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
                            style="max-width: 350px!important;"
                            placeholder="si:github or si:nextcloud">
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
                        placeholder="https://example.org">

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
                iframeHeight: '',
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
                return '#ffffff'
            }
            return this.state.widgetIconColor || '#ffffff'
        }
    },
    watch: {
        'state.widgetIconColor': {
            handler(newColor) {
                if (this.state.widgetIcon && this.state.widgetIcon.startsWith('si:')) {
                    const iconImage = this.$el?.querySelector('.widget-icon img');
                    if (iconImage) {
                        iconImage.src = this.getIconUrl(this.state.widgetIcon, newColor);
                    }
                }
            },
            immediate: true
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
            // Update the icon preview immediately
            this.$nextTick(() => {
                const iconImage = this.$el?.querySelector('.widget-icon img');
                if (iconImage && this.state.widgetIcon && this.state.widgetIcon.startsWith('si:')) {
                    iconImage.src = this.getIconUrl(this.state.widgetIcon, this.state.widgetIconColor);
                }
            });
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

<style scoped>
/* Form layout */
.iframewidget-grid-form {
    display: grid;
    margin-top: 30px;
}

.iframewidget-grid-form label:not(:first-child) {
    margin-top: 20px;
}

input {
    max-width: 400px;
    width: 100%;
}

/* Checkbox styles */
.checkbox-container {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

.checkbox-container input[type="checkbox"] {
    position: static;
    display: block;
    height: 16px;
    width: 16px;
    margin: 0;
    opacity: 1;
    cursor: pointer;
}

.checkbox-container .checkbox-icon {
    display: none; 
}

.checkbox-container input[type="checkbox"]:checked {
    background-color: var(--color-primary);
    border-color: var(--color-primary);
}

/* Button styles */
.iframewidget-button-group {
    margin-top: 20px;
}

/* Header styles */
.iframewidget-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 10px;
}

.iframewidget-logo {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.iframewidget-logo-image {
    width: 60px;
    height: 60px;
    border-radius: 5px;
}

.iframewidget-version {
    font-size: 12px;
    color: var(--color-text-maxcontrast);
    margin-top: 5px;
}

/* Layout container */
.iframewidget-admin-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-top: 20px;
}

.iframewidget-admin-form {
    flex: 0.5;
    min-width: 330px;
}

.iframewidget-admin-preview {
    flex: 1;
    min-width: 320px;
    max-width: 640px;
}

/* Preview styles */
.preview-container {
    border: 1px solid var(--color-border);
    border-radius: var(--border-radius-container-large);
    overflow: hidden;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
    background-color: var(--color-main-background-blur);
    transition: width 0.3s ease;
}

.preview-header {
    display: flex;
    align-items: center;
    padding: 16px;
    background-color: var(--color-main-background-blur);
}

.preview-header h2 {
    display: block;
    align-items: center;
    flex-grow: 1;
    margin: 0;
    font-size: 20px;
    font-weight: bold;
    padding: 16px 8px;
    height: 56px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.preview-header span:first-child {
    width: 16px;
    height: 16px;
    background-size: 16px;
    margin-right: 8px;
}

.preview-header span {
    background-size: 32px!important;
    width: 32px!important;
    height: 32px!important;
    margin-right: 16px!important;
    background-position: center;
    float: left;
    margin-top: -6px;
    margin-left: 6px;
}

.preview-content {
    margin: 0 16px 16px 16px;
    position: relative;
    height: 424px;
}

.preview-empty {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 200px;
    padding: 20px;
    color: var(--color-text-maxcontrast);
    text-align: center;
}

.preview-frame {
    width: 100%;
    height: 100%;
    border: none;
    min-height: 50px;
}

.preview-title {
    font-size: 1rem;
    font-weight: bold;
    margin-bottom: 10px;
    margin-top: 0;
}

/* Empty title styles */
.preview-header.preview-title-empty {
    height: 10px !important;
    padding: 0 !important;
}

.preview-header.preview-title-empty h2 {
    display: none !important;
}

.preview-content.preview-title-empty {
    height: calc(424px + 78px);
}

/* Custom icon styles */
.preview-header h2 .widget-icon {
    display: inline-flex;
    align-items: center;
    margin-right: 8px;
    width: 32px !important;
    height: 32px !important;
}

.preview-header h2 .widget-icon img {
    width: 32px;
    height: 32px;
    object-fit: contain;
    vertical-align: middle;
}

/* Hide default icon when custom icon is present */
.preview-header h2 .widget-icon + .icon-iframewidget {
    display: none !important;
}

/* Icon input styles */
.icon-input-container {
    display: flex;
    align-items: center;
    gap: 15px;
    max-width: 400px!important;
}

/* Icon Finder Link styles */
.icon-finder {
    font-size: 0.85em;
    font-weight: normal;
    color: var(--color-text-maxcontrast);
}

.icon-finder a {
    color: var(--color-primary);
}

/* Color button container transitions */
.color-button-container {
    display: inline-flex;
    transition: width 0.3s ease, margin 0.3s ease;
    width: 0;
    height: 32px;
}

.color-button-container.has-button {
    width: 32px;
    margin-left: 4px;
}

/* Color picker styles */
input[type="color"] {
    aspect-ratio: 1/1;
    min-width: 32px;
    min-height: 32px;
    width: 32px;
    height: 32px;
    padding: 0!important;
    margin: 0!important;
    border-radius: 50%;
    box-shadow: 0 0 5px rgba(255, 255, 255, 0.5) !important;
    transition: transform 0.2s, box-shadow 0.2s;
    cursor: pointer!important;
}

input[type="color"]:hover {
    transform: scale(1.1);
    transition: transform 0.2s, box-shadow 0.2s;
    box-shadow: 0 0 12px rgba(255, 255, 255, 0.5) !important;
}

input[type="color"]::-webkit-color-swatch-wrapper {
    padding: 0;
}

input[type="color"]::-webkit-color-swatch {
    border: none;
    border-radius: 50%;
}

/* Firefox support */
input[type="color"]::-moz-color-swatch {
    border: none;
    border-radius: 50%;
}

/* Color reset button transitions */
.icon-reset-color {
    min-width: 32px;
    min-height: 32px;
    background-color: transparent;
    border: none;
    cursor: pointer;
    opacity: 0.7;
    margin: 0!important;
    border-radius: 50%;
}

.icon-reset-color:hover {
    opacity: 1;
    background-color: var(--color-background-hover);
}

.fade-scale-enter-from {
    opacity: 0;
    transform: scale(0);
}

.fade-scale-enter-active, 
.fade-scale-leave-active {
    transition: all 0.3s ease;
}

.fade-scale-enter-from,
.fade-scale-leave-to {
    opacity: 0;
    transform: scale(0);
}

.icon-preview {
    display: inline-flex;
    align-items: center;
    margin: 0 4px;
}

/* CSP error display styling */
.widget-error.csp-error {
    display: flex;
    flex-direction: column;
    padding: 16px;
    text-align: center;
    height: 200px;
    justify-content: center;
}

/* Error title styling */
.widget-error .error-title {
    font-weight: bold;
    margin-bottom: 8px;
    color: var(--color-text-maxcontrast);
}

/* Error message paragraph styling */
.widget-error p {
    margin: 8px 0;
    color: var(--color-text-maxcontrast);
}

/* Container for action buttons in error state */
.error-actions {
    margin-top: 16px;
}

/* Button styling for error actions */
.error-actions .button {
    display: inline-block;
    padding: 8px 16px;
    text-decoration: none;
    border-radius: var(--border-radius);
    background-color: var(--color-primary);
    color: var(--color-primary-text);
}

/* Hover state for action buttons */
.error-actions .button:hover {
    background-color: var(--color-primary-element-hover);
}

</style>
