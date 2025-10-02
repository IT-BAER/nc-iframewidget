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
    }    public function getTitle(): string {
        // Return a friendly name for widget picker/selection
        // The actual widget title display is controlled by frontend config and CSS
        return $this->l10n->t('Personal iFrame');
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
        
        // For SimpleIcons (si:), we need to use a generic class and handle the icon in JavaScript
        // Never return the raw si: format as it's not a valid CSS class name
        if (str_starts_with($icon, 'si:')) {
            // Return a consistent class that CSS can target properly
            return 'icon-personal-iframewidget';
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
            // Pass the actual configured title (not the picker name)
            'widgetTitle' => $widgetTitle,
            'widgetIcon' => $widgetIcon,
            'widgetIconColor' => $widgetIconColor,
            'iframeUrl' => $iframeUrl,
            'iframeHeight' => $iframeHeight ?: '0' // Use 0 for auto/100% height
        ];
        
        // Provide initial state
        $this->initialStateService->provideInitialState('personal-iframewidget-config', $config);
    }
}
