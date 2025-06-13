<template>
    <div class="iframewidget-container" 
         :class="{'ifw-widget-extra-wide': isExtraWide, 'ifw-title-empty': widgetTitleEmpty}"
         :style="{ visibility: configLoaded ? 'visible' : 'hidden' }"
         data-widget-id="personal-iframewidget">
    
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
                {{ t('iframewidget', 'Please set a URL in your') }}
                <a :href="settingsUrl">{{ t('iframewidget', 'Personal Settings') }}</a>.
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
    name: 'PersonalDashboardWidget',
    data() {
        return {
            loading: true,
            configLoaded: false,
            isLoading: false, // Added for consistency
            error: false,
            iframeError: false,
            config: loadState('iframewidget', 'personal-iframewidget-config') || {
                extraWide: false,
                widgetTitle: 'Personal iFrame Widget', // Default title
                widgetIcon: '',
                widgetIconColor: '',
                iframeUrl: '',
                iframeHeight: '' // Default height, can be '0' for 100%
            },
            observer: null
        }
    },
    computed: {
        settingsUrl() {
            return OC.generateUrl('/settings/user/iframewidget');
        },
        isExtraWide() {
            // Consistently check against string 'true' or boolean true
            return this.config.extraWide === 'true' || this.config.extraWide === true;
        },
        iframeHeight() {
            return (!this.config.iframeHeight || this.config.iframeHeight === '0') 
                ? '100%' 
                : parseInt(this.config.iframeHeight) + 'px';
        },        widgetTitleEmpty() {
            // Use the title from the config for this check
            return !this.config.widgetTitle || this.config.widgetTitle.trim() === '';
        }
    },    mounted() {
        this.$nextTick(() => {
            if (this.$el) {
                this.applyPanelClasses();
            }
            
            // Ensure configLoaded is true if config is present
            if (this.config && this.config.extraWide !== undefined) {
                this.configLoaded = true;
            }
            this.loading = false; // Set loading to false after initial setup
        });
    
        // Setup a mutation observer to watch for dashboard changes
        this.observer = new MutationObserver(() => {
            this.applyPanelClasses();
        });
    
        // Start observing once the component is mounted
        // Use a timeout to ensure the dashboard is rendered
        setTimeout(() => {
            const dashboard = document.querySelector('.app-dashboard');
            if (dashboard) {
                this.observer.observe(dashboard, { 
                    childList: true,
                    subtree: false, // Observe only direct children of dashboard for performance
                    attributeFilter: ['class'] // Observe class changes
                });
            }
        }, 500); // Delay observer attachment
    
    	// Check if iframe loads correctly after a timeout
    	if (this.config.iframeUrl) {
        	setTimeout(() => {
            	this.checkIframeLoaded();
        	}, 3000); // Check after 3 seconds
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
            this.iframeError = false; // Reset error state

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
    // Watch for config changes to re-apply panel classes
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
            // Check if the blocked URI is related to the current iframe URL
            if (e.blockedURI && this.config.iframeUrl && 
                (e.blockedURI === this.config.iframeUrl || 
                this.config.iframeUrl.startsWith(e.blockedURI))) {
                this.iframeError = true;
            }
        },

        applyPanelClasses() {
            if (!this.$el || typeof this.$el.closest !== 'function') return;
            
            const parentPanel = this.$el.closest('.panel');
            if (parentPanel) {
                // Force extra wide if enabled
                if (this.isExtraWide) {
                    parentPanel.classList.add('ifw-widget-extra-wide');
                    // Explicitly set grid-column property to ensure it works
                    parentPanel.style.gridColumn = 'span 2';
                } else {
                    parentPanel.classList.remove('ifw-widget-extra-wide');
                    parentPanel.style.gridColumn = '';
                }
                
                // Set empty title class if needed
                parentPanel.classList.toggle('ifw-title-empty', this.widgetTitleEmpty);
                
                // Set widget ID for CSS targeting
                parentPanel.setAttribute('data-widget-id', 'personal-iframewidget');
                
                // Handle title and icon
                const titleEl = parentPanel.querySelector('.panel--header h2');
                if (titleEl) {
                    // Ensure left alignment
                    titleEl.style.textAlign = 'left';
                    titleEl.style.display = 'flex';
                    titleEl.style.alignItems = 'center';
                    titleEl.style.justifyContent = 'flex-start';
                    
                    if (this.config.widgetIcon && !this.widgetTitleEmpty) {
                        // Hide the original icon
                        const originalIcon = titleEl.querySelector('.icon-personal-iframewidget, .icon-iframe'); 
                        if (originalIcon) {
                            originalIcon.style.display = 'none';
                        }
                        
                        let iconEl = titleEl.querySelector('.widget-icon');
                        if (!iconEl) {
                            iconEl = document.createElement('span');
                            iconEl.className = 'widget-icon';
                            titleEl.insertBefore(iconEl, titleEl.firstChild);
                        }
                        
						let imgEl = iconEl.querySelector('img');
						if (!imgEl) {
							imgEl = document.createElement('img');
							imgEl.addEventListener('error', this.handleIconError);
							iconEl.appendChild(imgEl);
						}

						if (this.config.widgetIcon && this.config.widgetIcon.startsWith('si:')) {
							imgEl.alt = this.config.widgetIcon;
							imgEl.className = 'dashboard-icon';
							
							const prefersDarkMode = window.matchMedia && 
								window.matchMedia('(prefers-color-scheme: dark)').matches;
							const tempIcon = prefersDarkMode ? 
								OC.filePath('iframewidget', 'img', 'iframewidget-dark.svg') : 
								OC.filePath('iframewidget', 'img', 'iframewidget.svg');
							imgEl.src = tempIcon;
							
							this.validateIcon(this.config.widgetIcon).then(isValid => {
								if (isValid) {
									imgEl.src = this.getIconUrl(
										this.config.widgetIcon, 
										this.config.widgetIconColor
									);
								}
							});
						} else {
							imgEl.src = this.getIconUrl(
								this.config.widgetIcon, 
								this.config.widgetIconColor
							);
							imgEl.alt = this.config.widgetIcon;
							imgEl.className = 'dashboard-icon';
						}
                        
                        imgEl.style.width = '32px';
                        imgEl.style.height = '32px';
                    }
                } else if (parentPanel) { // Ensure icon is removed if no icon is set
                    const titleEl = parentPanel.querySelector('.panel--header h2');
                    if (titleEl) {
                        const iconEl = titleEl.querySelector('.widget-icon');
                        if (iconEl) {
                            iconEl.remove();
                        }
                        const originalIcon = titleEl.querySelector('.icon-personal-iframewidget, .icon-iframe');
                        if (originalIcon) {
                            originalIcon.style.display = ''; // Show default icon
                        }
                    }
                }
            }
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
            
            // Fallback for non-SimpleIcons or invalid format (e.g. if user inputs a URL)
            if (iconName.startsWith('http') || iconName.startsWith('/')) {
                 return iconName; // Assume it's a direct URL
            }

            const prefersDarkMode = window.matchMedia && 
                                window.matchMedia('(prefers-color-scheme: dark)').matches;
            return prefersDarkMode ? 
                OC.filePath('iframewidget', 'img', 'iframewidget-dark.svg') : 
                OC.filePath('iframewidget', 'img', 'iframewidget.svg');
        },
    
		validateIcon(iconName) {
			return new Promise((resolve) => {
				if (!iconName || !iconName.startsWith('si:')) {
					resolve(true); 
					return;
				}
				
				const simpleIconName = iconName.substring(3).toLowerCase();
				const img = new Image();
				const timeoutId = setTimeout(() => {
					img.src = ''; 
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
				img.src = `https://cdn.simpleicons.org/${simpleIconName}`;
			});
		},        checkIframeLoaded() {
            if (!this.$el || typeof this.$el.querySelector !== 'function') return;
            
            const iframe = this.$el.querySelector('iframe');
            if (!iframe || !this.config.iframeUrl) return;
            
            this.isLoading = true; // Use isLoading consistently
            
            const timeoutDuration = 5000;
            const loadTimeout = setTimeout(() => {
                if (iframe.offsetWidth > 0 && iframe.offsetHeight > 0) {
                    this.iframeError = false;
                } else {
                    this.iframeError = true;
                }
                this.isLoading = false;
            }, timeoutDuration);
            
            iframe.onload = () => {
                clearTimeout(loadTimeout);
                this.iframeError = false;
                this.isLoading = false;
            };
            
            // Fallback error handler for iframe
            iframe.onerror = () => {
                clearTimeout(loadTimeout);
                this.iframeError = true;
                this.isLoading = false;
            };

            const visibilityCheck = setInterval(() => {
                if (iframe.offsetWidth > 0 && iframe.offsetHeight > 0) {
                    clearInterval(visibilityCheck);
                    this.iframeError = false;
                    this.isLoading = false;
                }
            }, 500);
            
            setTimeout(() => clearInterval(visibilityCheck), timeoutDuration + 1000);
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

    	handleIframeError(event) {
    		console.warn('Failed to load iframe content: ', event);
    		this.iframeError = true;
            this.loading = false; // Ensure loading is false on error
		}
    }
}
</script>

<style scoped>
.iframewidget-container {
    width: 100%;
    height: 100%;
    min-height: 100px; /* Match admin widget */
    overflow: hidden;
    /* Added for consistency from admin widget */
    position: relative; 
    transition: all 0.3s ease;
    background: var(--color-main-background);
    border-radius: var(--border-radius);
}

.iframewidget-frame {
    width: 100%;
    border: none;
    overflow: hidden; /* Match admin widget */
    min-height: 200px; /* Ensure a minimum height */
    background: var(--color-main-background); /* Match admin widget */
}

.widget-loading,
.widget-error,
.widget-empty {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 200px; /* Match admin widget */
    width: 100%;
    color: var(--color-text-maxcontrast); /* Match admin widget */
	text-align: center;
    flex-direction: column; /* Match admin widget */
    /* From personal widget original */
    padding: 20px; 
    color: var(--color-text-lighter); /* This was different, aligning with admin iframewidget's maxcontrast */
}

/* Added for consistency */
.widget-loading .icon-loading {
    width: 32px;
    height: 32px;
}

.widget-error.csp-error {
    display: flex;
    flex-direction: column;
    padding: 16px;
    text-align: center;
    /* Added from personal widget original, if different from admin */
    background-color: var(--color-background-dark);
    border-radius: var(--border-radius);
}

.error-title { /* Combined from both */
    font-weight: bold;
    margin-bottom: 8px;
    color: var(--color-text-maxcontrast);
}

.widget-error p { /* From admin widget */
    margin: 8px 0;
    color: var(--color-text-maxcontrast);
}

.error-actions { /* Combined from both */
    margin-top: 16px;
}

.error-actions .button { /* Combined from both */
    display: inline-block;
    padding: 8px 16px;
    text-decoration: none;
    border-radius: var(--border-radius);
    background-color: var(--color-primary);
    color: var(--color-primary-text);
}

/* Added from personal for hover, if desired */
.error-actions .button:hover {
    background-color: var(--color-primary-hover);
}

/* Global styles for panel manipulation, similar to admin widget */
:global(.panel.ifw-widget-extra-wide[data-widget-id="personal-iframewidget"]) {
    grid-column: span 2 !important;
    width: calc(100% - var(--default-grid-gap)) !important;
}

:global(.panel.ifw-title-empty[data-widget-id="personal-iframewidget"] .app-popover-menu-utils) {
    top: 0;
}

/* Ensure dashboard icon styling is available */
:global(.panel--header h2 .widget-icon) {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin-right: 8px !important;
    vertical-align: middle !important;
}

:global(.panel--header h2 .widget-icon .dashboard-icon) {
    width: 32px !important;
    height: 32px !important;
    object-fit: contain !important;
}

/* Fix title alignment */
:global(.panel[data-widget-id="personal-iframewidget"] .panel--header h2) {
    display: flex !important;
    align-items: center !important;
    text-align: left !important;
    justify-content: flex-start !important;
}
</style>
