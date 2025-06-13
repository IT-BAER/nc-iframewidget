<?php

declare(strict_types=1);

namespace OCA\IframeWidget\Dashboard;

use OCA\IframeWidget\AppInfo\Application;
use OCP\Dashboard\IWidget;
use OCP\IConfig;
use OCP\IL10N;
use OCP\IUserSession;
use OCP\AppFramework\Services\IInitialState;

class PersonalIframeWidget implements IWidget {
    private IL10N $l10n;
    private IConfig $config;
    private IInitialState $initialStateService;
    private IUserSession $userSession;

    public function __construct(
        IL10N $l10n,
        IConfig $config,
        IInitialState $initialStateService,
        IUserSession $userSession
    ) {
        $this->l10n = $l10n;
        $this->config = $config;
        $this->initialStateService = $initialStateService;
        $this->userSession = $userSession;
    }

    public function getId(): string {
        return 'personal-iframewidget';
    }

    public function getTitle(): string {
        $userId = $this->userSession->getUser()->getUID();
        $title = $this->config->getUserValue($userId, Application::APP_ID, 'personal_widget_title', '');
        // Ensure a default title is provided if the user hasn't set one, to prevent empty title styling issues.
        return empty(trim($title)) ? $this->l10n->t('Personal iFrame') : $title;
    }

    public function getOrder(): int {
        return 10;
    }

    public function getIconClass(): string {
        $userId = $this->userSession->getUser()->getUID();
        $icon = $this->config->getUserValue($userId, Application::APP_ID, 'personal_widget_icon', '');
        $color = $this->config->getUserValue($userId, Application::APP_ID, 'personal_widget_icon_color', '');
        
        // Use default icon if none set
        if (empty($icon)) {
            return 'icon-iframe';
        }
        
        // Semantic UI icons (si:) or custom icons
        if (str_starts_with($icon, 'si:')) {
            return !empty($color) ? $icon . ' ' . $color : $icon;
        }
        
        // For regular icons (icon-*), append icon-loading class
        if (str_starts_with($icon, 'icon-')) {
            return !empty($color) ? $icon . ' ' . $color . ' icon-loading' : $icon . ' icon-loading';
        }
        
        // Default with color if set
        return !empty($color) ? $icon . ' ' . $color : $icon;
    }

    public function getUrl(): ?string {
        return null;
    }

    public function load(): void {
        $userId = $this->userSession->getUser()->getUID();
        
        // Get widget settings
        $rawExtraWide = $this->config->getUserValue($userId, Application::APP_ID, 'personal_extra_wide', 'false');
        $widgetTitle = $this->config->getUserValue($userId, Application::APP_ID, 'personal_widget_title', '');
        $widgetIcon = $this->config->getUserValue($userId, Application::APP_ID, 'personal_widget_icon', '');
        $widgetIconColor = $this->config->getUserValue($userId, Application::APP_ID, 'personal_widget_icon_color', '');
        $iframeUrl = $this->config->getUserValue($userId, Application::APP_ID, 'personal_iframe_url', '');
        $iframeHeight = $this->config->getUserValue($userId, Application::APP_ID, 'personal_iframe_height', '');
        
        // Ensure extraWide is a string 'true' or 'false' for consistency with how it's handled in JS
        $extraWide = ($rawExtraWide === '1' || $rawExtraWide === 'true' || $rawExtraWide === true) ? 'true' : 'false';
        
        // Initialize config with all required fields
        $config = [
            'extraWide' => $extraWide,
            // Use the result of getTitle() to ensure default is applied if necessary
            'widgetTitle' => $this->getTitle(), 
            'widgetIcon' => $widgetIcon,
            'widgetIconColor' => $widgetIconColor,
            'iframeUrl' => $iframeUrl,
            'iframeHeight' => $iframeHeight ?: '0' // Use 0 for auto/100% height
        ];
        
        // Provide initial state
        $this->initialStateService->provideInitialState('personal-iframewidget-config', $config);
    }
}
