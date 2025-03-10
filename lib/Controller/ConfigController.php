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


}
