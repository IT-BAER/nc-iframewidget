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
    /** @var IL10N */
    private IL10N $l10n;
    
    /** @var IConfig */
    private IConfig $config;
    
    /** @var IInitialState */
    private IInitialState $initialStateService;

    /** @var IUserSession */
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
        return $title ?: $this->l10n->t('Personal iFrame');
    }

    public function getOrder(): int {
        return 10;
    }

    public function getIconClass(): string {
        $userId = $this->userSession->getUser()->getUID();
        $icon = $this->config->getUserValue($userId, Application::APP_ID, 'personal_widget_icon', 'icon-iframe');
        $color = $this->config->getUserValue($userId, Application::APP_ID, 'personal_widget_icon_color', '');
        
        if ($icon && str_starts_with($icon, 'si:') && $color) {
            $simpleIconName = substr($icon, 3);
            $colorValue = ltrim($color, '#');
            return "si:{$simpleIconName}?color={$colorValue}";
        }
        
        return $icon ?: 'icon-iframe';
    }

    public function getUrl(): ?string {
        return null;
    }

    public function load(): void {
        $userId = $this->userSession->getUser()->getUID();
        
        // Convert the extra_wide value to a proper boolean
        $extraWide = $this->config->getUserValue($userId, Application::APP_ID, 'personal_extra_wide', '0');
        $extraWide = ($extraWide === '1' || $extraWide === 'true' || $extraWide === true);
        
        $this->initialStateService->provideInitialState('personal-iframewidget-config', [
            'iframeUrl' => $this->config->getUserValue($userId, Application::APP_ID, 'personal_iframe_url', ''),
            'widgetTitle' => $this->getTitle(),
            'widgetIcon' => $this->config->getUserValue($userId, Application::APP_ID, 'personal_widget_icon', ''),
            'widgetIconColor' => $this->config->getUserValue($userId, Application::APP_ID, 'personal_widget_icon_color', ''),
            'extraWide' => $extraWide,
        ]);
    }
}
