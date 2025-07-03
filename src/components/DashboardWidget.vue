<template>
    <div class="iframewidget-container" 
         :style="{ visibility: configLoaded ? 'visible' : 'hidden' }"
         data-widget-id="iframewidget">
    
        <!-- Loading state -->
        <div v-if="loading" class="widget-loading">
            <div class="icon icon-loading"></div>
        </div>

        <!-- Error state -->
        <div v-else-if="error" class="widget-error">
            {{ t('iframewidget', 'Could not load iFrame Configuration') }}
        </div>

        <!-- No URL configured state -->
		<div v-else-if="!config.iframeUrl" class="widget-empty">
    		<div>{{ t('iframewidget', 'No URL configured.') }}</div>
    		<div>
        		{{ t('iframewidget', 'Please set a URL in the') }}
        		<a :href="adminSettingsUrl">{{ t('iframewidget', 'Admin Settings') }}</a>.
    		</div>
		</div>

		<!-- Iframe content -->
		<iframe v-else-if="config.iframeUrl && !iframeError"
				:src="config.iframeUrl"
				:style="{ height: iframeHeight }"
				class="iframewidget-frame"
				referrerpolicy="no-referrer"
				allow="fullscreen"
				@error="handleIframeError"
				@load="iframeError = false"
				sandbox="allow-same-origin allow-scripts allow-popups allow-forms">
		</iframe>

		<!-- CSP Error state -->
		<div v-else-if="iframeError && config.iframeUrl" class="widget-error csp-error">
			<div class="error-title">{{ t('iframewidget', 'Failed to load content') }}</div>
			<p>{{ t('iframewidget', 'This might be caused by Content Security Policy (CSP) restrictions.') }}</p>
			<div class="error-actions">
				<a href="https://github.com/IT-BAER/nc-iframewidget#csp-configuration" target="_blank" class="button primary">
					{{ t('iframewidget', 'View CSP Configuration Guide') }}
				</a>
			</div>
		</div>
    
    </div>
</template>

<script>
import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'
import { loadState } from '@nextcloud/initial-state'

