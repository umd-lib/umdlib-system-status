<?php

namespace Drupal\Tests\umdlib_system_status\Unit;

use Drupal\Core\Cache\CacheableJsonResponse;
use Drupal\Tests\UnitTestCase;
use Drupal\umdlib_system_status\Controller\SystemStatusController;
use Drupal\umdlib_system_status\Form\SystemStatusSettingsForm;
use Symfony\Component\HttpFoundation\Request;

/**
 * Unit tests for SystemStatusController.
 *
 * @group umdlib_system_status
 */
class SystemStatusControllerTest extends UnitTestCase {

  /**
   * Test the errorResponse method returns JSON with error message.
   */
  public function testErrorResponse() {
    $controller = new SystemStatusController();
    $reflection = new \ReflectionClass($controller);
    $method = $reflection->getMethod('errorResponse');
    $method->setAccessible(TRUE);

    $result = $method->invoke($controller, 'Test error message');

    $this->assertIsArray($result->getContent());
    $content = json_decode($result->getContent(), TRUE);
    $this->assertEquals('Test error message', $content['error']);
  }

  /**
   * Test getSystemStatusUrl retrieves the configured URL.
   */
  public function testGetSystemStatusUrl() {
    $mockConfig = $this->createMock('Drupal\Core\Config\ImmutableConfig');
    $mockConfig->method('get')
      ->with(SystemStatusSettingsForm::SERVICE_URL_FIELD)
      ->willReturn('https://example.com/status');

    $controller = new SystemStatusController();
    $reflection = new \ReflectionClass($controller);
    
    // Set the config property via reflection
    $configProperty = $reflection->getProperty('config');
    $configProperty->setAccessible(TRUE);
    $configProperty->setValue($controller, $mockConfig);

    $method = $reflection->getMethod('getSystemStatusUrl');
    $method->setAccessible(TRUE);

    $result = $method->invoke($controller);
    $this->assertEquals('https://example.com/status', $result);
  }

  /**
   * Test getSystemStatusUrl returns null when not configured.
   */
  public function testGetSystemStatusUrlNotConfigured() {
    $mockConfig = $this->createMock('Drupal\Core\Config\ImmutableConfig');
    $mockConfig->method('get')
      ->with(SystemStatusSettingsForm::SERVICE_URL_FIELD)
      ->willReturn(NULL);

    $controller = new SystemStatusController();
    $reflection = new \ReflectionClass($controller);
    
    $configProperty = $reflection->getProperty('config');
    $configProperty->setAccessible(TRUE);
    $configProperty->setValue($controller, $mockConfig);

    $method = $reflection->getMethod('getSystemStatusUrl');
    $method->setAccessible(TRUE);

    $result = $method->invoke($controller);
    $this->assertNull($result);
  }

  /**
   * Test error response when no upstream URL is configured.
   */
  public function testGetJsonMissingConfiguration() {
    $mockConfig = $this->createMock('Drupal\Core\Config\ImmutableConfig');
    $mockConfig->method('get')
      ->with(SystemStatusSettingsForm::SERVICE_URL_FIELD)
      ->willReturn(NULL);

    $controller = new SystemStatusController();
    $reflection = new \ReflectionClass($controller);
    
    $configProperty = $reflection->getProperty('config');
    $configProperty->setAccessible(TRUE);
    $configProperty->setValue($controller, $mockConfig);

    $request = new Request();
    $response = $controller->getJson($request);
    
    $this->assertTrue($response->isOk());
    $content = json_decode($response->getContent(), TRUE);
    $this->assertArrayHasKey('error', $content);
    $this->assertStringContainsString('Configuration', $content['error']);
  }

}
