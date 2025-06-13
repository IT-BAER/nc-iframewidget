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
            config: loadState('iframewidget', 'personal-iframewidget-config') || {
                extraWide: false,
                widgetTitle: 'Personal iFrame Widget',
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
    methods: {
        applyPanelClasses() {
            const panel = this.$el.closest('.panel')
            if (panel) {
                if (this.config.widgetIcon) {
                    const iconColor = this.config.widgetIconColor || ''
                    panel.style.setProperty('--ifw-icon-color', iconColor)
                }
            }
        },
        handleIframeError() {
            this.iframeError = true
            this.loading = false
        }
    },
    mounted() {
        this.$nextTick(() => {
            this.configLoaded = this.config && this.config.extraWide !== undefined
            this.loading = false
            this.applyPanelClasses()
            
            // Create observer to reapply classes if DOM changes
            this.observer = new MutationObserver(() => {
                this.applyPanelClasses()
            })
            
            // Start observing DOM changes
            const panel = this.$el.closest('.panel')
            if (panel) {
                this.observer.observe(panel, { 
                    attributes: true, 
                    childList: true, 
                    subtree: true 
                })
            }
        })
    },
    beforeDestroy() {
        if (this.observer) {
            this.observer.disconnect()
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
