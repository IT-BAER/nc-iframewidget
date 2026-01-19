<?php

declare(strict_types=1);

namespace OCA\IframeWidget\Dashboard;

use OCA\IframeWidget\AppInfo\Application;
use OCP\Dashboard\IWidget;
use OCP\Dashboard\IConditionalWidget;
use OCP\IConfig;
use OCP\IGroupManager;
use OCP\IL10N;
use OCP\IUserSession;
use OCP\AppFramework\Services\IInitialState;
use OCP\Security\IContentSecurityPolicyManager;

/**
 * Base class for group-based iFrame widget slots
 * 
 * Implements IConditionalWidget to hide unconfigured slots from the dashboard selector.
 * Each slot (1-5) shows a group widget if the user belongs to a configured group.
 */
abstract class GroupWidgetSlot implements IWidget, IConditionalWidget
{
    protected IL10N $l10n;
    protected IConfig $config;
    protected IInitialState $initialStateService;
    protected IUserSession $userSession;
    protected IGroupManager $groupManager;
    protected IContentSecurityPolicyManager $cspManager;
    
    /** @var int The slot number (1-5) */
    protected int $slotNumber;

    public function __construct(
        IL10N $l10n,
        IConfig $config,
        IInitialState $initialStateService,
        IUserSession $userSession,
        IGroupManager $groupManager,
        IContentSecurityPolicyManager $cspManager
    ) {
        $this->l10n = $l10n;
        $this->config = $config;
        $this->initialStateService = $initialStateService;
        $this->userSession = $userSession;
        $this->groupManager = $groupManager;
        $this->cspManager = $cspManager;
    }

    /**
     * Get the slot number - must be implemented by subclasses
     */
    abstract protected function getSlotNumber(): int;

    /**
     * @return string Unique id that identifies the widget
     */
    public function getId(): string
    {
        $slot = $this->getSlotNumber();
        // First slot keeps original ID for backward compatibility
        return $slot === 1 ? 'group-iframewidget' : 'group-iframewidget-' . $slot;
    }

    /**
     * @return string title for the widget
     */
    public function getTitle(): string
    {
        $widgetConfig = $this->getUserWidgetConfig();
        if ($widgetConfig && !empty(trim($widgetConfig['title'] ?? ''))) {
            return $widgetConfig['title'];
        }
        
        $slot = $this->getSlotNumber();
        return $slot === 1 
            ? $this->l10n->t('Group iFrame') 
            : $this->l10n->t('Group iFrame') . ' ' . $slot;
    }

    /**
     * @return int Initial order for widget sorting
     */
    public function getOrder(): int
    {
        return 20 + $this->getSlotNumber();
    }

    /**
     * @return string css class that displays an icon next to the widget title
     */
    public function getIconClass(): string
    {
        return 'icon-group-iframewidget';
    }

    /**
     * @return string|null The absolute url to the apps own view
     */
    public function getUrl(): ?string
    {
        return null;
    }

    /**
     * @return bool
     */
    public function isVisible(\OCP\IUser $user = null): bool
    {
        return $this->isEnabled($user);
    }

    /**
     * Check if this widget slot is enabled for the current user
     * 
     * This hides unconfigured slots from the dashboard widget selector.
     * A slot is enabled if:
     * 1. There's a widget configured for this slot number
     * 2. The current user belongs to the configured group
     * 
     * @return bool
     */
    public function isEnabled(\OCP\IUser $user = null): bool
    {
        if ($user === null) {
            $user = $this->userSession->getUser();
            error_log("GroupWidgetSlot: user from session is " . ($user ? $user->getUID() : 'null'));
        } else {
            error_log("GroupWidgetSlot: user passed to isEnabled is " . $user->getUID());
        }
        
        if ($user === null) {
            error_log("GroupWidgetSlot: user is null, returning false");
            return false;
        }
        
        $widgetConfig = $this->getUserWidgetConfig($user);
        if ($widgetConfig === null || empty($widgetConfig['url'])) {
            return false;
        }
        
        return $widgetConfig['enabled'] ?? true;
    }

