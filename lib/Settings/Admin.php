<?php

namespace OCA\IframeWidget\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\IL10N;
use OCP\IConfig;
use OCP\Settings\ISettings;
use OCP\AppFramework\Services\IInitialState;

use OCA\IframeWidget\AppInfo\Application;

class Admin implements ISettings
{
    /** @var IConfig */
    private $config;

    /** @var IL10N */
    private $l;

    /** @var IInitialState */
    private $initialStateService;

    /**
     * Admin constructor.
     */
    public function __construct(
        IConfig $config,
        IL10N $l,
        IInitialState $initialStateService
    ) {
        $this->config = $config;
        $this->l = $l;
        $this->initialStateService = $initialStateService;
    }

    /**
     * Return admin settings template
     * 
     * @return TemplateResponse
     */
    public function getForm(): TemplateResponse
    {
        // Load settings from config
        $widgetTitle = $this->config->getAppValue(Application::APP_ID, 'widgetTitle', 'iFrame Widget');
        $widgetIcon = $this->config->getAppValue(Application::APP_ID, 'widgetIcon', '');
        $widgetIconColor = $this->config->getAppValue(Application::APP_ID, 'widgetIconColor', '');
        $extraWide = $this->config->getAppValue(Application::APP_ID, 'extraWide', false);
        $maxSize = $this->config->getAppValue(Application::APP_ID, 'maxSize', false);
        $iframeUrl = $this->config->getAppValue(Application::APP_ID, 'iframeUrl', '');
        $iframeHeight = $this->config->getAppValue(Application::APP_ID, 'iframeHeight', '');

        // Set initial state for frontend
        $adminConfig = [
            'widgetTitle' => $widgetTitle,
            'widgetIcon' => $widgetIcon,
            'widgetIconColor' => $widgetIconColor,
            'extraWide' => $extraWide,
            'maxSize' => $maxSize,
            'iframeUrl' => $iframeUrl,
            'iframeHeight' => $iframeHeight,
        ];
        
        $this->initialStateService->provideInitialState('admin-config', $adminConfig);

    	// Add SimpleIcons to Content Security Policy
    	$cspManager = \OC::$server->getContentSecurityPolicyManager();
    	$policy = new \OCP\AppFramework\Http\ContentSecurityPolicy();
    	$policy->addAllowedImageDomain('cdn.simpleicons.org');
    	$cspManager->addDefaultPolicy($policy);
    
        // Add dashboard styles for preview
        \OCP\Util::addStyle('iframewidget', 'dashboard');
        
        return new TemplateResponse(Application::APP_ID, 'adminSettings');
    }

    /**
     * @return string
     */
    public function getSection(): string
    {
        return 'iframewidget';
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return 10;
    }
}
