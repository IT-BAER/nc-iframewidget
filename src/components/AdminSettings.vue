<template>
    <div id="iframewidget-admin-settings" class="section">
        <!-- Header section -->
        <div class="iframewidget-header">
            <h2>{{ t('iframewidget', 'iFrame Widget Settings') }}</h2>
            <div class="iframewidget-logo">
                <img src="../../img/baer4-100x100.png" alt="Logo" class="iframewidget-logo-image">
                <span class="iframewidget-version">v0.5.0</span>
            </div>
        </div>
        
        <p class="settings-hint">
            {{ t('iframewidget', 'Configure the iFrame Widget for the Dashboard.') }}
        </p>
        
        <div class="iframewidget-admin-container">
            <!-- Left side: Settings form -->
            <div class="iframewidget-admin-form">
                <div class="iframewidget-grid-form">
                    <!-- Widget Title -->
                    <label for="iframe-widget-title">
                        {{ t('iframewidget', 'Widget Title') }}
                    </label>
                    <input id="iframe-widget-title"
                        v-model="state.widgetTitle"
                        type="text"
                        placeholder="Hidden">
                    
                    <!-- Widget Icon -->
                    <label for="widget-icon">
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
						<input id="widget-icon"
    						v-model="typedIcon"
    						type="text"
    						@input="debounceIconUpdate"
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
                    <label for="iframe-url">
                        {{ t('iframewidget', 'URL to Display') }}
                    </label>
                    <input id="iframe-url"
                        v-model="typedUrl"
                        type="url"
                        @input="debounceUrlUpdate"
                        placeholder="https://example.org">

                    <!-- iFrame Height -->
                    <label for="iframe-height">
                        {{ t('iframewidget', 'iFrame Height (px)') }}
                    </label>
                    <input id="iframe-height"
                        v-model="state.iframeHeight"
                        type="number"
                        min="0"
                        placeholder="100%">

                    <!-- Extra Wide Toggle -->
                    <label for="extra-wide" class="checkbox-label">
                        {{ t('iframewidget', 'Extra Wide (2 Col)') }}
                    </label>
                    <div class="checkbox-container">
                        <label>
                            <input id="extra-wide"
                                v-model="extraWideComputed"
                                type="checkbox"
                                class="checkbox">
                            <span class="checkbox-icon"></span>
                        </label>
                    </div>
                </div>

                <!-- Save Button -->
                <div class="iframewidget-button-group">
                    <button @click="saveSettings" class="primary">
                        {{ t('iframewidget', 'Save') }}
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
                        <iframe v-else
                                :src="state.iframeUrl"
                                :style="{ height: previewHeight }"
                                class="preview-frame"
                                referrerpolicy="no-referrer"
                                allow="fullscreen"
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

