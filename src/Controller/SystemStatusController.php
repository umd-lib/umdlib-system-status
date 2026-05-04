<?php
/**
 * @file
 * Definition of Drupal\umdlib_system_status\Controller\SystemStatusController
 */

 namespace Drupal\umdlib_system_status\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Cache\CacheableJsonResponse;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\umdlib_system_status\Form\SystemStatusSettingsForm;
 /**
  * Implementation of SystemStatusController
  */
  class SystemStatusController extends ControllerBase {

    protected $config;
  
    public function __construct() {
      $this->config = \Drupal::config(SystemStatusSettingsForm::SETTINGS);
    }

    public function getJson(Request $request) {
      // Check for cached response
      $cache_id = 'umdlib_system_status:json';
      $cache = \Drupal::cache()->get($cache_id);
      if ($cache) {
        return $cache->data;
      }

      // Get the upstream status url
      $status_url = $this->getSystemStatusUrl();
      if ($status_url == null || $status_url == '') {
        return $this->errorResponse("Configuration of the upstream system status service url missing!");
      }
      // Get the status
      $curl = curl_init();
      try {
        curl_setopt($curl, CURLOPT_URL, $status_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
        $output = curl_exec($curl);

        if (curl_errno($curl)) {
          return $this->errorResponse("The curl request to $status_url failed with code: "
            . curl_errno($curl));
        } elseif (curl_getinfo($curl, CURLINFO_HTTP_CODE) != 200) {
          return $this->errorResponse("The upstream request $status_url failed with HTTP status code: "
            . curl_getinfo($curl, CURLINFO_HTTP_CODE));
        } else {
          $data = json_decode($output, true);
          if ($data === null) {
            return $this->errorResponse("The response from $status_url is not a valid JSON string.");
          }
          $data['#cache'] = [
            'max-age' => 300, 
            'contexts' => [
              'url',
            ],
          ];
          $response = new CacheableJsonResponse($data);
          $response->addCacheableDependency(CacheableMetadata::createFromRenderArray($data));
          
          // Cache the response for 5 minutes (300 seconds)
          \Drupal::cache()->set($cache_id, $response, time() + 300);
          
          return $response;
        }
      } finally {
        curl_close($curl);
      }
    }

    private function errorResponse($message) {
      return new JsonResponse([
        'error' => $message
      ]);
    }

    protected function getSystemStatusUrl() {
      return $this->config->get(SystemStatusSettingsForm::SERVICE_URL_FIELD);
    }
  }
