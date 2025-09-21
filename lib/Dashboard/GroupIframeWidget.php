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
        $widgetConfig = $this->getUserWidgetConfig($userId);

        if (!$widgetConfig) {
            return '';
        }

        return empty(trim($widgetConfig['title'])) ? '' : $widgetConfig['title'];
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
        $widgetConfig = $this->getUserWidgetConfig($userId);

        // Default icon if no widget configured for user
        if (!$widgetConfig) {
            return 'icon-iframewidget';
        }

        $icon = $widgetConfig['icon'];

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
        $widgetConfig = $this->getUserWidgetConfig($userId);

        // Always provide initial state, even if user is not in a configured group
        $config = [
            'extraWide' => false,
            'widgetTitle' => '',
            'widgetIcon' => '',
            'widgetIconColor' => '',
            'iframeHeight' => '',
            'iframeUrl' => '',
            'userGroup' => ''
        ];

        // If user has a configured widget, load their actual settings
        if ($widgetConfig) {
            $config = [
                'extraWide' => $widgetConfig['extraWide'],
                'widgetTitle' => $widgetConfig['title'],
                'widgetIcon' => $widgetConfig['icon'],
                'widgetIconColor' => $widgetConfig['iconColor'],
                'iframeHeight' => $widgetConfig['height'],
                'iframeUrl' => $widgetConfig['url'],
                'userGroup' => $widgetConfig['groupId']
            ];
        }

        // Always provide initial state
        $this->initialStateService->provideInitialState('group-iframewidget-config', $config);

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
     * Get the user's widget configuration for their group
     * Returns the default widget configuration for the first configured group the user belongs to
     *
     * @param string $userId
     * @return array|null
     */
    private function getUserWidgetConfig(string $userId): ?array
    {
        // Check if we have the new JSON-based storage
        $jsonWidgets = $this->config->getAppValue(Application::APP_ID, 'groupWidgetsJson', '');
        if (!empty($jsonWidgets)) {
            $widgets = json_decode($jsonWidgets, true);
            if (is_array($widgets)) {
                // Find user's groups
                $userGroups = $this->groupManager->getUserGroupIds($this->userSession->getUser());

                // Look for default widget in user's groups
                foreach ($userGroups as $groupId) {
                    foreach ($widgets as $widget) {
                        if ($widget['groupId'] === $groupId && $widget['isDefault']) {
                            return $widget;
                        }
                    }
                }

                // If no default found, return first widget for user's group
                foreach ($userGroups as $groupId) {
                    foreach ($widgets as $widget) {
                        if ($widget['groupId'] === $groupId) {
                            return $widget;
                        }
                    }
                }
            }
        }

        // Fallback to old individual key storage for backward compatibility
        $configuredGroups = $this->getConfiguredGroups();

        foreach ($configuredGroups as $groupId) {
            if ($this->groupManager->isInGroup($userId, $groupId)) {
                // Get widget settings for this group
                $widgetTitle = $this->config->getAppValue(
                    Application::APP_ID,
                    'group_' . $groupId . '_widgetTitle',
                    ''
                );
                $widgetIcon = $this->config->getAppValue(
                    Application::APP_ID,
                    'group_' . $groupId . '_widgetIcon',
                    ''
                );
                $widgetIconColor = $this->config->getAppValue(
                    Application::APP_ID,
                    'group_' . $groupId . '_widgetIconColor',
                    ''
                );
                $iframeUrl = $this->config->getAppValue(
                    Application::APP_ID,
                    'group_' . $groupId . '_iframeUrl',
                    ''
                );
                $iframeHeight = $this->config->getAppValue(
                    Application::APP_ID,
                    'group_' . $groupId . '_iframeHeight',
                    ''
                );
                $extraWide = $this->config->getAppValue(
                    Application::APP_ID,
                    'group_' . $groupId . '_extraWide',
                    'false'
                );

                // Return widget config if it has meaningful data
                if (!empty($iframeUrl) || !empty($widgetTitle) || !empty($widgetIcon)) {
                    return [
                        'id' => $groupId . '_default',
                        'groupId' => $groupId,
                        'title' => $widgetTitle,
                        'icon' => $widgetIcon,
                        'iconColor' => $widgetIconColor,
                        'url' => $iframeUrl,
                        'height' => $iframeHeight,
                        'extraWide' => $extraWide === 'true',
                        'isDefault' => true
                    ];
                }
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
        $allKeys = $this->config->getAppKeys(Application::APP_ID);

        foreach ($allKeys as $key) {
            if (str_starts_with($key, 'group_') && str_ends_with($key, '_iframeUrl')) {
                $value = $this->config->getAppValue(Application::APP_ID, $key, '');
                if (!empty($value)) {
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
        }

        return $groups;
    }
}