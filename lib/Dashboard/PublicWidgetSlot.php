<?php

declare(strict_types=1);

namespace OCA\IframeWidget\Dashboard;

use OCA\IframeWidget\AppInfo\Application;
use OCP\Dashboard\IWidget;
use OCP\Dashboard\IConditionalWidget;
use OCP\IConfig;
use OCP\IGroupManager;
use OCP\IL10N;
use OCP\IUserSession;
use OCP\AppFramework\Services\IInitialState;
use OCP\Security\IContentSecurityPolicyManager;
use OCA\IframeWidget\Service\CspPolicyHelper;

/**
 * Base class for public iFrame widget slots
 * 
 * Implements IConditionalWidget to hide unconfigured slots from the dashboard selector.
 * Each slot (1-5) is a separate widget that can be configured independently.
 */
abstract class PublicWidgetSlot implements IWidget, IConditionalWidget
{
    protected IL10N $l10n;
    protected IConfig $config;
    protected IInitialState $initialStateService;
    protected IContentSecurityPolicyManager $cspManager;
    protected CspPolicyHelper $cspPolicyHelper;
    
    /** @var int The slot number (1-5) */
    protected int $slotNumber;

    public function __construct(
        IL10N $l10n,
        IConfig $config,
        IInitialState $initialStateService,
        IContentSecurityPolicyManager $cspManager,
        CspPolicyHelper $cspPolicyHelper
    ) {
        $this->l10n = $l10n;
        $this->config = $config;
        $this->initialStateService = $initialStateService;
        $this->cspManager = $cspManager;
        $this->cspPolicyHelper = $cspPolicyHelper;
    }

    /**
     * Get the slot number - must be implemented by subclasses
     */
    abstract protected function getSlotNumber(): int;

    /**
     * @return string Unique id that identifies the widget
     */
    public function getId(): string
    {
        $slot = $this->getSlotNumber();
        // First slot keeps original ID for backward compatibility
        return $slot === 1 ? 'iframewidget' : 'iframewidget-' . $slot;
    }

    /**
     * @return string title for the widget
     */
    public function getTitle(): string
    {
        $widgetConfig = $this->getWidgetConfig();
        if ($widgetConfig && !empty(trim($widgetConfig['title'] ?? ''))) {
            return $widgetConfig['title'];
        }
        
        $slot = $this->getSlotNumber();
        return $slot === 1 
            ? $this->l10n->t('Public iFrame') 
            : $this->l10n->t('Public iFrame') . ' ' . $slot;
    }

    /**
     * @return int Initial order for widget sorting
     */
    public function getOrder(): int
    {
        return 10 + $this->getSlotNumber();
    }

    /**
     * @return string css class that displays an icon next to the widget title
     */
    public function getIconClass(): string
    {
        return 'icon-iframewidget';
    }

    /**
     * @return string|null The absolute url to the apps own view
     */
    public function getUrl(): ?string
    {
        return null;
    }

    /**
     * @return bool
     */
    public function isVisible(\OCP\IUser $user = null): bool
    {
        return $this->isEnabled();
    }

    /**
     * Check if this widget slot is enabled (has configuration AND passed enabled check)
     * 
     * This hides unconfigured or disabled slots from the dashboard widget selector.
     * @return bool
     */
    public function isEnabled(\OCP\IUser $user = null): bool
    {
        $widgetConfig = $this->getWidgetConfig();
        if ($widgetConfig === null || empty($widgetConfig['url'])) {
            return false;
        }
        return $widgetConfig['enabled'] ?? true;
    }

    /**
     * Execute widget bootstrap code like loading scripts and providing initial state
     */
    public function load(): void
    {
        $widgetConfig = $this->getWidgetConfig();
        
        $config = [
            'slotNumber' => $this->getSlotNumber(),
            'extraWide' => $widgetConfig['extraWide'] ?? false,
            'widgetTitle' => $widgetConfig['title'] ?? '',
            'widgetIcon' => $widgetConfig['icon'] ?? '',
            'widgetIconColor' => $widgetConfig['iconColor'] ?? '',
            'iframeHeight' => $widgetConfig['height'] ?? '',
            'iframeUrl' => $widgetConfig['url'] ?? '',
            'iframeSandbox' => $widgetConfig['iframeSandbox'] ?? 'allow-same-origin allow-scripts allow-popups allow-forms',
            'iframeAllow' => $widgetConfig['iframeAllow'] ?? ''
        ];

        // Provide initial state with slot-specific key
        $stateKey = $this->getSlotNumber() === 1 
            ? 'widget-config' 
            : 'widget-config-' . $this->getSlotNumber();
        $this->initialStateService->provideInitialState($stateKey, $config);

        $this->cspPolicyHelper->addDashboardPolicyForUser(null, $this->cspManager);

        // Load scripts and styles
        \OCP\Util::addScript('iframewidget', 'iframewidget-dashboard');
        \OCP\Util::addStyle('iframewidget', 'dashboard');
    }

    /**
     * Get the widget configuration for this slot
     * 
     * @return array|null Widget config or null if not configured
     */
    protected function getWidgetConfig(): ?array
    {
        $slot = $this->getSlotNumber();
        
        // Check new JSON storage FIRST for multiple public widgets
        // This takes precedence over legacy config to ensure 'enabled' status is respected
        $jsonWidgets = $this->config->getAppValue(Application::APP_ID, 'publicWidgetsJson', '');
        if (!empty($jsonWidgets)) {
            $widgets = json_decode($jsonWidgets, true);
            if (is_array($widgets)) {
                // Find widget assigned to this slot
                foreach ($widgets as $widget) {
                    if (($widget['slot'] ?? 0) === $slot) {
                        return $widget;
                    }
                }
            }
        }
        
        // Fallback for slot 1: check legacy single-widget config for backward compatibility
        if ($slot === 1) {
            $legacyUrl = $this->config->getAppValue(Application::APP_ID, 'iframeUrl', '');
            if (!empty($legacyUrl)) {
                return [
                    'id' => 'public-1',
                    'title' => $this->config->getAppValue(Application::APP_ID, 'widgetTitle', ''),
                    'icon' => $this->config->getAppValue(Application::APP_ID, 'widgetIcon', ''),
                    'iconColor' => $this->config->getAppValue(Application::APP_ID, 'widgetIconColor', ''),
                    'url' => $legacyUrl,
                    'height' => $this->config->getAppValue(Application::APP_ID, 'iframeHeight', ''),
                    'extraWide' => $this->config->getAppValue(Application::APP_ID, 'extraWide', 'false') === 'true',
                    'iframeSandbox' => $this->config->getAppValue(Application::APP_ID, 'iframeSandbox', 'allow-same-origin allow-scripts allow-popups allow-forms'),
                    'iframeAllow' => $this->config->getAppValue(Application::APP_ID, 'iframeAllow', '')
                ];
            }
        }

        return null;
    }
}
