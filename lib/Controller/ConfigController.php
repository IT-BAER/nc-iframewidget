<?php
namespace OCA\IframeWidget\Controller;

use OCP\Files\IAppData;
use OCP\IConfig;
use OCP\IServerContainer;
use OCP\IRequest;
use OCP\IUserSession;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use OCA\IframeWidget\AppInfo\Application;

/**
 * ConfigController handles configuration management for the iFrame Widget
 * 
 * This controller handles saving and retrieving configuration values,
 * as well as proxying requests to SimpleIcons to avoid CORS issues.
 */
class ConfigController extends Controller
{
    /** @var IConfig */
    private $config;

    /** @var string|null */
    private $userId;
    
    /** @var IUserSession */
    private $userSession;

    /**
     * Constructor for ConfigController
     */
    public function __construct(
        $AppName,
        IRequest $request,
        IServerContainer $serverContainer,
        IConfig $config,
        IAppData $appData,
        IUserSession $userSession
    ) {
        parent::__construct($AppName, $request);
        $this->userSession = $userSession;
        $user = $userSession->getUser();
        $this->userId = $user ? $user->getUID() : null;
        $this->appData = $appData;
        $this->serverContainer = $serverContainer;
        $this->config = $config;
    }

	/**
	 * Validate if a URL is valid and starts with http:// or https://
	 * 
	 * @param string $url The URL to validate
	 * @return bool True if valid
	 */
	private function isValidUrl(string $url): bool {
		$url = trim($url);
		if (empty($url)) {
			return false;
		}
		return (stripos($url, 'http://') === 0 || stripos($url, 'https://') === 0);
	}
    
    /**
     * Set admin configuration values
     *
     * @NoCSRFRequired
     * @AdminRequired
     * @return DataResponse
     */
    public function setAdminConfig(): DataResponse
    {
        // Get the request body and decode it
        $request = file_get_contents('php://input');
        $values = json_decode($request, true);
    
        if (!is_array($values)) {
            return new DataResponse(['status' => 'error', 'message' => 'Invalid input'], 400);
        }
    
        foreach ($values as $key => $value) {
            // For boolean values that come as strings
            if ($value === 'true' || $value === true) {
                $value = 'true';
            } elseif ($value === 'false' || $value === false) {
                $value = 'false';
            }

            if ($key === 'iframeUrl' && !empty($value) && !$this->isValidUrl($value)) {
                return new DataResponse(['status' => 'error', 'message' => 'Invalid URL for iframeUrl. Must start with http:// or https://'], 400);
            }

            $this->config->setAppValue(Application::APP_ID, $key, $value);
        }
    
        return new DataResponse(['status' => 'success']);
    }

    /**
     * Get widget configuration
     * 
     * @NoAdminRequired
     * @return DataResponse
     */
    public function getConfig(): DataResponse
    {
        $widgetTitle = $this->config->getAppValue(Application::APP_ID, 'widgetTitle', 'iFrame Widget');
        $extraWide = $this->config->getAppValue(Application::APP_ID, 'extraWide', false);
        $maxSize = $this->config->getAppValue(Application::APP_ID, 'maxSize', false);
        $iframeUrl = $this->config->getAppValue(Application::APP_ID, 'iframeUrl', '');
        $iframeHeight = $this->config->getAppValue(Application::APP_ID, 'iframeHeight', '');
        $widgetIcon = $this->config->getAppValue(Application::APP_ID, 'widgetIcon', '');
        $widgetIconColor = $this->config->getAppValue(Application::APP_ID, 'widgetIconColor', '');
        
        $iframeSandbox = $this->config->getAppValue(Application::APP_ID, 'iframeSandbox', 'allow-same-origin allow-scripts allow-popups allow-forms');
        $iframeAllow = $this->config->getAppValue(Application::APP_ID, 'iframeAllow', '');
        
        return new DataResponse([
            'widgetTitle' => $widgetTitle,
            'extraWide' => $extraWide,
            'maxSize' => $maxSize,
            'iframeUrl' => $iframeUrl,
            'iframeHeight' => $iframeHeight,
            'widgetIcon' => $widgetIcon,
            'widgetIconColor' => $widgetIconColor,
            'iframeSandbox' => $iframeSandbox,
            'iframeAllow' => $iframeAllow
        ]);
    }

