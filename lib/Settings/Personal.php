<?php

declare(strict_types=1);

namespace OCA\IframeWidget\Settings;

use OCA\IframeWidget\AppInfo\Application;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Services\IInitialState;
use OCP\IConfig;
use OCP\Settings\IIconSection;
use OCP\IUserSession;
use OCP\IL10N;
use OCP\Settings\ISettings;
use OCP\Security\IContentSecurityPolicyManager;

use OCA\IframeWidget\Service\CspPolicyHelper;

class Personal implements ISettings {
    private IConfig $config;
    private IUserSession $userSession;
    private IInitialState $initialStateService;
    private IL10N $l;
    private IContentSecurityPolicyManager $cspManager;
    private CspPolicyHelper $cspPolicyHelper;

    public function __construct(
        IConfig $config,
        IUserSession $userSession,
        IInitialState $initialStateService,
        IL10N $l,
        IContentSecurityPolicyManager $cspManager,
        CspPolicyHelper $cspPolicyHelper
    ) {
        $this->config = $config;
        $this->userSession = $userSession;
        $this->initialStateService = $initialStateService;
        $this->l = $l;
        $this->cspManager = $cspManager;
        $this->cspPolicyHelper = $cspPolicyHelper;
    }

    public function getForm(): TemplateResponse {
        $userId = $this->userSession->getUser()->getUID();

        $this->initialStateService->provideInitialState('personal-iframewidget-config', [
            'widgetTitle' => $this->config->getUserValue($userId, Application::APP_ID, 'personal_widget_title', ''),
            'widgetIcon' => $this->config->getUserValue($userId, Application::APP_ID, 'personal_widget_icon', 'icon-iframe'),
            'widgetIconColor' => $this->config->getUserValue($userId, Application::APP_ID, 'personal_widget_icon_color', ''),
            'iframeUrl' => $this->config->getUserValue($userId, Application::APP_ID, 'personal_iframe_url', ''),
            'iframeHeight' => $this->config->getUserValue($userId, Application::APP_ID, 'personal_iframe_height', ''),
            'extraWide' => $this->config->getUserValue($userId, Application::APP_ID, 'personal_extra_wide', '0') === '1',
        ]);

        $user = $this->userSession->getUser();
        if ($user !== null) {
            $this->cspPolicyHelper->addPersonalSettingsPolicy($user, $this->cspManager);
        }
        
        return new TemplateResponse(Application::APP_ID, 'personalSettings', [], 'blank');
    }

    public function getSection(): string {
        return Application::APP_ID;
    }

    public function getPriority(): int {
        return 10;
    }

    public function getName(): ?string {
        return $this->l->t('Personal iFrame Widget');
    }
}
