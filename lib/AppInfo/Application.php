<?php

declare(strict_types=1);

namespace OCA\IframeWidget\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCA\IframeWidget\Dashboard\IframeWidget;

class Application extends App implements IBootstrap
{
    public const APP_ID = 'iframewidget';

    public function __construct()
    {
        parent::__construct(self::APP_ID);
    }

    public function register(IRegistrationContext $context): void
    {
        $context->registerDashboardWidget(IframeWidget::class);
    }
    
    public function boot(IBootContext $context): void
    {
    }
}
