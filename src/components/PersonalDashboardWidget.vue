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
                extraWide: false,
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
            return this.config.extraWide
        }
    },
    async created() {
        try {
            this.config = await loadState('iframewidget', 'personal-iframewidget-config')
            this.configLoaded = true
        } catch (e) {
            console.error('Failed to load personal iFrame widget config:', e)
            this.error = true
        } finally {
            this.loading = false
        }
    },
    methods: {
        handleIframeError() {
            this.iframeError = true
        }
    }
}
</script>

<style scoped>
.iframewidget-container {
    position: relative;
    min-height: 200px;
    height: 100%;
}

.iframewidget-frame {
    width: 100%;
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
    height: 100%;
    min-height: 200px;
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

.csp-error {
    color: var(--color-error);
}

.ifw-widget-extra-wide {
    grid-column: span 2;
}

.ifw-title-empty {
    height: 0;
    min-height: 0;
    margin: 0;
    padding: 0;
}
</style>
