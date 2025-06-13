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

        // Register settings
        $context->registerService(Personal::class, function($c) {
            return new Personal(
                $c->get(\OCP\IConfig::class),
                $c->get(\OCP\IUserSession::class),
                $c->get(\OCP\AppFramework\Services\IInitialState::class),
                $c->get(\OCP\IL10N::class)
            );
        });

        $context->registerService(PersonalSection::class, function($c) {
            return new PersonalSection(
                $c->get(\OCP\IL10N::class),
                $c->get(\OCP\IURLGenerator::class)
            );
        });

        // Register settings and sections
        $context->registerPersonalSettings(Personal::class);
        $context->registerPersonalSection(PersonalSection::class);
    }
    
    public function boot(IBootContext $context): void
    {
        // No need to manually register settings in boot() since we're using registerPersonalSettings/registerPersonalSection
    }
}