	/**
	 * Proxy request to SimpleIcons CDN to avoid CORS issues
	 *
	 * @NoAdminRequired
	 * @param string $icon Icon name
	 * @return DataResponse
	 */
	public function proxyIcon(string $icon): DataResponse {
		$icon = strtolower(trim($icon));
		$color = $this->request->getParam('color', '');
		
		$url = "https://cdn.simpleicons.org/{$icon}";
		if (!empty($color)) {
			$url .= "/{$color}";
		}
		
		try {
			$client = new \GuzzleHttp\Client();
			$response = $client->get($url, [
				'timeout' => 5,
				'connect_timeout' => 5
			]);
			
			return new DataResponse([
				'exists' => true
			]);
		} catch (\Exception $e) {
			return new DataResponse([
				'exists' => false, 
				'error' => $e->getMessage()
			], 404);
		}
	}

	/**
	 * Get available groups for group widget assignment
	 *
	 * @AdminRequired
	 * @return DataResponse
	 */
	public function getGroups(): DataResponse {
		try {
			$groupManager = $this->serverContainer->get(\OCP\IGroupManager::class);
			$groups = [];

			// Try to get all groups using search method (works in Nextcloud 31)
			$searchResult = $groupManager->search('');
			foreach ($searchResult as $group) {
				$groupId = $group->getGID();
				$groups[] = [
					'id' => $groupId,
					'displayName' => $groupManager->getDisplayName($groupId) ?: $groupId
				];
			}

			return new DataResponse($groups);
		} catch (\Exception $e) {
			return new DataResponse(['error' => 'Failed to load groups: ' . $e->getMessage()], 500);
		}
	}

