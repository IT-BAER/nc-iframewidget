<?php
namespace OCA\IframeWidget\Controller;

use OCP\Files\IAppData;
use OCP\IConfig;
use OCP\IServerContainer;
use OCP\IRequest;
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

    /**
     * Constructor for ConfigController
     */
    public function __construct(
        $AppName,
        IRequest $request,
        IServerContainer $serverContainer,
        IConfig $config,
        IAppData $appData,
        ?string $userId
    ) {
        parent::__construct($AppName, $request);
        $this->userId = $userId;
        $this->appData = $appData;
        $this->serverContainer = $serverContainer;
        $this->config = $config;
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
        
        return new DataResponse([
            'widgetTitle' => $widgetTitle,
            'extraWide' => $extraWide,
            'maxSize' => $maxSize,
            'iframeUrl' => $iframeUrl,
            'iframeHeight' => $iframeHeight,
            'widgetIcon' => $widgetIcon,
            'widgetIconColor' => $widgetIconColor
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
				foreach ($parsedWidgets as &$widget) {
					$widget['groupDisplayName'] = $groupManager->getDisplayName($widget['groupId']) ?: $widget['groupId'];
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
		// Get the request body and decode it
		$request = file_get_contents('php://input');
		$data = json_decode($request, true);

		if (!is_array($data) || !isset($data['groupId'])) {
			return new DataResponse(['status' => 'error', 'message' => 'Invalid input or missing groupId'], 400);
		}

		$groupId = $data['groupId'];

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
		$widgetId = $data['id'] ?? null;
		$widgetIndex = null;

		if ($widgetId) {
			foreach ($existingWidgets as $index => $widget) {
				if ($widget['id'] === $widgetId) {
					$widgetIndex = $index;
					break;
				}
			}
		}

		// Prepare widget data
		$widgetData = [
			'id' => $widgetId ?: uniqid($groupId . '_', true),
			'groupId' => $groupId,
			'title' => $data['title'] ?? '',
			'icon' => $data['icon'] ?? '',
			'iconColor' => $data['iconColor'] ?? '',
			'url' => $data['url'] ?? '',
			'height' => $data['height'] ?? '',
			'extraWide' => $data['extraWide'] ?? false,
			'isDefault' => $data['isDefault'] ?? false
		];

		// If this widget is set as default, unset default flag from other widgets in the same group
		if ($widgetData['isDefault']) {
			foreach ($existingWidgets as &$widget) {
				if ($widget['groupId'] === $groupId && $widget['id'] !== $widgetData['id']) {
					$widget['isDefault'] = false;
				}
			}
		}

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


}
