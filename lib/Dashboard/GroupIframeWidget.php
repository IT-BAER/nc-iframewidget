<?php

declare(strict_types=1);

namespace OCA\IframeWidget\Dashboard;

use OCA\IframeWidget\AppInfo\Application;
use OCP\Dashboard\IWidget;
use OCP\IConfig;
use OCP\IGroupManager;
use OCP\IL10N;
use OCP\IUserSession;
use OCP\AppFramework\Services\IInitialState;

/**
 * GroupIframeWidget implements group-based dashboard widget functionality
 *
 * This class registers and configures group-based iFrame widgets for the
 * Nextcloud dashboard, handling configuration based on user group membership.
 */
class GroupIframeWidget implements IWidget
{
    /** @var IL10N */
    private IL10N $l10n;

    /** @var IConfig */
    private IConfig $config;

    /** @var IInitialState */
    private IInitialState $initialStateService;

    /** @var IUserSession */
    private IUserSession $userSession;

    /** @var IGroupManager */
    private IGroupManager $groupManager;

    /** @var string|null */
    private ?string $userGroup = null;

    /**
     * Constructor for GroupIframeWidget
     */
    public function __construct(
        IL10N $l10n,
        IConfig $config,
        IInitialState $initialStateService,
        IUserSession $userSession,
        IGroupManager $groupManager
    ) {
        $this->l10n = $l10n;
        $this->config = $config;
        $this->initialStateService = $initialStateService;
        $this->userSession = $userSession;
        $this->groupManager = $groupManager;
    }

    /**
     * @return string Unique id that identifies the widget
     */
    public function getId(): string
    {
        return 'group-iframewidget';
    }

    /**
     * @return string title for the widget
     */
    public function getTitle(): string
    {
        $userId = $this->userSession->getUser()->getUID();
        $userGroup = $this->getUserGroup($userId);

        if (!$userGroup) {
            return '';
        }

        $widgetTitle = $this->config->getAppValue(
            Application::APP_ID,
            'group_' . $userGroup . '_widgetTitle',
            ''
        );

        return empty(trim($widgetTitle)) ? '' : $widgetTitle;
    }

    /**
     * @return int Initial order for widget sorting
     */
    public function getOrder(): int
    {
        return 5;
    }

    /**
     * @return string css class that displays an icon next to the widget title
     */
    public function getIconClass(): string
    {
        $userId = $this->userSession->getUser()->getUID();
        $userGroup = $this->getUserGroup($userId);

        if (!$userGroup) {
            return '';
        }

        $icon = $this->config->getAppValue(
            Application::APP_ID,
            'group_' . $userGroup . '_widgetIcon',
            ''
        );

        // Use default icon if none set
        if (empty($icon)) {
            return 'icon-iframewidget';
        }

        // For SimpleIcons (si:iconname), we need to use a generic class and handle the icon in JavaScript
        if (str_starts_with($icon, 'si:')) {
            return 'icon-group-iframewidget';
        }

        // For regular icons (icon-*), return as-is
        if (str_starts_with($icon, 'icon-')) {
            return $icon;
        }

        // Default fallback
        return 'icon-iframewidget';
    }

    /**
     * @return string|null The absolute url to the apps own view
     */
    public function getUrl(): ?string
    {
        return null;
    }

    /**
     * Execute widget bootstrap code like loading scripts and providing initial state
     */
    public function load(): void
    {
        $userId = $this->userSession->getUser()->getUID();
        $userGroup = $this->getUserGroup($userId);

        if (!$userGroup) {
            return; // Don't load if user is not in a configured group
        }

        // Get widget settings for this group
        $extraWide = $this->config->getAppValue(
            Application::APP_ID,
            'group_' . $userGroup . '_extraWide',
            'false'
        );
        $widgetTitle = $this->config->getAppValue(
            Application::APP_ID,
            'group_' . $userGroup . '_widgetTitle',
            ''
        );
        $widgetIcon = $this->config->getAppValue(
            Application::APP_ID,
            'group_' . $userGroup . '_widgetIcon',
            ''
        );
        $widgetIconColor = $this->config->getAppValue(
            Application::APP_ID,
            'group_' . $userGroup . '_widgetIconColor',
            ''
        );
        $iframeHeight = $this->config->getAppValue(
            Application::APP_ID,
            'group_' . $userGroup . '_iframeHeight',
            ''
        );
        $iframeUrl = $this->config->getAppValue(
            Application::APP_ID,
            'group_' . $userGroup . '_iframeUrl',
            ''
        );

        // Only load if URL is configured
        if (empty($iframeUrl)) {
            return;
        }

        // Provide initial state directly
        $this->initialStateService->provideInitialState('group-iframewidget-config', [
            'extraWide' => $extraWide,
            'widgetTitle' => $widgetTitle,
            'widgetIcon' => $widgetIcon,
            'widgetIconColor' => $widgetIconColor,
            'iframeHeight' => $iframeHeight,
            'iframeUrl' => $iframeUrl,
            'userGroup' => $userGroup
        ]);

        // Add SimpleIcons to Content Security Policy
        $cspManager = \OC::$server->getContentSecurityPolicyManager();
        $policy = new \OCP\AppFramework\Http\ContentSecurityPolicy();
        $policy->addAllowedImageDomain('cdn.simpleicons.org');
        $cspManager->addDefaultPolicy($policy);

        // Load scripts and styles
        \OCP\Util::addScript('iframewidget', 'iframewidget-dashboard');
        \OCP\Util::addStyle('iframewidget', 'dashboard');
    }

    /**
     * Get the user's group for widget configuration
     * Returns the first configured group the user belongs to
     *
     * @param string $userId
     * @return string|null
     */
    private function getUserGroup(string $userId): ?string
    {
        if ($this->userGroup !== null) {
            return $this->userGroup;
        }

        // Get all configured groups
        $configuredGroups = $this->getConfiguredGroups();

        foreach ($configuredGroups as $groupId) {
            if ($this->groupManager->isInGroup($userId, $groupId)) {
                $this->userGroup = $groupId;
                return $groupId;
            }
        }

        return null;
    }

    /**
     * Get all groups that have iframe widget configurations
     *
     * @return array
     */
    private function getConfiguredGroups(): array
    {
        $groups = [];
        $allKeys = $this->config->getAllValuesForApp(Application::APP_ID);

        foreach ($allKeys as $key => $value) {
            if (str_starts_with($key, 'group_') && str_ends_with($key, '_iframeUrl') && !empty($value)) {
                // Extract group ID from key like 'group_admins_iframeUrl'
                $parts = explode('_', $key);
                if (count($parts) >= 3) {
                    $groupId = $parts[1];
                    if (!in_array($groupId, $groups)) {
                        $groups[] = $groupId;
                    }
                }
            }
        }

        return $groups;
    }
}