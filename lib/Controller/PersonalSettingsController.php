<?php

declare(strict_types=1);

namespace OCA\IframeWidget\Controller;

use OCA\IframeWidget\AppInfo\Application;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IConfig;
use OCP\IRequest;
use OCP\IUserSession;

class PersonalSettingsController extends Controller {
    private IConfig $config;
    private IUserSession $userSession;

    public function __construct(
        string $appName,
        IRequest $request,
        IConfig $config,
        IUserSession $userSession
    ) {
        parent::__construct($appName, $request);
        $this->config = $config;
        $this->userSession = $userSession;
    }

    /**
     * @NoAdminRequired
     */
    public function getSettings(): JSONResponse {
        $userId = $this->userSession->getUser()->getUID();

        return new JSONResponse([
            'widgetTitle' => $this->config->getUserValue($userId, Application::APP_ID, 'personal_widget_title', ''),
            'widgetIcon' => $this->config->getUserValue($userId, Application::APP_ID, 'personal_widget_icon', 'icon-iframe'),
            'iframeUrl' => $this->config->getUserValue($userId, Application::APP_ID, 'personal_iframe_url', ''),
            'extraWide' => $this->config->getUserValue($userId, Application::APP_ID, 'personal_extra_wide', '0') === '1',
        ]);
    }

    /**
     * @NoAdminRequired
     */
    public function setSettings(string $widgetTitle = '', string $widgetIcon = '', string $iframeUrl = '', bool $extraWide = false): JSONResponse {
        $userId = $this->userSession->getUser()->getUID();

        $this->config->setUserValue($userId, Application::APP_ID, 'personal_widget_title', $widgetTitle);
        $this->config->setUserValue($userId, Application::APP_ID, 'personal_widget_icon', $widgetIcon);
        $this->config->setUserValue($userId, Application::APP_ID, 'personal_iframe_url', $iframeUrl);
        $this->config->setUserValue($userId, Application::APP_ID, 'personal_extra_wide', $extraWide ? '1' : '0');

        return new JSONResponse(['status' => 'success']);
    }
}
