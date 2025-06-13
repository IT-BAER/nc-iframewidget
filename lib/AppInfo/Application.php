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

        // Register personal settings page
        $context->registerPersonalSettings(Personal::class);
    }
    
    public function boot(IBootContext $context): void
    {
    }
}
