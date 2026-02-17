<template>
    <div class="iframewidget-container"
         :style="{ visibility: configLoaded ? 'visible' : 'hidden' }"
         :data-widget-id="widgetId">

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
            <div>{{ t('iframewidget', 'No URL configured for this group.') }}</div>
            <div>
                {{ t('iframewidget', 'Please configure group widgets in the') }}
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
import { loadState } from '@nextcloud/initial-state'

export default {
    name: 'IndividualGroupWidget',
    props: {
        groupId: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            loading: true,
            configLoaded: false,
            error: false,
            iframeError: false,
            config: {}
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
         * Widget ID for this group
         */
        widgetId() {
            return 'group-iframewidget-' + this.groupId;
        },

        /**
         * Determine if widget should be displayed in extra wide mode
         */
        isExtraWide() {
            return this.config.extraWide === 'true' || this.config.extraWide === true;
        },

        /**
         * Calculate iframe height based on configuration
         */
        iframeHeight() {
            if (!this.config.iframeHeight || this.config.iframeHeight === '0') {
                return '100%';
            }
            return parseInt(this.config.iframeHeight) + 'px';
        }
    },
    methods: {
        /**
         * Initialize the widget
         */
        async init() {
            try {
                this.loading = true;
                this.error = false;

                // Load group configuration from initial state
                const stateKey = 'group-iframewidget-' + this.groupId + '-config';
                this.config = loadState('iframewidget', stateKey) || {};

                this.configLoaded = true;
            } catch (err) {
                console.error('Failed to initialize group widget for group:', this.groupId, err);
                this.error = true;
            } finally {
                this.loading = false;
            }
        },

        /**
         * Handle iframe loading errors
         */
        handleIframeError() {
            this.iframeError = true;
        }
    },
    mounted() {
        this.init();
    }
}

</script>

<style scoped>
.iframewidget-container {
    width: 100%;
    min-height: 150px;
}

.widget-loading {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100px;
}

.widget-error {
    padding: 10px;
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
    border-radius: 3px;
    text-align: center;
}

.widget-empty {
    padding: 20px;
    text-align: center;
    color: #666;
}

.csp-error {
    background: #fff3cd;
    color: #856404;
    border-color: #ffeaa7;
}

.error-title {
    font-weight: bold;
    margin-bottom: 5px;
}

.error-actions {
    margin-top: 10px;
}

.button {
    display: inline-block;
    padding: 6px 12px;
    background: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 3px;
    font-size: 12px;
}

.button:hover {
    background: #0056b3;
}

.iframewidget-frame {
    width: 100%;
    border: none;
    border-radius: 3px;
}
</style>
