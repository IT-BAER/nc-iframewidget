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
            isLoading: false,
            error: false,
            iframeError: false,
            config: loadState('iframewidget', 'personal-widget-config') || {
                extraWide: false,
                widgetTitle: '',
                widgetIcon: '',
                widgetIconColor: '',
                iframeUrl: '',
                iframeHeight: ''
            },
            observer: null
        }
    },
    computed: {
        settingsUrl() {
            return OC.generateUrl('/settings/user/iframewidget')
        },
        isExtraWide() {
            return this.config.extraWide === true || this.config.extraWide === 'true'
        },
        iframeHeight() {
            return (!this.config.iframeHeight || this.config.iframeHeight === '0') 
                ? '100%' 
                : parseInt(this.config.iframeHeight) + 'px'
        },
        widgetTitleEmpty() {
            return !this.config.widgetTitle || this.config.widgetTitle.trim() === ''
        }
    },
    mounted() {
        this.$nextTick(() => {
            this.applyPanelClasses()
            
            if (this.config.extraWide !== undefined) {
                this.configLoaded = true
            }
        })

        // Setup a mutation observer to watch for dashboard changes
        this.observer = new MutationObserver(() => {
            this.applyPanelClasses()
        })

        // Start observing once the component is mounted
        setTimeout(() => {
            const dashboard = document.querySelector('.app-dashboard')
            if (dashboard) {
                this.observer.observe(dashboard, { 
                    childList: true,
                    subtree: false,
                    attributeFilter: ['class']
                })
            }
        }, 500)

        // Check if iframe loads correctly after a timeout
        if (this.config.iframeUrl) {
            setTimeout(() => {
                this.checkIframeLoaded()
            }, 3000)
        }
        
        // Listen for actual CSP errors in the console
        window.addEventListener('securitypolicyviolation', this.handleCSPViolation)
    },
    beforeDestroy() {
        if (this.observer) {
            this.observer.disconnect()
        }
        window.removeEventListener('securitypolicyviolation', this.handleCSPViolation)
    },
    watch: {
        'config.iframeUrl': function(newUrl, oldUrl) {
            if (newUrl !== oldUrl) {
                this.iframeError = false

                // Attempt to reload iframe with new URL
                this.$nextTick(() => {
                    const iframe = this.$el.querySelector('iframe')
                    if (iframe) {
                        // Force reload by updating src
                        iframe.src = newUrl
                    }
                })
            }
        }
    },
    methods: {
        handleCSPViolation(e) {
            if (e.blockedURI && this.config.iframeUrl && 
                (e.blockedURI === this.config.iframeUrl || 
                this.config.iframeUrl.startsWith(e.blockedURI))) {
                this.iframeError = true
            }
        },
        applyPanelClasses() {
            if (!this.$el || typeof this.$el.closest !== 'function') return
            
            const parentPanel = this.$el.closest('.panel')
            if (parentPanel) {
                parentPanel.classList.toggle('ifw-widget-extra-wide', this.isExtraWide)
                parentPanel.classList.toggle('ifw-title-empty', this.widgetTitleEmpty)
                parentPanel.setAttribute('data-widget-id', 'personal-iframewidget')
            }
        },
        handleIframeError() {
            this.iframeError = true
        },
        checkIframeLoaded() {
            const iframe = this.$el.querySelector('iframe')
            if (iframe && !this.iframeError) {
                try {
                    // Try to access iframe content to check if it loaded
                    const iframeDoc = iframe.contentDocument || iframe.contentWindow.document
                    if (!iframeDoc) {
                        this.iframeError = true
                    }
                } catch (e) {
                    // CSP or other security error
                    this.iframeError = true
                }
            }
        }
    }
}
</script>

<style scoped>
.iframewidget-container {
    position: relative;
    width: 100%;
    min-height: 200px;
    transition: all 0.3s ease;
    background: var(--color-main-background);
    border-radius: var(--border-radius);
    overflow: hidden;
}

.iframewidget-container.ifw-widget-extra-wide {
    width: calc(200% + var(--grid-gap));
}

.iframewidget-frame {
    width: 100%;
    min-height: 200px;
    border: none;
    background: var(--color-main-background);
}

.widget-loading,
.widget-error,
.widget-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 200px;
    padding: 20px;
    text-align: center;
    color: var(--color-text-lighter);
}

.widget-loading .icon-loading {
    width: 32px;
    height: 32px;
}

.error-title {
    font-weight: bold;
    margin-bottom: 8px;
}

.error-actions {
    margin-top: 16px;
}

.error-actions .button {
    display: inline-block;
    padding: 8px 16px;
    background-color: var(--color-primary);
    color: var(--color-primary-text);
    border-radius: var(--border-radius);
    text-decoration: none;
}

.error-actions .button:hover {
    background-color: var(--color-primary-hover);
}

.csp-error {
    background-color: var(--color-background-dark);
    border-radius: var(--border-radius);
    padding: 20px;
}

/**
 * Panel wrapper classes
 * These are applied to the parent panel by applyPanelClasses()
 */
:global(.panel.ifw-widget-extra-wide) {
    grid-column: span 2 !important;
}

:global(.panel.ifw-title-empty .app-popover-menu-utils) {
    top: 0;
}
</style>