	/**
	 * Get configured group widgets
	 *
	 * @AdminRequired
	 * @return DataResponse
	 */
	public function getGroupWidgets(): DataResponse {
		$groupWidgets = [];
		$allKeys = $this->config->getAppKeys(Application::APP_ID);

	// Check if we have the new JSON-based storage
		$jsonWidgets = $this->config->getAppValue(Application::APP_ID, 'groupWidgetsJson', '');
		if (!empty($jsonWidgets)) {
			$parsedWidgets = json_decode($jsonWidgets, true);
			if (is_array($parsedWidgets)) {
				$groupManager = $this->serverContainer->get(\OCP\IGroupManager::class);
				$needsSave = false;

				// Group widgets by groupId for slot assignment
				$widgetsByGroup = [];
				foreach ($parsedWidgets as $widget) {
					$gid = $widget['groupId'] ?? 'unknown';
					if (!isset($widgetsByGroup[$gid])) {
						$widgetsByGroup[$gid] = [];
					}
					$widgetsByGroup[$gid][] = $widget;
				}

				// Backfill missing slots and enabled status
				foreach ($parsedWidgets as &$widget) {
					$widget['groupDisplayName'] = $groupManager->getDisplayName($widget['groupId']) ?: $widget['groupId'];
					
					// Assign slot if missing
					if (!isset($widget['slot']) || $widget['slot'] === null) {
						$gid = $widget['groupId'];
						$usedSlots = [];
						foreach ($parsedWidgets as $w) {
							if ($w['groupId'] === $gid && isset($w['slot'])) {
								$usedSlots[] = $w['slot'];
							}
						}
						for ($i = 1; $i <= 5; $i++) {
							if (!in_array($i, $usedSlots)) {
								$widget['slot'] = $i;
								$needsSave = true;
								break;
							}
						}
					}

					// Backfill enabled status if missing
					if (!isset($widget['enabled'])) {
						// Use isDefault if available to preserve previous visibility state
						// Otherwise default to true
						$widget['enabled'] = isset($widget['isDefault']) ? $widget['isDefault'] : true;
						$needsSave = true;
					}
				}

				// Save if we updated any slots
				if ($needsSave) {
					$this->config->setAppValue(Application::APP_ID, 'groupWidgetsJson', json_encode($parsedWidgets));
				}

				return new DataResponse(array_values($parsedWidgets));
			}
		}

		// Fallback to old individual key storage for backward compatibility
		foreach ($allKeys as $key) {
			if (str_starts_with($key, 'group_') && str_contains($key, '_')) {
				// Extract group ID and field from key like 'group_admins_widgetTitle'
				$parts = explode('_', $key, 3);
				if (count($parts) >= 3) {
					$groupId = $parts[1];
					$field = $parts[2];

					if (!isset($groupWidgets[$groupId])) {
						$groupWidgets[$groupId] = [
							'id' => $groupId . '_default',
							'groupId' => $groupId,
							'title' => '',
							'icon' => '',
							'iconColor' => '',
							'url' => '',
							'height' => '',
							'extraWide' => false,
							'isDefault' => true,
							'groupDisplayName' => $this->serverContainer->get(\OCP\IGroupManager::class)->getDisplayName($groupId) ?: $groupId
						];
					}

					// Set the field value
					$value = $this->config->getAppValue(Application::APP_ID, $key, '');
					if ($field === 'extraWide' && ($value === true || $value === 'true')) {
						$value = 'true';
					} elseif ($field === 'extraWide' && ($value === false || $value === 'false')) {
						$value = 'false';
					}

					// Map old field names to new ones
					$fieldMap = [
						'widgetTitle' => 'title',
						'widgetIcon' => 'icon',
						'widgetIconColor' => 'iconColor',
						'iframeUrl' => 'url',
						'iframeHeight' => 'height'
					];

					if (isset($fieldMap[$field])) {
						$groupWidgets[$groupId][$fieldMap[$field]] = $value;
					} elseif ($field === 'extraWide') {
						$groupWidgets[$groupId][$field] = $value;
					}
				}
			}
		}

		// Filter out widgets that have no meaningful data
		$filteredWidgets = [];
		foreach ($groupWidgets as $widget) {
			if (!empty($widget['url']) || !empty($widget['title']) || !empty($widget['icon'])) {
				$filteredWidgets[] = $widget;
			}
		}

		return new DataResponse($filteredWidgets);
	}

