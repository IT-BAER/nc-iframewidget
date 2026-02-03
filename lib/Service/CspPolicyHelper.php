<?php

declare(strict_types=1);

namespace OCA\IframeWidget\Service;

use OCA\IframeWidget\AppInfo\Application;
use OCP\AppFramework\Http\ContentSecurityPolicy;
use OCP\IConfig;
use OCP\IGroupManager;
use OCP\IUser;
use OCP\IUserSession;
use OCP\Security\IContentSecurityPolicyManager;

class CspPolicyHelper
{
    private const SIMPLE_ICONS_DOMAIN = 'cdn.simpleicons.org';

    private IConfig $config;
    private IGroupManager $groupManager;
    private IUserSession $userSession;

    public function __construct(
        IConfig $config,
        IGroupManager $groupManager,
        IUserSession $userSession
    ) {
        $this->config = $config;
        $this->groupManager = $groupManager;
        $this->userSession = $userSession;
    }

    public function addAdminSettingsPolicy(IContentSecurityPolicyManager $cspManager): void
    {
        $urls = array_merge(
            $this->getPublicWidgetUrls(true),
            $this->getGroupWidgetUrls(true)
        );

        $this->applyPolicy($cspManager, $urls);
    }

    public function addPersonalSettingsPolicy(IUser $user, IContentSecurityPolicyManager $cspManager): void
    {
        $urls = $this->getPersonalWidgetUrls($user);
        $this->applyPolicy($cspManager, $urls);
    }

    public function addDashboardPolicyForUser(?IUser $user, IContentSecurityPolicyManager $cspManager): void
    {
        if ($user === null) {
            $user = $this->userSession->getUser();
        }

        $urls = $this->getPublicWidgetUrls();

        if ($user !== null) {
            $urls = array_merge(
                $urls,
                $this->getGroupWidgetUrlsForUser($user),
                $this->getPersonalWidgetUrls($user)
            );
        }

        $this->applyPolicy($cspManager, $urls);
    }

    private function applyPolicy(IContentSecurityPolicyManager $cspManager, array $urls): void
    {
        $policy = new ContentSecurityPolicy();
        $policy->addAllowedImageDomain(self::SIMPLE_ICONS_DOMAIN);

        foreach ($this->normalizeUrlsToOrigins($urls) as $origin) {
            $policy->addAllowedFrameDomain($origin);
        }

        $cspManager->addDefaultPolicy($policy);
    }

    private function normalizeUrlsToOrigins(array $urls): array
    {
        $origins = [];

        foreach ($urls as $url) {
            if (!is_string($url)) {
                continue;
            }

            $origin = $this->normalizeUrlToOrigin($url);
            if ($origin !== null) {
                $origins[$origin] = true;
            }
        }

        return array_keys($origins);
    }

    private function normalizeUrlToOrigin(string $url): ?string
    {
        $trimmed = trim($url);
        if ($trimmed === '') {
            return null;
        }

        $parts = parse_url($trimmed);
        if ($parts === false || empty($parts['scheme']) || empty($parts['host'])) {
            return null;
        }

        $scheme = strtolower($parts['scheme']);
        if (!in_array($scheme, ['http', 'https'], true)) {
            return null;
        }

        $host = strtolower($parts['host']);
        if ($host === '') {
            return null;
        }

        $port = $parts['port'] ?? null;
        $origin = $scheme . '://' . $host;
        if ($port !== null) {
            $origin .= ':' . $port;
        }

        return $origin;
    }

    private function getPublicWidgetUrls(bool $includeDisabled = false): array
    {
        $urls = [];

        $jsonWidgets = $this->config->getAppValue(Application::APP_ID, 'publicWidgetsJson', '');
        if (!empty($jsonWidgets)) {
            $widgets = json_decode($jsonWidgets, true);
            if (is_array($widgets)) {
                foreach ($widgets as $widget) {
                    $url = $widget['url'] ?? '';
                    $enabled = $widget['enabled'] ?? true;
                    if (!empty($url) && ($includeDisabled || $enabled)) {
                        $urls[] = $url;
                    }
                }
            }
        }

        $legacyUrl = $this->config->getAppValue(Application::APP_ID, 'iframeUrl', '');
        if (!empty($legacyUrl)) {
            $urls[] = $legacyUrl;
        }

        return $urls;
    }

    private function getGroupWidgetUrls(bool $includeDisabled = false): array
    {
        $urls = [];

        $jsonWidgets = $this->config->getAppValue(Application::APP_ID, 'groupWidgetsJson', '');
        if (!empty($jsonWidgets)) {
            $widgets = json_decode($jsonWidgets, true);
            if (is_array($widgets)) {
                foreach ($widgets as $widget) {
                    $url = $widget['url'] ?? '';
                    $enabled = $widget['enabled'] ?? true;
                    if (!empty($url) && ($includeDisabled || $enabled)) {
                        $urls[] = $url;
                    }
                }
            }
        }

        return $urls;
    }

    private function getGroupWidgetUrlsForUser(IUser $user): array
    {
        $urls = [];
        $userGroups = $this->groupManager->getUserGroupIds($user);

        $jsonWidgets = $this->config->getAppValue(Application::APP_ID, 'groupWidgetsJson', '');
        if (!empty($jsonWidgets)) {
            $widgets = json_decode($jsonWidgets, true);
            if (is_array($widgets)) {
                foreach ($widgets as $widget) {
                    $url = $widget['url'] ?? '';
                    $enabled = $widget['enabled'] ?? true;
                    $groupId = $widget['groupId'] ?? '';

                    if (!empty($url) && $enabled && in_array($groupId, $userGroups, true)) {
                        $urls[] = $url;
                    }
                }
            }
        }

        return $urls;
    }

    private function getPersonalWidgetUrls(IUser $user): array
    {
        $userId = $user->getUID();
        $url = $this->config->getUserValue($userId, Application::APP_ID, 'personal_iframe_url', '');

        return !empty($url) ? [$url] : [];
    }
}
