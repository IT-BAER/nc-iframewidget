<?php

declare(strict_types=1);

namespace OCA\IframeWidget\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCA\IframeWidget\Dashboard\IframeWidget;
use OCA\IframeWidget\Dashboard\PersonalIframeWidget;
use OCA\IframeWidget\Settings\Personal;
use OCA\IframeWidget\Settings\PersonalSection;
use OCP\Settings\ISettingsManager;

class Application extends App implements IBootstrap
{
    public const APP_ID = 'iframewidget';

    public function __construct()
    {
        parent::__construct(self::APP_ID);
    }

    public function register(IRegistrationContext $context): void
    {
        // Register dashboard widgets
        $context->registerDashboardWidget(IframeWidget::class);
        $context->registerDashboardWidget(PersonalIframeWidget::class);

        // Register services
        $context->registerService(Personal::class, function($c) {
            return new Personal(
                $c->get('OCP\IConfig'),
                $c->get('OCP\IUserSession'),
                $c->get('OCP\AppFramework\Services\IInitialState'),
                $c->get('OCP\IL10N')
            );
        });

        $context->registerService(PersonalSection::class, function($c) {
            return new PersonalSection(
                $c->get('OCP\IL10N'),
                $c->get('OCP\IURLGenerator')
            );
        });
    }
    
    public function boot(IBootContext $context): void
    {
        $container = $context->getServerContainer();
        
        /** @var ISettingsManager $settingsManager */
        $settingsManager = $container->get(ISettingsManager::class);
        
        // Register settings pages
        $settingsManager->registerSetting(ISettingsManager::KEY_PERSONAL_SETTINGS, Personal::class);
        $settingsManager->registerSection(ISettingsManager::KEY_PERSONAL_SECTION, PersonalSection::class);
    }
}