	/**
	 * Save or update a group widget configuration
	 *
	 * @AdminRequired
	 * @return DataResponse
	 */
	public function setGroupWidget(): DataResponse {
		$data = $this->request->getParams();
		$groupId = $data['groupId'] ?? '';
		$widgetId = $data['id'] ?? '';
		$url = $data['url'] ?? '';

		if (empty($groupId)) {
			return new DataResponse(['status' => 'error', 'message' => 'Group ID is required'], 400);
		}

		if (!empty($url) && !$this->isValidUrl($url)) {
			return new DataResponse(['status' => 'error', 'message' => 'Invalid URL. Must start with http:// or https://'], 400);
		}

		// Validate that the group exists
		$groupManager = $this->serverContainer->get(\OCP\IGroupManager::class);
		if (!$groupManager->groupExists($groupId)) {
			return new DataResponse(['status' => 'error', 'message' => 'Group does not exist'], 400);
		}

		// Get existing widgets
		$existingWidgets = [];
		$jsonWidgets = $this->config->getAppValue(Application::APP_ID, 'groupWidgetsJson', '');
		if (!empty($jsonWidgets)) {
			$existingWidgets = json_decode($jsonWidgets, true) ?: [];
		}

		// Find existing widget or create new one
		$widgetIndex = null;

		if ($widgetId) {
			foreach ($existingWidgets as $index => $widget) {
				if ($widget['id'] === $widgetId) {
					$widgetIndex = $index;
					break;
				}
			}
		}

		// Assign slot number if not provided (find next available slot for this group)
		$slot = $data['slot'] ?? null;
		if ($slot === null && $widgetIndex === null) {
			// Find all slots used by widgets in this group
			$usedSlots = [];
			foreach ($existingWidgets as $widget) {
				if ($widget['groupId'] === $groupId && isset($widget['slot'])) {
					$usedSlots[] = $widget['slot'];
				}
			}
			// Find next available slot (1-5)
			for ($i = 1; $i <= 5; $i++) {
				if (!in_array($i, $usedSlots)) {
					$slot = $i;
					break;
				}
			}
			if ($slot === null) {
				return new DataResponse(['status' => 'error', 'message' => 'Maximum 5 widgets per group allowed'], 400);
			}
		} elseif ($widgetIndex !== null) {
			// Keep existing slot if updating
			$slot = $slot ?? ($existingWidgets[$widgetIndex]['slot'] ?? null);
		}

		// Prepare widget data
		$widgetData = [
			'id' => $widgetId ?: uniqid($groupId . '_', true),
			'slot' => $slot,
			'groupId' => $groupId,
			'title' => $data['title'] ?? '',
			'icon' => $data['icon'] ?? '',
			'iconColor' => $data['iconColor'] ?? '',
			'url' => $url,
			'height' => $data['height'] ?? '',
			'extraWide' => $data['extraWide'] ?? false,
			'enabled' => $data['enabled'] ?? true,
			'iframeSandbox' => $data['iframeSandbox'] ?? 'allow-same-origin allow-scripts allow-popups allow-forms',
			'iframeAllow' => $data['iframeAllow'] ?? ''
		];



		// Update or add widget
		if ($widgetIndex !== null) {
			$existingWidgets[$widgetIndex] = $widgetData;
		} else {
			$existingWidgets[] = $widgetData;
		}

		// Save to JSON storage
		$this->config->setAppValue(Application::APP_ID, 'groupWidgetsJson', json_encode($existingWidgets));

		return new DataResponse(['status' => 'success', 'widgetId' => $widgetData['id']]);
	}

	/**
	 * Delete a group widget configuration (helper method)
	 *
	 * @param string $groupId Group ID
	 */
	private function deleteGroupWidgetConfig(string $groupId): void {
		// Delete all group widget configuration keys
		$fields = [
			'widgetTitle',
			'widgetIcon',
			'widgetIconColor',
			'iframeUrl',
			'iframeHeight',
			'extraWide'
		];

		foreach ($fields as $field) {
			$this->config->deleteAppValue(Application::APP_ID, 'group_' . $groupId . '_' . $field);
		}
	}

	/**
	 * Delete a group widget configuration
	 *
	 * @AdminRequired
	 * @param string $widgetId Widget ID
	 * @return DataResponse
	 */
	public function deleteGroupWidget(string $widgetId): DataResponse {
		// Get existing widgets
		$existingWidgets = [];
		$jsonWidgets = $this->config->getAppValue(Application::APP_ID, 'groupWidgetsJson', '');
		if (!empty($jsonWidgets)) {
			$existingWidgets = json_decode($jsonWidgets, true) ?: [];
		}

		// Find and remove the widget
		$found = false;
		foreach ($existingWidgets as $index => $widget) {
			if ($widget['id'] === $widgetId) {
				unset($existingWidgets[$index]);
				$found = true;
				break;
			}
		}

		if (!$found) {
			// Check if it's an old-style widget (id ends with '_default')
			if (str_ends_with($widgetId, '_default')) {
				$groupId = substr($widgetId, 0, -8); // Remove '_default'
				$this->deleteGroupWidgetConfig($groupId);
				return new DataResponse(['status' => 'success']);
			}
			return new DataResponse(['status' => 'error', 'message' => 'Widget not found'], 404);
		}

		// Re-index array and save
		$existingWidgets = array_values($existingWidgets);
		$this->config->setAppValue(Application::APP_ID, 'groupWidgetsJson', json_encode($existingWidgets));

		return new DataResponse(['status' => 'success']);
	}