export default {
    name: 'AdminSettings',
    data() {
        return {
            state: loadState('iframewidget', 'admin-config') || {
                widgetTitle: 'iFrame Widget',
                widgetIcon: 'si:nextcloud',
                widgetIconColor: '', 
                extraWide: false,
                maxSize: false,
                iframeUrl: '',
                iframeHeight: ''
            },
            typedUrl: '',         // Temporary storage for URL during typing
        	typedIcon: '',
            urlUpdateTimer: null,  // Timer for debouncing URL updates
        	iconUpdateTimer: null,  // Timer for debouncing icon updates
        	loading: false
        }
    },
    computed: {
        /**
         * Determine if widget is in extra-wide mode
         * @returns {boolean} True if extra-wide
         */
        isExtraWide() {
            return this.state.extraWide === true || this.state.extraWide === 'true';
        },
        
        /**
         * Calculate preview iframe height
         * @returns {string} CSS height value
         */
        previewHeight() {
            if (!this.state.iframeHeight || this.state.iframeHeight === '0') {
                return '100%';
            } else {
                return parseInt(this.state.iframeHeight) + 'px';
            }
        },
        
        /**
         * Computed property for the extra-wide toggle
         */
        extraWideComputed: {
            get() {
                return this.state.extraWide === 'true';
            },
            set(value) {
                this.state.extraWide = value ? 'true' : 'false';
                // Update preview immediately when checkbox changes
                this.$nextTick(() => {
                    const container = this.$el.querySelector('.preview-container');
                    if (container) {
                        container.style.width = value ? '640px' : '320px';
                    }
                });
            }
        },
    
		colorValue() {
			// Return a default color when empty
			return this.state.widgetIconColor || "#ffffff";
		}
    },
    mounted() {
        this.typedUrl = this.state.iframeUrl; // Initialize with current URL
    	this.typedIcon = this.state.widgetIcon;  // Initialize with current icon
        this.updatePreview();
        
        this.$nextTick(() => {
            const container = this.$el.querySelector('.preview-container');
            if (container && (this.state.extraWide === 'true' || this.state.extraWide === true)) {
                container.style.width = '640px';
            }
        });
    },
	beforeDestroy() {
		if (this.urlUpdateTimer) {
			clearTimeout(this.urlUpdateTimer);
		}
		if (this.iconUpdateTimer) {
			clearTimeout(this.iconUpdateTimer);
		}
	},
    methods: {
        /**
         * Handle color picker changes
         * @param {Event} event - Change event
         */
    	updateColor(event) {
        	this.state.widgetIconColor = event.target.value;
        	this.debounceIconUpdate();
    	},
        
        /**
         * Debounce URL updates to prevent excessive iframe reloads
         */
        debounceUrlUpdate() {
            // Clear any existing timer
            if (this.urlUpdateTimer) {
                clearTimeout(this.urlUpdateTimer);
            }
            
            // Set a new timer
            this.urlUpdateTimer = setTimeout(() => {
                this.state.iframeUrl = this.typedUrl;
                this.$forceUpdate(); // Force Vue to update the UI
            }, 500); // 500ms = 0.5 seconds
        },
    
		/**
		 * Debounce icon updates to prevent excessive API requests
		 */
		debounceIconUpdate() {
			// Clear any existing timer
			if (this.iconUpdateTimer) {
				clearTimeout(this.iconUpdateTimer);
			}
			
			// Set a new timer
			this.iconUpdateTimer = setTimeout(() => {
				this.state.widgetIcon = this.typedIcon;  // Only update state after timeout
				this.validateIcon();
			}, 500); // 500ms debounce time
		},
    
		/**
		 * Validate icon by sending a GET request
		 */
		validateIcon() {
			if (!this.state.widgetIcon || !this.state.widgetIcon.startsWith('si:')) {
				this.updatePreview();
				return;
			}
			
			const iconName = this.state.widgetIcon.substring(3).toLowerCase();
			let proxyUrl = generateUrl('/apps/iframewidget/proxy-icon/' + encodeURIComponent(iconName));
			
			if (this.state.widgetIconColor) {
				proxyUrl += '?color=' + encodeURIComponent(this.state.widgetIconColor.replace('#', ''));
			}
			
			// Check if the icon exists using our proxy
			axios.get(proxyUrl)
				.then(response => {
					if (response.data.exists) {
						this.updatePreview();
					} else {
						console.warn(`Icon not found: ${iconName}`);
						showError(t('iframewidget', 'Icon not found: {name}', {name: iconName}));
					}
				})
				.catch(error => {
					console.warn(`Icon not found: ${iconName}`);
					showError(t('iframewidget', 'Icon not found: {name}', {name: iconName}));
				});
		},
    
        /**
         * Generate URL for widget icon
         * @param {string} iconName - Icon name, optionally with si: prefix
         * @param {string} color - Optional hex color for the icon
         * @returns {string} Complete icon URL
         */
        getIconUrl(iconName, color = null) {
            if (!iconName) {
                // Choose fallback based on theme
                const prefersDarkMode = window.matchMedia && 
                                    window.matchMedia('(prefers-color-scheme: dark)').matches;
                return prefersDarkMode ? 
                    OC.filePath('iframewidget', 'img', 'iframewidget-dark.svg') : 
                    OC.filePath('iframewidget', 'img', 'iframewidget.svg');
            }
            
            // Handle Simple Icons format (si:iconname)
            if (iconName.startsWith('si:')) {
                const simpleIconName = iconName.substring(3).toLowerCase();
                
                // Use the simpleicons.org CDN with color support
                if (color) {
                    // Remove # from hex color if present
                    color = color.replace('#', '');
                    return `https://cdn.simpleicons.org/${simpleIconName}/${color}`;
                }
                
                return `https://cdn.simpleicons.org/${simpleIconName}`;
            }
            
            // Fallback for invalid format
            const prefersDarkMode = window.matchMedia && 
                                window.matchMedia('(prefers-color-scheme: dark)').matches;
            return prefersDarkMode ? 
                OC.filePath('iframewidget', 'img', 'iframewidget-dark.svg') : 
                OC.filePath('iframewidget', 'img', 'iframewidget.svg');
        },
        
        /**
         * Handle errors loading icons
         * @param {Event} event - Error event
         */
		handleIconError(event) {
    		console.warn(`Icon not found or blocked by CSP: ${event.target.alt}`);
    
    		// Choose appropriate fallback based on theme
    		const prefersDarkMode = window.matchMedia && 
        		window.matchMedia('(prefers-color-scheme: dark)').matches;
    
    		const fallbackIcon = prefersDarkMode ? 
        		OC.filePath('iframewidget', 'img', 'iframewidget-dark.svg') : 
        		OC.filePath('iframewidget', 'img', 'iframewidget.svg');
    
   			// Replace with fallback
    		event.target.src = fallbackIcon;
		},
        
        /**
         * Clear icon color
         */
        clearColor() {
            this.state.widgetIconColor = '';
            this.$forceUpdate();
            this.updatePreview();
        },
        
        /**
         * Update the preview display
         */
        updatePreview() {
            this.$nextTick(() => {
                const previewHeader = this.$el.querySelector('.preview-header h2');
                if (previewHeader) {
                    // Remove default icon or hide it when custom icon exists
                    const defaultIcon = previewHeader.querySelector('.icon-iframewidget');
                    
                    if (this.state.widgetIcon) {
                        // Hide default icon when custom icon is used
                        if (defaultIcon) defaultIcon.style.display = 'none';
                        
                        // Create/update custom icon
                        const iconEl = previewHeader.querySelector('.widget-icon') || 
                                    document.createElement('span');
                        iconEl.className = 'widget-icon';
                        
                        // Get image element
                        let imgEl = iconEl.querySelector('img');
                        if (!imgEl) {
                            imgEl = document.createElement('img');
                            imgEl.addEventListener('error', this.handleIconError);
                            iconEl.appendChild(imgEl);
                        }
                        
                        // Update src with color parameter
                        imgEl.src = this.getIconUrl(
                            this.state.widgetIcon, 
                            this.state.widgetIconColor
                        );
                        imgEl.alt = this.state.widgetIcon;
                        
                        // Set size
                        imgEl.style.width = '32px';
                        imgEl.style.height = '32px';
                        
                        // Remove all filters since we're using direct color
                        imgEl.classList.remove('icon-dark', 'icon-light');
                        imgEl.style.filter = '';
                        
                        // Insert at beginning if not already present
                        if (!previewHeader.querySelector('.widget-icon')) {
                            previewHeader.insertBefore(iconEl, previewHeader.firstChild);
                        }
                    } else {
                        // Show default icon when no custom icon
                        if (defaultIcon) defaultIcon.style.display = '';
                        
                        // Remove custom icon
                        const iconEl = previewHeader.querySelector('.widget-icon');
                        if (iconEl) iconEl.remove();
                    }
                }
            });
        },
        
        /**
         * Save settings to the server
         */
        saveSettings() {
            this.loading = true;
            const url = generateUrl('/apps/iframewidget/config');
    
            axios.post(url, this.state, {
                headers: {
                    'Content-Type': 'application/json',
                    'requesttoken': OC.requestToken,
                    'OCS-APIRequest': 'true',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(() => {
                this.loading = false;
                showSuccess(t('iframewidget', 'Settings saved'));
            })
            .catch((error) => {
                this.loading = false;
                showError(t('iframewidget', 'Could not save settings'));
                console.error(error);
            });
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

#widget-icon {
    max-width: 350px!important;
}
</style>