export default {
    name: 'DashboardWidget',
    data() {
        return {
            loading: true,
            configLoaded: false,
            isLoading: false,
            error: false,
        	iframeError: false,
            config: loadState('iframewidget', 'widget-config') || {
                extraWide: false,
                widgetTitle: 'iFrame Widget',
                widgetIcon: '',
                widgetIconColor: '',
                iframeUrl: 'https://example.org',
                iframeHeight: ''
            },
            observer: null
        }
    },
    computed: {
    
    	/**
    	 * Quick link to the Widget Admin Settings
    	 */
    	adminSettingsUrl() {
        	return OC.generateUrl('/settings/admin/iframewidget');
    	},
    
        /**
         * Determine if widget should be displayed in extra wide mode
         * @returns {boolean} True if widget should be extra wide
         */
        isExtraWide() {
            return this.config.extraWide === 'true' || this.config.extraWide === true
        },
        
        /**
         * Calculate iframe height based on configuration
         * @returns {string} CSS height value
         */
        iframeHeight() {
            return (!this.config.iframeHeight || this.config.iframeHeight === '0') 
                ? '100%' 
                : parseInt(this.config.iframeHeight) + 'px';
        },
        
        /**
         * Check if widget title is empty
         * @returns {boolean} True if title is empty
         */
        widgetTitleEmpty() {
            return !this.config.widgetTitle || this.config.widgetTitle.trim() === '';
        }
    },
    mounted() {
        this.$nextTick(() => {
            this.applyPanelClasses();
            
            if (this.config.extraWide !== undefined) {
                this.configLoaded = true;
            }
        });
    
        this.fetchConfig();
    
        // Setup a mutation observer to watch for dashboard changes
        this.observer = new MutationObserver(() => {
            this.applyPanelClasses();
        });
    
        // Start observing once the component is mounted
        setTimeout(() => {
            const dashboard = document.querySelector('.app-dashboard');
            if (dashboard) {
                this.observer.observe(dashboard, { 
                    childList: true,
                    subtree: false,
                    attributeFilter: ['class']
                });
            }
        }, 500);
    
    	// Check if iframe loads correctly after a timeout
    	if (this.config.iframeUrl) {
        	setTimeout(() => {
            	this.checkIframeLoaded();
        	}, 3000);
    	}
        
        // Listen for actual CSP errors in the console
        window.addEventListener('securitypolicyviolation', this.handleCSPViolation);
    },
    
    beforeDestroy() {
        if (this.observer) {
            this.observer.disconnect();
        }
        window.removeEventListener('securitypolicyviolation', this.handleCSPViolation);
    },
    watch: {
    'config.iframeUrl': function(newUrl, oldUrl) {
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
    },
    config: {
        handler() {
            this.$nextTick(() => {
                this.applyPanelClasses();
            });
        },
        deep: true
    }
    },
    methods: {

        handleCSPViolation(e) {
            if (e.blockedURI && this.config.iframeUrl && 
                (e.blockedURI === this.config.iframeUrl || 
                this.config.iframeUrl.startsWith(e.blockedURI))) {
                this.iframeError = true;
            }
        },

        /**
        * Apply custom classes to the parent panel element
        * 
        * Finds the parent panel element and applies CSS classes based on the widget configuration.
        * Handles extra-wide mode, title visibility, and custom icon integration.
        */
        applyPanelClasses() {
            if (!this.$el || typeof this.$el.closest !== 'function') return;
            
            const parentPanel = this.$el.closest('.panel');
            if (parentPanel) {
                // Ensure extra-wide class is explicitly toggled based on the config
                console.log('DashboardWidget extraWide:', this.isExtraWide, this.config.extraWide);
                parentPanel.classList.toggle('ifw-widget-extra-wide', this.isExtraWide);
                
                parentPanel.classList.toggle('ifw-title-empty', this.widgetTitleEmpty);
                parentPanel.setAttribute('data-widget-id', 'iframewidget');
                
                // Add custom icon if widget has a title and icon
                if (this.config.widgetIcon && !this.widgetTitleEmpty) {
                    const titleEl = parentPanel.querySelector('.panel--header h2');
                    if (titleEl) {
                        // Hide the original icon - search for any of the possible icon classes
                        const iconSelectors = ['.icon-iframewidget'];
                        
                        // Also add a selector for the custom icon if it's a SimpleIcon (si:) format
                        if (this.config.widgetIcon && this.config.widgetIcon.startsWith('si:')) {
                            iconSelectors.push('.' + this.config.widgetIcon.substring(3).toLowerCase());
                        }
                        
                        // Find and hide all matching icons
                        iconSelectors.forEach(selector => {
                            const icon = titleEl.querySelector(selector);
                            if (icon) {
                                icon.style.display = 'none';
                                icon.classList.add('hidden');
                            }
                        });
                        
                        // Create or get icon element
                        let iconEl = titleEl.querySelector('.widget-icon');
                        if (!iconEl) {
                            iconEl = document.createElement('span');
                            iconEl.className = 'widget-icon';
                            titleEl.insertBefore(iconEl, titleEl.firstChild);
                        }
                        
						// Set up image
						let imgEl = iconEl.querySelector('img');
						if (!imgEl) {
							imgEl = document.createElement('img');
							imgEl.addEventListener('error', this.handleIconError);
							iconEl.appendChild(imgEl);
						}

						// Pre-validate icon for SimpleIcons
						if (this.config.widgetIcon && this.config.widgetIcon.startsWith('si:')) {
							imgEl.alt = this.config.widgetIcon;
							imgEl.className = 'dashboard-icon';
							
							// First set to loading state or fallback
							const prefersDarkMode = window.matchMedia && 
								window.matchMedia('(prefers-color-scheme: dark)').matches;
							const tempIcon = prefersDarkMode ? 
								OC.filePath('iframewidget', 'img', 'iframewidget-dark.svg') : 
								OC.filePath('iframewidget', 'img', 'iframewidget.svg');
							imgEl.src = tempIcon;
							
							// Then validate and update if valid
							this.validateIcon(this.config.widgetIcon).then(isValid => {
								if (isValid) {
									imgEl.src = this.getIconUrl(
										this.config.widgetIcon, 
										this.config.widgetIconColor
									);
								}
							});
						} else {
							// Non-SimpleIcons don't need validation
							imgEl.src = this.getIconUrl(
								this.config.widgetIcon, 
								this.config.widgetIconColor
							);
							imgEl.alt = this.config.widgetIcon;
							imgEl.className = 'dashboard-icon';
						}
                        
                        // Force size constraints
                        imgEl.style.width = '32px';
                        imgEl.style.height = '32px';
                    }
                }
            }
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
		 * Check if icon exists using img element instead of XHR
		 * @param {string} iconName - Icon name with si: prefix
		 * @returns {Promise} Promise resolving to boolean indicating if icon exists
		 */
		validateIcon(iconName) {
			return new Promise((resolve) => {
				if (!iconName || !iconName.startsWith('si:')) {
					resolve(true); // Non-simple icons don't need validation
					return;
				}
				
				const simpleIconName = iconName.substring(3).toLowerCase();
				
				// Use image loading instead of XHR to avoid CORS
				const img = new Image();
				const timeoutId = setTimeout(() => {
					// Consider it failed if it takes too long
					img.src = ''; // Cancel the request
					resolve(false);
				}, 5000);
				
				img.onload = () => {
					clearTimeout(timeoutId);
					resolve(true);
				};
				
				img.onerror = () => {
					clearTimeout(timeoutId);
					resolve(false);
				};
				
				// Start loading the image
				img.src = `https://cdn.simpleicons.org/${simpleIconName}`;
			});
		},
    
		/**
		 * Checks if iframe content can be accessed
		 * Detects Content Security Policy (CSP) violations by trying to access iframe DOM
		 * Sets iframeError flag if access is blocked by browser security
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
		 * Handle errors loading iframe content
		 * @param {Event} event - Error event from iframe
		 */
    	handleIframeError(event) {
    		console.warn('Failed to load iframe content: ', event);
    		this.iframeError = true;
		},
        
        /**
         * Fetch widget configuration from backend
         */
        fetchConfig() {
            this.loading = true;
            const url = generateUrl('/apps/iframewidget/config');

            axios.get(url)
                .then((response) => {
                    this.config = response.data;
                    this.loading = false;
                    this.configLoaded = true;
                    this.applyPanelClasses();
                })
                .catch((error) => {
                    console.error(error);
                    this.loading = false;
                    this.error = true;
                    this.configLoaded = true;
                });
        }
    }
}
</script>

<style scoped>
.iframewidget-container {
    width: 100%;
    height: 100%;
    min-height: 100px;
    overflow: hidden;
}

.iframewidget-frame {
    width: 100%;
    border: none;
    overflow: hidden;
}

.widget-loading,
.widget-error,
.widget-empty {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 200px;
    width: 100%;
    color: var(--color-text-maxcontrast);
	text-align: center;
    flex-direction: column;
}

/* CSP error display styling */
.widget-error.csp-error {
    display: flex;
    flex-direction: column;
    padding: 16px;
    text-align: center;
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
