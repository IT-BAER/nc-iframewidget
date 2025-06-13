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
        return empty(trim($title)) ? '' : $title;
    }

    public function getOrder(): int {
        return 10;
    }

    public function getIconClass(): string {
        $userId = $this->userSession->getUser()->getUID();
        $icon = $this->config->getUserValue($userId, Application::APP_ID, 'personal_widget_icon', '');
        
        if (empty($icon)) {
            return 'icon-iframe';
        }
        
        if (str_starts_with($icon, 'si:')) {
            $color = $this->config->getUserValue($userId, Application::APP_ID, 'personal_widget_icon_color', '');
            if (!empty($color)) {
                return $icon . ' ' . $color;
            }
        }
        
        return $icon;
    }

    public function getUrl(): ?string {
        return null;
    }

    public function load(): void {
        $userId = $this->userSession->getUser()->getUID();
        
        // Get widget settings
        $extraWide = $this->config->getUserValue($userId, Application::APP_ID, 'personal_extra_wide', '0');
        $widgetTitle = $this->config->getUserValue($userId, Application::APP_ID, 'personal_widget_title', '');
        $widgetIcon = $this->config->getUserValue($userId, Application::APP_ID, 'personal_widget_icon', '');
        $widgetIconColor = $this->config->getUserValue($userId, Application::APP_ID, 'personal_widget_icon_color', '');
        $iframeUrl = $this->config->getUserValue($userId, Application::APP_ID, 'personal_iframe_url', '');
        
        // Convert boolean value
        $extraWide = ($extraWide === '1' || $extraWide === 'true');
        
        // Provide initial state
        $this->initialStateService->provideInitialState('personal-widget-config', [
            'extraWide' => $extraWide,
            'widgetTitle' => $widgetTitle,
            'widgetIcon' => $widgetIcon,
            'widgetIconColor' => $widgetIconColor,
            'iframeUrl' => $iframeUrl
        ]);
    }
}