	/**
	 * Get configured public widgets
	 *
	 * @AdminRequired
	 * @return DataResponse
	 */
	public function getPublicWidgets(): DataResponse {
		$widgets = [];

		// Check for new JSON-based storage
		$jsonWidgets = $this->config->getAppValue(Application::APP_ID, 'publicWidgetsJson', '');
		if (!empty($jsonWidgets)) {
			$parsedWidgets = json_decode($jsonWidgets, true);
			if (is_array($parsedWidgets)) {
				$needsSave = false;
				foreach ($parsedWidgets as &$widget) {
					if (!isset($widget['enabled'])) {
						$widget['enabled'] = true;
						$needsSave = true;
					}
				}
				if ($needsSave) {
					$this->config->setAppValue(Application::APP_ID, 'publicWidgetsJson', json_encode($parsedWidgets));
				}
				return new DataResponse(array_values($parsedWidgets));
			}
		}

		// Fallback to legacy single-widget config for backward compatibility
		$legacyUrl = $this->config->getAppValue(Application::APP_ID, 'iframeUrl', '');
		if (!empty($legacyUrl)) {
			$widgets[] = [
				'id' => 'public-1',
				'slot' => 1,
				'title' => $this->config->getAppValue(Application::APP_ID, 'widgetTitle', ''),
				'icon' => $this->config->getAppValue(Application::APP_ID, 'widgetIcon', ''),
				'iconColor' => $this->config->getAppValue(Application::APP_ID, 'widgetIconColor', ''),
				'url' => $legacyUrl,
				'height' => $this->config->getAppValue(Application::APP_ID, 'iframeHeight', ''),
				'extraWide' => $this->config->getAppValue(Application::APP_ID, 'extraWide', 'false') === 'true'
			];
		}

		return new DataResponse($widgets);
	}

