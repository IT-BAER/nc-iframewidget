<?php

declare(strict_types=1);

namespace OCA\IframeWidget\Dashboard;

use OCA\IframeWidget\AppInfo\Application;
use OCP\Dashboard\IWidget;
use OCP\IConfig;
use OCP\IL10N;
use OCP\AppFramework\Services\IInitialState;

/**
 * IframeWidget implements the dashboard widget functionality
 * 
 * This class registers and configures the iFrame widget for the
 * Nextcloud dashboard, handling configuration, icon display, and
 * content security policy adjustments.
 */
class IframeWidget implements IWidget
{
    /** @var IL10N */
    private IL10N $l10n;
    
    /** @var IConfig */
    private IConfig $config;
    
    /** @var IInitialState */
    private IInitialState $initialStateService;

    /**
     * Constructor for IframeWidget
     */
    public function __construct(
        IL10N $l10n,
        IConfig $config,
        IInitialState $initialStateService
    ) {
        $this->l10n = $l10n;
        $this->config = $config;
        $this->initialStateService = $initialStateService;
    }

    /**
     * @return string Unique id that identifies the widget
     */
    public function getId(): string
    {
        return 'iframewidget';
    }

    /**
     * @return string title for the widget
     */
    public function getTitle(): string
    {
        // Return configured title if set, otherwise return friendly picker name
        $widgetTitle = $this->config->getAppValue(Application::APP_ID, 'widgetTitle', '');
        return empty(trim($widgetTitle)) ? $this->l10n->t('Public iFrame') : $widgetTitle;
    }

    /**
     * @return int Initial order for widget sorting
     */
    public function getOrder(): int
    {
        return 0;
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
     * Execute widget bootstrap code like loading scripts and providing initial state
     */
    public function load(): void
    {
        // Get widget settings
        $extraWide = $this->config->getAppValue(Application::APP_ID, 'extraWide', 'false');
        $widgetTitle = $this->config->getAppValue(Application::APP_ID, 'widgetTitle', '');
        $widgetIcon = $this->config->getAppValue(Application::APP_ID, 'widgetIcon', '');
        $widgetIconColor = $this->config->getAppValue(Application::APP_ID, 'widgetIconColor', '');
        $iframeHeight = $this->config->getAppValue(Application::APP_ID, 'iframeHeight', '');
        $iframeUrl = $this->config->getAppValue(Application::APP_ID, 'iframeUrl', '');
        
        // Provide initial state directly
        $this->initialStateService->provideInitialState('widget-config', [
            'extraWide' => $extraWide,
            'widgetTitle' => $widgetTitle,
            'widgetIcon' => $widgetIcon,
            'widgetIconColor' => $widgetIconColor,
            'iframeHeight' => $iframeHeight,
            'iframeUrl' => $iframeUrl
        ]);
    
    	// Add SimpleIcons to Content Security Policy
    	$cspManager = \OC::$server->getContentSecurityPolicyManager();
    	$policy = new \OCP\AppFramework\Http\ContentSecurityPolicy();
    	$policy->addAllowedImageDomain('cdn.simpleicons.org');
    	$cspManager->addDefaultPolicy($policy);
    
        // Load scripts and styles
        \OCP\Util::addScript('iframewidget', 'iframewidget-dashboard');
        \OCP\Util::addStyle('iframewidget', 'dashboard');
    }
}
