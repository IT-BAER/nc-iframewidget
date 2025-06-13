<template>
    <div class="iframewidget-container" 
         :class="{'ifw-widget-extra-wide': isExtraWide, 'ifw-title-empty': widgetTitleEmpty}"
         :style="{ visibility: configLoaded ? 'visible' : 'hidden' }"
         data-widget-id="personal-iframewidget">
    
        <!-- Header with icon and title -->
        <div v-if="config.widgetTitle || config.widgetIcon" class="widget-header">
            <div v-if="config.widgetIcon" class="widget-icon" :style="iconStyle"></div>
            <h3 v-if="config.widgetTitle" class="widget-title">{{ config.widgetTitle }}</h3>
        </div>
    
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
            error: false,
            iframeError: false,
            config: {
                iframeUrl: '',
                widgetTitle: '',
                widgetIcon: '',
                widgetIconColor: '',
                extraWide: false
            },
            configLoaded: false,
            iframeHeight: '100%'
        }
    },
    computed: {
        widgetTitleEmpty() {
            return !this.config.widgetTitle || this.config.widgetTitle.trim() === ''
        },
        settingsUrl() {
            return generateUrl('/settings/user/iframewidget')
        },
        isExtraWide() {
            // Handle both string and boolean values
            return this.config.extraWide === true || this.config.extraWide === 'true' || this.config.extraWide === '1'
        },
        iconStyle() {
            if (this.config.widgetIcon && this.config.widgetIcon.startsWith('si:')) {
                const iconName = this.config.widgetIcon.substring(3).toLowerCase()
                let iconUrl = `https://cdn.simpleicons.org/${iconName}`
                if (this.config.widgetIconColor) {
                    iconUrl += '/' + this.config.widgetIconColor.replace('#', '')
                }
                return {
                    backgroundImage: `url(${iconUrl})`,
                    backgroundSize: 'contain',
                    backgroundRepeat: 'no-repeat',
                    backgroundPosition: 'center'
                }
            }
            return {}
        }
    },
    async created() {
        try {
            const config = await loadState('iframewidget', 'personal-iframewidget-config')
            // Ensure all config values are properly set
            this.config = {
                iframeUrl: config.iframeUrl || '',
                widgetTitle: config.widgetTitle || '',
                widgetIcon: config.widgetIcon || '',
                widgetIconColor: config.widgetIconColor || '',
                extraWide: config.extraWide === true || config.extraWide === 'true' || config.extraWide === '1'
            }
            this.configLoaded = true

            // Force an update to ensure all UI elements reflect the config
            this.$nextTick(() => {
                this.$forceUpdate()
            })
        } catch (e) {
            console.error('Failed to load personal iFrame widget config:', e)
            this.error = true
        } finally {
            this.loading = false
        }
    },
    mounted() {
        // Add a mutation observer to handle dynamic class changes
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    this.updateExtraWideClass()
                }
            })
        })

        if (this.$el) {
            observer.observe(this.$el, {
                attributes: true
            })
        }

        // Initial class update
        this.updateExtraWideClass()
    },
    beforeDestroy() {
        // Clean up mutation observer if it exists
        if (this.observer) {
            this.observer.disconnect()
        }
    },
    methods: {
        handleIframeError() {
            this.iframeError = true
            console.error('IFrame loading error')
        },
        updateExtraWideClass() {
            if (this.$el) {
                const shouldBeWide = this.isExtraWide
                const hasClass = this.$el.classList.contains('ifw-widget-extra-wide')
                
                if (shouldBeWide && !hasClass) {
                    this.$el.classList.add('ifw-widget-extra-wide')
                } else if (!shouldBeWide && hasClass) {
                    this.$el.classList.remove('ifw-widget-extra-wide')
                }

                // Update grid column span
                if (shouldBeWide) {
                    this.$el.style.gridColumn = 'span 2'
                } else {
                    this.$el.style.gridColumn = ''
                }
            }
        }
    },
    watch: {
        'config.extraWide': {
            handler() {
                this.updateExtraWideClass()
            },
            immediate: true
        },
        'config.widgetIcon': {
            handler() {
                this.$forceUpdate()
            },
            immediate: true
        },
        'config.widgetIconColor': {
            handler() {
                this.$forceUpdate()
            },
            immediate: true
        }
    }
}
</script>

<style scoped>
.iframewidget-container {
    position: relative;
    width: 100%;
    transition: all 0.3s ease;
    background: var(--color-main-background);
    border-radius: var(--border-radius);
    overflow: hidden;
}

.widget-header {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    background: var(--color-main-background);
    border-bottom: 1px solid var(--color-border);
}

.widget-icon {
    width: 24px;
    height: 24px;
    margin-right: 8px;
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
}

.widget-title {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
    color: var(--color-main-text);
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

.ifw-widget-extra-wide {
    grid-column: span 2;
}

.ifw-title-empty {
    .widget-header {
        display: none;
    }
}

/* Dark mode adjustments */
@media (prefers-color-scheme: dark) {
    .widget-header {
        background: var(--color-background-dark);
    }
}
</style>