    /**
     * Execute widget bootstrap code like loading scripts and providing initial state
     */
    public function load(): void
    {
        $widgetConfig = $this->getUserWidgetConfig();
        
        $config = [
            'slotNumber' => $this->getSlotNumber(),
            'extraWide' => $widgetConfig['extraWide'] ?? false,
            'widgetTitle' => $widgetConfig['title'] ?? '',
            'widgetIcon' => $widgetConfig['icon'] ?? '',
            'widgetIconColor' => $widgetConfig['iconColor'] ?? '',
            'iframeHeight' => $widgetConfig['height'] ?? '',
            'iframeUrl' => $widgetConfig['url'] ?? '',
            'userGroup' => $widgetConfig['groupId'] ?? '',
            'iframeSandbox' => $widgetConfig['iframeSandbox'] ?? 'allow-same-origin allow-scripts allow-popups allow-forms',
            'iframeAllow' => $widgetConfig['iframeAllow'] ?? ''
        ];

        // Provide initial state with slot-specific key
        $stateKey = $this->getSlotNumber() === 1 
            ? 'group-iframewidget-config' 
            : 'group-iframewidget-config-' . $this->getSlotNumber();
        $this->initialStateService->provideInitialState($stateKey, $config);

        // Add SimpleIcons to Content Security Policy
        $policy = new \OCP\AppFramework\Http\ContentSecurityPolicy();
        $policy->addAllowedImageDomain('cdn.simpleicons.org');
        $this->cspManager->addDefaultPolicy($policy);

        // Load scripts and styles
        \OCP\Util::addScript('iframewidget', 'iframewidget-dashboard');
        \OCP\Util::addStyle('iframewidget', 'dashboard');
    }

    /**
     * Get the widget configuration for this slot for the current user
     * 
     * Finds widgets that match:
     * 1. This slot number
     * 2. A group the current user belongs to
     * 
     * @return array|null Widget config or null if not configured
     */
    protected function getUserWidgetConfig(\OCP\IUser $user = null): ?array
    {
        if ($user === null) {
            $user = $this->userSession->getUser();
        }
        
        if ($user === null) {
            return null;
        }
        
        $userId = $user->getUID();
        $userGroups = $this->groupManager->getUserGroupIds($user);
        $slot = $this->getSlotNumber();

        // Check new JSON storage for multiple group widgets
        $jsonWidgets = $this->config->getAppValue(Application::APP_ID, 'groupWidgetsJson', '');
        if (!empty($jsonWidgets)) {
            $widgets = json_decode($jsonWidgets, true);
            if (is_array($widgets)) {
                // 1. Filter widgets this user has access to, are enabled, and have a URL
                $userWidgets = [];
                foreach ($widgets as $widget) {
                    $widgetGroup = $widget['groupId'] ?? '';
                    $hasUrl = !empty($widget['url']);
                    if (in_array($widgetGroup, $userGroups) && ($widget['enabled'] ?? true) && $hasUrl) {
                        $userWidgets[] = $widget;
                    }
                }
                
                // 2. Sort them (by slot assigned during save, then groupId)
                usort($userWidgets, function($a, $b) {
                    $slotA = $a['slot'] ?? 999;
                    $slotB = $b['slot'] ?? 999;
                    if ($slotA !== $slotB) return $slotA - $slotB;
                    return strcmp($a['groupId'] ?? '', $b['groupId'] ?? '');
                });
                
                // 3. Return the one corresponding to this slot provider
                if (isset($userWidgets[$slot - 1])) {
                    return $userWidgets[$slot - 1];
                }
            }
        }

        // For slot 1, check legacy storage for backward compatibility
        if ($slot === 1) {
            return $this->getLegacyWidgetConfig($userId, $userGroups);
        }

        return null;
    }

    /**
     * Get widget config from legacy individual key storage
     * 
     * @param string $userId
     * @param array $userGroups
     * @return array|null
     */
    private function getLegacyWidgetConfig(string $userId, array $userGroups): ?array
    {
        $allKeys = $this->config->getAppKeys(Application::APP_ID);
        
        foreach ($userGroups as $groupId) {
            $urlKey = 'group_' . $groupId . '_iframeUrl';
            $url = $this->config->getAppValue(Application::APP_ID, $urlKey, '');
            
            if (!empty($url)) {
                return [
                    'id' => $groupId . '_default',
                    'groupId' => $groupId,
                    'title' => $this->config->getAppValue(Application::APP_ID, 'group_' . $groupId . '_widgetTitle', ''),
                    'icon' => $this->config->getAppValue(Application::APP_ID, 'group_' . $groupId . '_widgetIcon', ''),
                    'iconColor' => $this->config->getAppValue(Application::APP_ID, 'group_' . $groupId . '_widgetIconColor', ''),
                    'url' => $url,
                    'height' => $this->config->getAppValue(Application::APP_ID, 'group_' . $groupId . '_iframeHeight', ''),
                    'extraWide' => $this->config->getAppValue(Application::APP_ID, 'group_' . $groupId . '_extraWide', 'false') === 'true',
                    'isDefault' => true,
                    'iframeSandbox' => $this->config->getAppValue(Application::APP_ID, 'group_' . $groupId . '_iframeSandbox', 'allow-same-origin allow-scripts allow-popups allow-forms'),
                    'iframeAllow' => $this->config->getAppValue(Application::APP_ID, 'group_' . $groupId . '_iframeAllow', '')
                ];
            }
        }
        
        return null;
    }
}
