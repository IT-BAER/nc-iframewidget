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

		foreach ($allKeys as $key) {
			if (str_starts_with($key, 'group_') && str_contains($key, '_')) {
				// Extract group ID and field from key like 'group_admins_widgetTitle'
				$parts = explode('_', $key, 3);
				if (count($parts) >= 3) {
					$groupId = $parts[1];
					$field = $parts[2];
					
					if (!isset($groupWidgets[$groupId])) {
						$groupWidgets[$groupId] = [
							'groupId' => $groupId,
							'widgetTitle' => '',
							'widgetIcon' => '',
							'widgetIconColor' => '',
							'iframeUrl' => '',
							'iframeHeight' => '',
							'extraWide' => 'false',
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
					$groupWidgets[$groupId][$field] = $value;
				}
			}
		}

		// Filter out widgets that have no meaningful data
		$filteredWidgets = [];
		foreach ($groupWidgets as $widget) {
			if (!empty($widget['iframeUrl']) || !empty($widget['widgetTitle']) || !empty($widget['widgetIcon'])) {
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

		// Save group widget configuration
		$fields = [
			'widgetTitle',
			'widgetIcon',
			'widgetIconColor',
			'iframeUrl',
			'iframeHeight',
			'extraWide'
		];

		foreach ($fields as $field) {
			if (isset($data[$field]) || array_key_exists($field, $data)) {
				$value = $data[$field] ?? '';
				// For boolean values that come as strings
				if ($field === 'extraWide' && ($value === true || $value === 'true')) {
					$value = 'true';
				} elseif ($field === 'extraWide' && ($value === false || $value === 'false')) {
					$value = 'false';
				}
				$this->config->setAppValue(Application::APP_ID, 'group_' . $groupId . '_' . $field, $value);
			}
		}

		return new DataResponse(['status' => 'success']);
	}

	/**
	 * Delete a group widget configuration
	 *
	 * @AdminRequired
	 * @param string $groupId Group ID
	 * @return DataResponse
	 */
	public function deleteGroupWidget(string $groupId): DataResponse {
		// Validate that the group exists
		$groupManager = $this->serverContainer->get(\OCP\IGroupManager::class);
		if (!$groupManager->groupExists($groupId)) {
			return new DataResponse(['status' => 'error', 'message' => 'Group does not exist'], 400);
		}

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

		return new DataResponse(['status' => 'success']);
	}


}
