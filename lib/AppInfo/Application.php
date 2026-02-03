<?php

declare(strict_types=1);

namespace OCA\IframeWidget\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;

// Import widget slot classes
use OCA\IframeWidget\Dashboard\PublicWidgetSlot1;
use OCA\IframeWidget\Dashboard\PublicWidgetSlot2;
use OCA\IframeWidget\Dashboard\PublicWidgetSlot3;
use OCA\IframeWidget\Dashboard\PublicWidgetSlot4;
use OCA\IframeWidget\Dashboard\PublicWidgetSlot5;
use OCA\IframeWidget\Dashboard\GroupWidgetSlot1;
use OCA\IframeWidget\Dashboard\GroupWidgetSlot2;
use OCA\IframeWidget\Dashboard\GroupWidgetSlot3;
use OCA\IframeWidget\Dashboard\GroupWidgetSlot4;
use OCA\IframeWidget\Dashboard\GroupWidgetSlot5;
use OCA\IframeWidget\Dashboard\PersonalIframeWidget;

use OCA\IframeWidget\Settings\Personal;
use OCA\IframeWidget\Settings\PersonalSection;
use OCA\IframeWidget\Service\CspPolicyHelper;

class Application extends App implements IBootstrap
{
    public const APP_ID = 'iframewidget';

    public function __construct()
    {
        parent::__construct(self::APP_ID);
    }

    public function register(IRegistrationContext $context): void
    {
        $context->registerService(CspPolicyHelper::class, function($c) {
            return new CspPolicyHelper(
                $c->get(\OCP\IConfig::class),
                $c->get(\OCP\IGroupManager::class),
                $c->get(\OCP\IUserSession::class)
            );
        });

        // Register public iFrame widget slots (1-5)
        // Slots use IConditionalWidget::isEnabled() to hide when not configured
        $context->registerDashboardWidget(PublicWidgetSlot1::class);
        $context->registerDashboardWidget(PublicWidgetSlot2::class);
        $context->registerDashboardWidget(PublicWidgetSlot3::class);
        $context->registerDashboardWidget(PublicWidgetSlot4::class);
        $context->registerDashboardWidget(PublicWidgetSlot5::class);

        // Register personal widget (single, user-configured)
        $context->registerDashboardWidget(PersonalIframeWidget::class);

        // Register group-based widget slots (1-5)
        // Slots check user group membership and hide when not applicable
        $context->registerDashboardWidget(GroupWidgetSlot1::class);
        $context->registerDashboardWidget(GroupWidgetSlot2::class);
        $context->registerDashboardWidget(GroupWidgetSlot3::class);
        $context->registerDashboardWidget(GroupWidgetSlot4::class);
        $context->registerDashboardWidget(GroupWidgetSlot5::class);

        // Register settings
        $context->registerService(Personal::class, function($c) {
            return new Personal(
                $c->get(\OCP\IConfig::class),
                $c->get(\OCP\IUserSession::class),
                $c->get(\OCP\AppFramework\Services\IInitialState::class),
                $c->get(\OCP\IL10N::class),
                $c->get(\OCP\Security\IContentSecurityPolicyManager::class),
                $c->get(CspPolicyHelper::class)
            );
        });

        $context->registerService(PersonalSection::class, function($c) {
            return new PersonalSection(
                $c->get(\OCP\IL10N::class),
                $c->get(\OCP\IURLGenerator::class)
            );
        });

        // Register service aliases for settings
        $context->registerServiceAlias(\OCP\Settings\ISection::class, PersonalSection::class);
        $context->registerServiceAlias(\OCP\Settings\ISettings::class, Personal::class);
    }
    
    public function boot(IBootContext $context): void
    {
        // No additional CSS needed - all styles in dashboard.css
    }
}

