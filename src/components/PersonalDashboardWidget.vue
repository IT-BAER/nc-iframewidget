<template>
    <div class="iframewidget-container" 
         :class="{'ifw-widget-extra-wide': isExtraWide}"
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
import { loadState } from '@nextcloud/initial-state'
import { generateUrl } from '@nextcloud/router'

export default {
    name: 'PersonalDashboardWidget',
    data() {
        return {
            loading: true,
            configLoaded: false,
            error: false,
            iframeError: false,
            config: loadState('iframewidget', 'personal-widget-config') || {
                extraWide: false,
                widgetTitle: '',
                widgetIcon: '',
                widgetIconColor: '',
                iframeUrl: ''
            },
            iframeHeight: '100%',
            observer: null
        }
    },
    computed: {
        settingsUrl() {
            return generateUrl('/settings/user/iframewidget')
        },
        isExtraWide() {
            return this.config.extraWide === true || this.config.extraWide === 'true'
        }
    },
    created() {
        try {
            this.configLoaded = true
        } catch (e) {
            console.error('Failed to load personal iFrame widget config:', e)
            this.error = true
        } finally {
            this.loading = false
        }
    },
    mounted() {
        this.setupResizeObserver()
    },
    beforeDestroy() {
        if (this.observer) {
            this.observer.disconnect()
        }
    },
    methods: {
        handleIframeError() {
            this.iframeError = true
            console.error('IFrame loading error')
        },
        setupResizeObserver() {
            if (this.isExtraWide && this.$el) {
                this.$el.style.gridColumn = 'span 2'
            }

            this.observer = new ResizeObserver(() => {
                if (this.$el && this.isExtraWide) {
                    this.$el.style.gridColumn = 'span 2'
                }
            })

            if (this.$el) {
                this.observer.observe(this.$el)
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

.ifw-widget-extra-wide {
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
</style>