	/**
	 * Save or update a public widget configuration
	 *
	 * @AdminRequired
	 * @return DataResponse
	 */
	public function setPublicWidget(): DataResponse {
		$data = $this->request->getParams();
		$widgetId = $data['id'] ?? '';
		$url = $data['url'] ?? '';

		if (!empty($url) && !$this->isValidUrl($url)) {
			return new DataResponse(['status' => 'error', 'message' => 'Invalid URL. Must start with http:// or https://'], 400);
		}

		if (!is_array($data)) {
			return new DataResponse(['status' => 'error', 'message' => 'Invalid input'], 400);
		}

		// Get existing widgets
		$existingWidgets = [];
		$jsonWidgets = $this->config->getAppValue(Application::APP_ID, 'publicWidgetsJson', '');
		if (!empty($jsonWidgets)) {
			$existingWidgets = json_decode($jsonWidgets, true) ?: [];
		}

		// Find existing widget or create new one
		$widgetIndex = null;

		if ($widgetId) {
			foreach ($existingWidgets as $index => $widget) {
				if ($widget['id'] === $widgetId) {
					$widgetIndex = $index;
					break;
				}
			}
		}

		// Assign slot number if not provided (find next available slot)
		$slot = $data['slot'] ?? null;
		if ($slot === null && $widgetIndex === null) {
			$usedSlots = array_column($existingWidgets, 'slot');
			for ($i = 1; $i <= 5; $i++) {
				if (!in_array($i, $usedSlots)) {
					$slot = $i;
					break;
				}
			}
			if ($slot === null) {
				return new DataResponse(['status' => 'error', 'message' => 'Maximum 5 public widgets allowed'], 400);
			}
		} elseif ($widgetIndex !== null) {
			// Keep existing slot if updating
			$slot = $slot ?? $existingWidgets[$widgetIndex]['slot'];
		}

		// Prepare widget data
		$widgetData = [
			'id' => $widgetId ?: uniqid('public-', true),
			'slot' => $slot,
			'title' => $data['title'] ?? '',
			'icon' => $data['icon'] ?? '',
			'iconColor' => $data['iconColor'] ?? '',
			'url' => $url,
			'height' => $data['height'] ?? '',
			'extraWide' => $data['extraWide'] ?? false,
			'enabled' => $data['enabled'] ?? true,
			'iframeSandbox' => $data['iframeSandbox'] ?? 'allow-same-origin allow-scripts allow-popups allow-forms',
			'iframeAllow' => $data['iframeAllow'] ?? ''
		];

		// Update or add widget
		if ($widgetIndex !== null) {
			$existingWidgets[$widgetIndex] = $widgetData;
		} else {
			$existingWidgets[] = $widgetData;
		}

		// Save to JSON storage
		$this->config->setAppValue(Application::APP_ID, 'publicWidgetsJson', json_encode($existingWidgets));

		// Also update legacy keys for slot 1 (backward compatibility)
		if ($widgetData['slot'] === 1) {
			$this->config->setAppValue(Application::APP_ID, 'widgetTitle', $widgetData['title']);
			$this->config->setAppValue(Application::APP_ID, 'widgetIcon', $widgetData['icon']);
			$this->config->setAppValue(Application::APP_ID, 'widgetIconColor', $widgetData['iconColor']);
			$this->config->setAppValue(Application::APP_ID, 'iframeUrl', $widgetData['url']);
			$this->config->setAppValue(Application::APP_ID, 'iframeHeight', $widgetData['height']);
			$this->config->setAppValue(Application::APP_ID, 'extraWide', $widgetData['extraWide'] ? 'true' : 'false');
			$this->config->setAppValue(Application::APP_ID, 'iframeSandbox', $widgetData['iframeSandbox']);
			$this->config->setAppValue(Application::APP_ID, 'iframeAllow', $widgetData['iframeAllow']);
		}

		return new DataResponse(['status' => 'success', 'widgetId' => $widgetData['id'], 'slot' => $widgetData['slot']]);
	}

	/**
	 * Delete a public widget configuration
	 *
	 * @AdminRequired
	 * @param string $widgetId Widget ID
	 * @return DataResponse
	 */
	public function deletePublicWidget(string $widgetId): DataResponse {
		// Get existing widgets
		$existingWidgets = [];
		$jsonWidgets = $this->config->getAppValue(Application::APP_ID, 'publicWidgetsJson', '');
		if (!empty($jsonWidgets)) {
			$existingWidgets = json_decode($jsonWidgets, true) ?: [];
		}

		// Find and remove the widget
		$found = false;
		$deletedSlot = null;
		foreach ($existingWidgets as $index => $widget) {
			if ($widget['id'] === $widgetId) {
				$deletedSlot = $widget['slot'] ?? null;
				unset($existingWidgets[$index]);
				$found = true;
				break;
			}
		}

		if (!$found) {
			return new DataResponse(['status' => 'error', 'message' => 'Widget not found'], 404);
		}

		// Re-index array and save
		$existingWidgets = array_values($existingWidgets);
		$this->config->setAppValue(Application::APP_ID, 'publicWidgetsJson', json_encode($existingWidgets));

		// Clear legacy keys if slot 1 was deleted
		if ($deletedSlot === 1) {
			$this->config->deleteAppValue(Application::APP_ID, 'widgetTitle');
			$this->config->deleteAppValue(Application::APP_ID, 'widgetIcon');
			$this->config->deleteAppValue(Application::APP_ID, 'widgetIconColor');
			$this->config->deleteAppValue(Application::APP_ID, 'iframeUrl');
			$this->config->deleteAppValue(Application::APP_ID, 'iframeHeight');
			$this->config->deleteAppValue(Application::APP_ID, 'extraWide');
		}

		return new DataResponse(['status' => 'success']);
	}

}

