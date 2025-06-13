<?php

declare(strict_types=1);

namespace OCA\IframeWidget\Settings;

use OCA\IframeWidget\AppInfo\Application;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Services\IInitialState;
use OCP\IConfig;
use OCP\Settings\ISettings;
use OCP\IUserSession;

class Personal implements ISettings {
    private IConfig $config;
    private IUserSession $userSession;
    private IInitialState $initialStateService;

    public function __construct(
        IConfig $config,
        IUserSession $userSession,
        IInitialState $initialStateService
    ) {
        $this->config = $config;
        $this->userSession = $userSession;
        $this->initialStateService = $initialStateService;
    }

    public function getForm(): TemplateResponse {
        $userId = $this->userSession->getUser()->getUID();

        $this->initialStateService->provideInitialState('personal-widget-config', [
            'widgetTitle' => $this->config->getUserValue($userId, Application::APP_ID, 'personal_widget_title', ''),
            'widgetIcon' => $this->config->getUserValue($userId, Application::APP_ID, 'personal_widget_icon', 'icon-iframe'),
            'iframeUrl' => $this->config->getUserValue($userId, Application::APP_ID, 'personal_iframe_url', ''),
            'extraWide' => $this->config->getUserValue($userId, Application::APP_ID, 'personal_extra_wide', '0') === '1',
        ]);

        return new TemplateResponse(Application::APP_ID, 'personalSettings');
    }

    public function getSection(): string {
        return 'personal-settings';
    }

    public function getPriority(): int {
        return 10;
    }
}
