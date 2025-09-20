<template>
    <div id="iframewidget-admin-settings" class="section">
        <!-- Header section -->
        <div class="iframewidget-header">
            <h2>{{ t('iframewidget', 'iFrame Widget Settings') }}</h2>
            <div class="iframewidget-logo">
                <a href="https://github.com/IT-BAER" target="_blank" rel="noopener noreferrer" class="logo-link">
                    <img src="../../img/baer4-100x100.png" alt="Logo" class="iframewidget-logo-image">
                </a>
                <a href="https://github.com/IT-BAER/nc-iframewidget/releases" target="_blank" rel="noopener noreferrer" class="version-link">
                    <span class="iframewidget-version">v0.8.0</span>
                </a>
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

                    <!-- URL to Display section -->
                    <label for="iframeUrl">{{ t('iframewidget', 'URL to Display') }}</label>
                    <input type="text" 
                        :value="typedUrl"
                        @input="handleUrlInput"
                        id="iframeUrl" 
                        class="iframewidget-input" 
                        name="iframeUrl" 
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

						<!-- CSP Error state - Shows helpful guidance when content is blocked by CSP -->
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
								:style="{ height: previewHeight }"
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

        <!-- Group-based iFrame Widgets Section -->
        <div class="iframewidget-group-section">
            <h3>{{ t('iframewidget', 'Group-based iFrame Widgets') }}</h3>
            <p class="settings-hint">
                {{ t('iframewidget', 'Create iFrame widgets that are only visible to specific user groups.') }}
            </p>

            <!-- Add New Group Widget Button -->
            <div class="iframewidget-button-group">
                <button @click="showAddGroupDialog" class="primary">
                    {{ t('iframewidget', 'Add Group Widget') }}
                </button>
            </div>

            <!-- Group Widgets List -->
            <div v-if="groupWidgets.length > 0" class="group-widgets-list">
                <div v-for="groupWidget in groupWidgets" :key="groupWidget.groupId" class="group-widget-item">
                    <div class="group-widget-header">
                        <h4>{{ groupWidget.groupDisplayName || groupWidget.groupId }}</h4>
                        <div class="group-widget-actions">
                            <button @click="editGroupWidget(groupWidget)" class="button">
                                {{ t('iframewidget', 'Edit') }}
                            </button>
                            <button @click="deleteGroupWidget(groupWidget.groupId)" class="button button-danger">
                                {{ t('iframewidget', 'Delete') }}
                            </button>
                        </div>
                    </div>
                    <div class="group-widget-details">
                        <p><strong>{{ t('iframewidget', 'Title:') }}</strong> {{ groupWidget.widgetTitle || t('iframewidget', 'Not set') }}</p>
                        <p><strong>{{ t('iframewidget', 'URL:') }}</strong> {{ groupWidget.iframeUrl || t('iframewidget', 'Not set') }}</p>
                        <p><strong>{{ t('iframewidget', 'Icon:') }}</strong> {{ groupWidget.widgetIcon || t('iframewidget', 'Default') }}</p>
                    </div>
                </div>
            </div>
            <div v-else class="no-group-widgets">
                <p>{{ t('iframewidget', 'No group widgets configured yet.') }}</p>
            </div>
        </div>

        <!-- Group Widget Dialog -->
        <div v-if="showGroupDialog" class="modal-overlay" @click="hideGroupDialog">
            <div class="modal-content" @click.stop>
                <div class="modal-header">
                    <h3>{{ isEditing ? t('iframewidget', 'Edit Group Widget') : t('iframewidget', 'Add Group Widget') }}</h3>
                    <button @click="hideGroupDialog" class="modal-close">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="iframewidget-grid-form">
                        <!-- Group Selection -->
                        <label for="group-select">{{ t('iframewidget', 'Select Group') }}</label>
                        <select id="group-select" v-model="selectedGroupId" :disabled="isEditing">
                            <option value="">{{ t('iframewidget', 'Choose a group...') }}</option>
                            <option v-for="group in availableGroups" :key="group.id" :value="group.id">
                                {{ group.displayName || group.id }}
                            </option>
                        </select>

                        <!-- Widget Title -->
                        <label for="group-widget-title">{{ t('iframewidget', 'Widget Title') }}</label>
                        <input id="group-widget-title"
                            v-model="groupWidgetForm.widgetTitle"
                            type="text"
                            :placeholder="t('iframewidget', 'Group iFrame Widget')">

                        <!-- Widget Icon -->
                        <label for="group-widget-icon">{{ t('iframewidget', 'Widget Icon') }}
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
                            <input id="group-widget-icon"
                                v-model="groupWidgetForm.widgetIcon"
                                type="text"
                                :placeholder="t('iframewidget', 'si:nextcloud')">
                            <input type="color"
                                :value="groupWidgetForm.widgetIconColor || '#ffffff'"
                                @input="updateGroupColor"
                                :disabled="!groupWidgetForm.widgetIcon || !groupWidgetForm.widgetIcon.startsWith('si:')"
                                class="color-picker">
                            <div class="color-button-container" :class="{'has-button': groupWidgetForm.widgetIconColor && groupWidgetForm.widgetIconColor !== ''}">
                                <transition name="fade-scale">
                                    <button v-if="groupWidgetForm.widgetIconColor && groupWidgetForm.widgetIconColor !== ''" 
                                            type="button"
                                            class="icon-delete icon-reset-color" 
                                            @click="clearGroupColor"
                                            :title="t('iframewidget', 'Reset color')">
                                    </button>
                                </transition>
                            </div>
                        </div>

                        <!-- URL -->
                        <label for="group-iframe-url">{{ t('iframewidget', 'URL to Display') }}</label>
                        <input id="group-iframe-url"
                            v-model="groupWidgetForm.iframeUrl"
                            type="text"
                            :placeholder="t('iframewidget', 'https://example.org')">

                        <!-- Height -->
                        <label for="group-iframe-height">{{ t('iframewidget', 'iFrame Height (px)') }}</label>
                        <input id="group-iframe-height"
                            v-model="groupWidgetForm.iframeHeight"
                            type="number"
                            min="0"
                            :placeholder="t('iframewidget', '100%')">

                        <!-- Extra Wide -->
                        <label for="group-extra-wide" class="checkbox-label">
                            {{ t('iframewidget', 'Extra Wide (2 Col)') }}
                        </label>
                        <div class="checkbox-container">
                            <label>
                                <input id="group-extra-wide"
                                    v-model="groupWidgetForm.extraWide" 
                                    type="checkbox" 
                                    class="checkbox"
                                    :true-value="true"
                                    :false-value="false">
                                <span class="checkbox-icon"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Widget Preview -->
                <div class="modal-preview-section">
                    <h4 class="preview-title">{{ t('iframewidget', 'Widget Preview') }}</h4>
                    <div class="preview-container" :style="{ width: groupWidgetForm.extraWide ? '560px' : '320px' }">
                        <div class="preview-header" :class="{'preview-title-empty': !groupWidgetForm.widgetTitle || groupWidgetForm.widgetTitle.trim() === ''}">
                            <h2>
                                <span v-if="groupWidgetForm.widgetIcon && groupWidgetForm.widgetTitle && groupWidgetForm.widgetTitle.trim() !== ''"
                                    class="widget-icon">
                                    <img :src="getIconUrl(groupWidgetForm.widgetIcon, groupWidgetForm.widgetIconColor)" 
                                        :alt="groupWidgetForm.widgetIcon" 
                                        @error="handleIconError"
                                        class="dashboard-icon">
                                </span>
                                <span class="icon-iframewidget" v-else-if="groupWidgetForm.widgetTitle && groupWidgetForm.widgetTitle.trim() !== ''"></span>
                                <span v-if="groupWidgetForm.widgetTitle && groupWidgetForm.widgetTitle.trim() !== ''">{{ groupWidgetForm.widgetTitle }}</span>
                            </h2>
                        </div>
                        <div class="preview-content" :class="{'preview-title-empty': !groupWidgetForm.widgetTitle || groupWidgetForm.widgetTitle.trim() === ''}">
                            <div v-if="!groupWidgetForm.iframeUrl" class="preview-empty">
                                {{ t('iframewidget', 'No URL configured. Please set a URL in the Settings.') }}
                            </div>

                            <!-- CSP Error state - Shows helpful guidance when content is blocked by CSP -->
                            <div v-else-if="groupIframeError && groupWidgetForm.iframeUrl" class="widget-error csp-error">
                                <div class="error-title">{{ t('iframewidget', 'Failed to load content') }}</div>
                                <p>{{ t('iframewidget', 'This might be caused by Content Security Policy (CSP) restrictions.') }}</p>
                                <div class="error-actions">
                                    <a href="https://github.com/IT-BAER/nc-iframewidget#csp-configuration" target="_blank" class="button primary">
                                        {{ t('iframewidget', 'View CSP Configuration Guide') }}
                                    </a>
                                </div>
                            </div>
                            <iframe v-else
                                    :src="groupWidgetForm.iframeUrl"
                                    :style="{ height: groupPreviewHeight }"
                                    class="preview-frame"
                                    referrerpolicy="no-referrer"
                                    allow="fullscreen"
                                    @error="handleGroupIframeError"
                                    @load="groupIframeError = false"
                                    sandbox="allow-same-origin allow-scripts allow-popups allow-forms">
                            </iframe>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button @click="hideGroupDialog" class="button">
                        {{ t('iframewidget', 'Cancel') }}
                    </button>
                    <button @click="saveGroupWidget" class="button primary" :disabled="!selectedGroupId">
                        {{ isEditing ? t('iframewidget', 'Update') : t('iframewidget', 'Create') }}
                    </button>
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
        const loadedState = loadState('iframewidget', 'admin-config');
        const defaultState = {
            widgetTitle: 'iFrame Widget',
            widgetIcon: 'si:nextcloud',
            widgetIconColor: '',
            extraWide: false,
            maxSize: false,
            iframeUrl: '',
            iframeHeight: ''
        };

        // Ensure we always have a complete reactive state object
        // Merge loaded state with defaults to prevent undefined properties
        const state = loadedState && typeof loadedState === 'object'
            ? { ...defaultState, ...loadedState }
            : { ...defaultState };

        return {
            state: state,
            typedUrl: '',
            typedIcon: '',
            urlUpdateTimer: null,
            iconUpdateTimer: null,
            loading: false,
            isLoading: false,
            iframeError: false,
            // Group management data
            groupWidgets: [],
            availableGroups: [],
            showGroupDialog: false,
            isEditing: false,
            selectedGroupId: '',
            groupWidgetForm: {
                widgetTitle: '',
                widgetIcon: '',
                widgetIconColor: '',
                iframeUrl: '',
                iframeHeight: '',
                extraWide: false
            },
            // Component lifecycle management
            componentMounted: false,
            pendingUpdates: [],
            // Group widget preview
            groupIframeError: false
        }
    },
    computed: {
        /**
         * Determine if widget is in extra-wide mode
         * @returns {boolean} True if extra-wide
         */
        isExtraWide() {
            return this.state && (this.state.extraWide === true || this.state.extraWide === 'true');
        },
        
        /**
         * Calculate preview iframe height
         * @returns {string} CSS height value
         */
        previewHeight() {
            if (!this.state || !this.state.iframeHeight || this.state.iframeHeight === '0') {
                return '100%';
            } else {
                return parseInt(this.state.iframeHeight) + 'px';
            }
        },

        /**
         * Calculate group widget preview iframe height
         * @returns {string} CSS height value
         */
        groupPreviewHeight() {
            if (!this.groupWidgetForm.iframeHeight || this.groupWidgetForm.iframeHeight === '0') {
                return '100%';
            } else {
                return parseInt(this.groupWidgetForm.iframeHeight) + 'px';
            }
        },
        
        /**
         * Computed property for the extra-wide toggle
         */
        extraWideComputed: {
            get() {
                return this.state && this.state.extraWide === 'true';
            },
            set(value) {
                if (this.state) {
                    this.state.extraWide = value ? 'true' : 'false';
                    // Update preview immediately when checkbox changes
                    this.$nextTick(() => {
                        const container = this.$el.querySelector('.preview-container');
                        if (container) {
                            container.style.width = value ? '640px' : '320px';
                        }
                    });
                }
            }
        },
    
		colorValue() {
			// Return a default color when empty
			return (this.state && this.state.widgetIconColor) || "#ffffff";
		}
    },
    mounted() {
        // Defer reactive property access until next tick to ensure Vue reactivity is fully initialized
        this.$nextTick(() => {
            // Safely initialize typed values with state properties
            this.typedUrl = this.state && this.state.iframeUrl ? this.state.iframeUrl : '';
            this.typedIcon = this.state && this.state.widgetIcon ? this.state.widgetIcon : '';

            // Only call updatePreview if state is available and reactive
            if (this.state) {
                this.updatePreview();
            }

            // Handle preview container width
            const container = this.$el.querySelector('.preview-container');
            if (container && this.state && (this.state.extraWide === 'true' || this.state.extraWide === true)) {
                container.style.width = '640px';
            }

            // Listen for actual CSP errors in the console
            window.addEventListener('securitypolicyviolation', this.handleCSPViolation);

            // Check if iframe loads correctly after a timeout
            if (this.state && this.state.iframeUrl) {
                setTimeout(() => {
                    this.checkIframeLoaded();
                }, 3000);
            }

            // Load group data
            this.loadGroupData();

            // Mark component as mounted for safe reactivity updates
            this.componentMounted = true;
        });
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

    watch: {
        'state.iframeUrl': function(newUrl, oldUrl) {
            if (!this.state) return;

            if (newUrl !== oldUrl) {
                this.iframeError = false;

                // Attempt to reload iframe with new URL
                this.$nextTick(() => {
                    const iframe = this.$el.querySelector('iframe');
                    if (iframe) {
                        // Force reload by updating src
                        iframe.src = newUrl;
                    }
                });
            }
        }
    },

    methods: {
    
        /**
        * Handle color picker changes
        * 
        * Updates the icon color when the color picker value changes and
        * triggers a debounced update to prevent excessive reloads.
        * 
        * @param {Event} event - Color input change event
        */
    	updateColor(event) {
        	this.state.widgetIconColor = event.target.value;
        	this.debounceIconUpdate();
    	},

        handleUrlInput(event) {
            // Update internal value immediately (non-reactive)
            this.typedUrl = event.target.value;
            // Call debounce function
            this.debounceUrlUpdate();
        },

        /**
         * Debounce URL updates to prevent excessive iframe reloads
         */
        debounceUrlUpdate() {

            // Clear any existing timer
            if (this.urlUpdateTimer) {
                clearTimeout(this.urlUpdateTimer);
            }
            
            // Only store the input in this.typedUrl (non-reactive property)
            this.urlUpdateTimer = setTimeout(() => {

                // Only after the full 500ms, update state and UI
                this.iframeError = false;
                this.state.iframeUrl = this.typedUrl;
            }, 500);
        },

        handleCSPViolation(e) {
            if (e.blockedURI && this.state.iframeUrl && 
                (e.blockedURI === this.state.iframeUrl || 
                this.state.iframeUrl.startsWith(e.blockedURI))) {
                this.iframeError = true;
            }
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
		 * Handle errors loading iframe content
		 * @param {Event} event - Error event from iframe
		 */
		handleIframeError(event) {
			console.warn('Failed to load iframe content: ', event);
			this.iframeError = true;
		},

		/**
		 * Handle errors loading group widget iframe content
		 * @param {Event} event - Error event from iframe
		 */
		handleGroupIframeError(event) {
			console.warn('Failed to load group widget iframe content: ', event);
			this.groupIframeError = true;
		},
    
		/**
		 * Checks if iframe content can be accessed
		 * Detects Content Security Policy (CSP) violations by trying to access iframe DOM
		 */
        checkIframeLoaded() {
            const iframe = this.$el.querySelector('iframe');
            if (!iframe || !this.config.iframeUrl) return;
            
            this.isLoading = true;
            
            // Set up a visibility check instead of content access
            const timeoutDuration = 5000;
            const loadTimeout = setTimeout(() => {
                // If iframe is visible and has non-zero dimensions, consider it loaded
                if (iframe.offsetWidth > 0 && iframe.offsetHeight > 0) {
                    this.iframeError = false;
                } else {
                    // Only show error if iframe is not visible after timeout
                    this.iframeError = true;
                }
                this.isLoading = false;
            }, timeoutDuration);
            
            // Clear timeout when iframe loads
            iframe.onload = () => {
                clearTimeout(loadTimeout);
                this.iframeError = false;
                this.isLoading = false;
            };
            
            // Also clear the error if iframe becomes visible
            const visibilityCheck = setInterval(() => {
                if (iframe.offsetWidth > 0 && iframe.offsetHeight > 0) {
                    clearInterval(visibilityCheck);
                    this.iframeError = false;
                    this.isLoading = false;
                }
            }, 500);
            
            // Clean up interval after max time
            setTimeout(() => clearInterval(visibilityCheck), timeoutDuration + 1000);
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
            // Guard against undefined state
            if (!this.state) return;

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
         * Load group widgets and available groups
         */
        loadGroupData() {
            this.loadGroupWidgets();
            this.loadAvailableGroups();
        },
        
        /**
         * Load configured group widgets
         */
        loadGroupWidgets() {
            const url = generateUrl('/apps/iframewidget/group-widgets');
            console.log('Loading group widgets from:', url);
            axios.get(url)
                .then(response => {
                    console.log('Group widgets response:', response.data);
                    this.groupWidgets = response.data || [];
                    console.log('Group widgets set to:', this.groupWidgets);
                    this.$forceUpdate(); // Force Vue to re-render
                })
                .catch(error => {
                    console.error('Failed to load group widgets:', error);
                    this.groupWidgets = [];
                });
        },
        
        /**
         * Load available groups
         */
        loadAvailableGroups() {
            const url = generateUrl('/apps/iframewidget/groups');
            axios.get(url)
                .then(response => {
                    this.availableGroups = response.data || [];
                })
                .catch(error => {
                    console.error('Failed to load groups:', error);
                    this.availableGroups = [];
                });
        },
        
        /**
         * Show add group widget dialog
         */
        showAddGroupDialog() {
            this.isEditing = false;
            this.selectedGroupId = '';
            this.groupWidgetForm = {
                widgetTitle: '',
                widgetIcon: '',
                widgetIconColor: '',
                iframeUrl: '',
                iframeHeight: '',
                extraWide: false
            };
            this.groupIframeError = false;
            this.showGroupDialog = true;
        },
        
        /**
         * Hide group widget dialog
         */
        hideGroupDialog() {
            this.showGroupDialog = false;
            this.isEditing = false;
            this.selectedGroupId = '';
        },
        
        /**
         * Edit existing group widget
         */
        editGroupWidget(groupWidget) {
            this.isEditing = true;
            this.selectedGroupId = groupWidget.groupId;
            this.groupWidgetForm = {
                widgetTitle: groupWidget.widgetTitle || '',
                widgetIcon: groupWidget.widgetIcon || '',
                widgetIconColor: groupWidget.widgetIconColor || '',
                iframeUrl: groupWidget.iframeUrl || '',
                iframeHeight: groupWidget.iframeHeight || '',
                extraWide: groupWidget.extraWide === 'true' || groupWidget.extraWide === true
            };
            this.groupIframeError = false;
            this.showGroupDialog = true;
        },
        
        /**
         * Save group widget
         */
        saveGroupWidget() {
            if (!this.selectedGroupId) {
                showError(t('iframewidget', 'Please select a group'));
                return;
            }
            
            const url = generateUrl('/apps/iframewidget/group-widgets');
            const data = {
                groupId: this.selectedGroupId,
                ...this.groupWidgetForm
            };
            
            console.log('Saving group widget:', data);
            console.log('Form data:', this.groupWidgetForm);
            
            axios.post(url, data, {
                headers: {
                    'Content-Type': 'application/json',
                    'requesttoken': OC.requestToken,
                    'OCS-APIRequest': 'true',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    console.log('Save response:', response);
                    showSuccess(t('iframewidget', 'Group widget saved'));
                    this.hideGroupDialog();
                    // Add a small delay before reloading to ensure backend has processed
                    setTimeout(() => {
                        console.log('Reloading group widgets...');
                        this.loadGroupWidgets();
                    }, 500);
                })
                .catch(error => {
                    console.error('Save error:', error);
                    console.error('Error response:', error.response);
                    showError(t('iframewidget', 'Could not save group widget'));
                    console.error(error);
                });
        },
        
        /**
         * Delete group widget
         */
        deleteGroupWidget(groupId) {
            if (!confirm(t('iframewidget', 'Are you sure you want to delete this group widget?'))) {
                return;
            }
            
            const url = generateUrl('/apps/iframewidget/group-widgets/' + encodeURIComponent(groupId));
            axios.delete(url)
                .then(() => {
                    showSuccess(t('iframewidget', 'Group widget deleted'));
                    this.loadGroupWidgets();
                })
                .catch(error => {
                    showError(t('iframewidget', 'Could not delete group widget'));
                    console.error(error);
                });
        },
        
        /**
         * Handle group widget color picker changes
         * 
         * Updates the group widget icon color when the color picker value changes
         * 
         * @param {Event} event - Color input change event
         */
        updateGroupColor(event) {
            this.groupWidgetForm.widgetIconColor = event.target.value;
            this.$forceUpdate();
        },

        /**
         * Clear group widget icon color
         */
        clearGroupColor() {
            this.groupWidgetForm.widgetIconColor = '';
            this.$forceUpdate();
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

.iframewidget-logo a {
    text-decoration: none;
    color: inherit;
}

.iframewidget-logo a.logo-link {
    display: block;
}

.iframewidget-logo a:hover .iframewidget-version {
    text-decoration: underline;
    color: var(--color-primary-element);
}

.iframewidget-logo-image {
    width: 60px;
    height: 60px;
    border-radius: 5px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.iframewidget-logo-image:hover {
    transform: scale(1.05);
    filter: drop-shadow(1px -1px 3px rgba(0, 0, 0, 0.5));
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
    background-position: center;
    float: left;
    margin-top: -6px;
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
    float: left !important;
    display: inline-flex !important;
    margin-right: 16px !important;
    margin-left: 0 !important;
    position: relative !important;
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

/* Group Management Styles */
.iframewidget-group-section {
    margin-top: 40px;
    padding-top: 30px;
    border-top: 1px solid var(--color-border);
}

.iframewidget-group-section h3 {
    margin-bottom: 10px;
    color: var(--color-text-maxcontrast);
}

.group-widgets-list {
    margin-top: 20px;
}

.group-widget-item {
    border: 1px solid var(--color-border);
    border-radius: var(--border-radius);
    padding: 15px;
    margin-bottom: 15px;
    background-color: var(--color-main-background);
}

.group-widget-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.group-widget-header h4 {
    margin: 0;
    color: var(--color-text-maxcontrast);
}

.group-widget-actions {
    display: flex;
    gap: 10px;
}

.group-widget-actions .button {
    padding: 5px 10px;
    font-size: 12px;
}

.group-widget-details p {
    margin: 5px 0;
    color: var(--color-text-light);
}

.no-group-widgets {
    text-align: center;
    padding: 30px;
    color: var(--color-text-light);
    border: 2px dashed var(--color-border);
    border-radius: var(--border-radius);
}

/* Modal Styles */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-content {
    background-color: var(--color-main-background);
    border-radius: var(--border-radius);
    width: 90%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid var(--color-border);
}

.modal-header h3 {
    margin: 0;
    color: var(--color-text-maxcontrast);
}

.modal-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: var(--color-text-light);
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-close:hover {
    color: var(--color-text-maxcontrast);
}

.modal-body {
    padding: 20px;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding: 20px;
    border-top: 1px solid var(--color-border);
}

.group-widget-form {
    display: grid;
    gap: 15px;
}

.group-widget-form label {
    font-weight: 600;
    color: var(--color-text-maxcontrast);
}

.group-widget-form input,
.group-widget-form select {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid var(--color-border);
    border-radius: var(--border-radius);
    background-color: var(--color-background-dark);
    color: var(--color-text-light);
}

.group-widget-form input:focus,
.group-widget-form select:focus {
    outline: none;
    border-color: var(--color-primary);
}

.icon-input-container {
    display: flex;
    gap: 10px;
    align-items: center;
}

.icon-input-container input[type="text"] {
    flex: 1;
}

.color-picker {
    width: 50px;
    height: 36px;
    border: none;
    border-radius: var(--border-radius);
    cursor: pointer;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .modal-content {
        width: 95%;
        margin: 20px;
    }
    
    .group-widget-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .group-widget-actions {
        width: 100%;
        justify-content: flex-end;
    }
}

/* Modal preview section styling */
.modal-preview-section {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-top: 20px;
    padding: 20px 0 30px 0;
    border-top: 1px solid var(--color-border);
}

.modal-preview-section .preview-title {
    text-align: center;
    margin-bottom: 15px;
}

/* Responsive preview adjustments */
@media (max-width: 768px) {
    .modal-preview-section {
        padding: 15px 0 20px 0;
    }
    
    .modal-preview-section .preview-container {
        width: 100% !important;
        max-width: 320px;
    }
}

</style>
