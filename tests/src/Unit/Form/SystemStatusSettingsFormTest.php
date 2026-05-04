<?php

namespace Drupal\Tests\umdlib_system_status\Unit\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Tests\UnitTestCase;
use Drupal\umdlib_system_status\Form\SystemStatusSettingsForm;

/**
 * Unit tests for SystemStatusSettingsForm.
 *
 * @group umdlib_system_status
 */
class SystemStatusSettingsFormTest extends UnitTestCase {

  /**
   * Test getFormId returns the correct form ID.
   */
  public function testGetFormId() {
    $mockConfigFactory = $this->createMock('Drupal\Core\Config\ConfigFactoryInterface');
    $mockConfigFactory->method('getEditable')
      ->willReturn($this->createMock('Drupal\Core\Config\Config'));
    $mockConfigFactory->method('get')
      ->willReturn($this->createMock('Drupal\Core\Config\ImmutableConfig'));

    $form = new SystemStatusSettingsForm($mockConfigFactory);
    $this->assertEquals('system-status-settings-form', $form->getFormId());
  }

  /**
   * Test getEditableConfigNames returns the correct config names.
   */
  public function testGetEditableConfigNames() {
    $mockConfigFactory = $this->createMock('Drupal\Core\Config\ConfigFactoryInterface');
    $mockConfigFactory->method('getEditable')
      ->willReturn($this->createMock('Drupal\Core\Config\Config'));
    $mockConfigFactory->method('get')
      ->willReturn($this->createMock('Drupal\Core\Config\ImmutableConfig'));

    $form = new SystemStatusSettingsForm($mockConfigFactory);
    $reflection = new \ReflectionClass($form);
    $method = $reflection->getMethod('getEditableConfigNames');
    $method->setAccessible(TRUE);

    $result = $method->invoke($form);
    $this->assertContains(SystemStatusSettingsForm::SETTINGS, $result);
  }

  /**
   * Test validateForm with invalid URL (no protocol).
   */
  public function testValidateFormInvalidUrlNoProtocol() {
    $mockConfigFactory = $this->createMock('Drupal\Core\Config\ConfigFactoryInterface');
    $mockConfigFactory->method('getEditable')
      ->willReturn($this->createMock('Drupal\Core\Config\Config'));
    $mockConfigFactory->method('get')
      ->willReturn($this->createMock('Drupal\Core\Config\ImmutableConfig'));

    $form = [
      SystemStatusSettingsForm::SERVICE_URL_FIELD => [
        '#type' => 'url',
      ],
    ];

    $formState = $this->createMock(FormStateInterface::class);
    $formState->method('getValue')
      ->with(SystemStatusSettingsForm::SERVICE_URL_FIELD)
      ->willReturn('example.com/status');
    $formState->expects($this->once())
      ->method('setErrorByName')
      ->with(
        SystemStatusSettingsForm::SERVICE_URL_FIELD,
        $this->stringContainsString('http')
      );

    $settingsForm = new SystemStatusSettingsForm($mockConfigFactory);
    $settingsForm->validateForm($form, $formState);
  }

  /**
   * Test validateForm with valid HTTPS URL.
   */
  public function testValidateFormValidHttpsUrl() {
    $mockConfigFactory = $this->createMock('Drupal\Core\Config\ConfigFactoryInterface');
    $mockConfigFactory->method('getEditable')
      ->willReturn($this->createMock('Drupal\Core\Config\Config'));
    $mockConfigFactory->method('get')
      ->willReturn($this->createMock('Drupal\Core\Config\ImmutableConfig'));

    $form = [
      SystemStatusSettingsForm::SERVICE_URL_FIELD => [
        '#type' => 'url',
      ],
    ];

    $formState = $this->createMock(FormStateInterface::class);
    $formState->method('getValue')
      ->with(SystemStatusSettingsForm::SERVICE_URL_FIELD)
      ->willReturn('https://example.com/status');
    $formState->expects($this->never())
      ->method('setErrorByName');

    $settingsForm = new SystemStatusSettingsForm($mockConfigFactory);
    $settingsForm->validateForm($form, $formState);
  }

  /**
   * Test validateForm with valid HTTP URL.
   */
  public function testValidateFormValidHttpUrl() {
    $mockConfigFactory = $this->createMock('Drupal\Core\Config\ConfigFactoryInterface');
    $mockConfigFactory->method('getEditable')
      ->willReturn($this->createMock('Drupal\Core\Config\Config'));
    $mockConfigFactory->method('get')
      ->willReturn($this->createMock('Drupal\Core\Config\ImmutableConfig'));

    $form = [
      SystemStatusSettingsForm::SERVICE_URL_FIELD => [
        '#type' => 'url',
      ],
    ];

    $formState = $this->createMock(FormStateInterface::class);
    $formState->method('getValue')
      ->with(SystemStatusSettingsForm::SERVICE_URL_FIELD)
      ->willReturn('http://example.com/status');
    $formState->expects($this->never())
      ->method('setErrorByName');

    $settingsForm = new SystemStatusSettingsForm($mockConfigFactory);
    $settingsForm->validateForm($form, $formState);
  }

  /**
   * Test validateForm with empty URL (should not error).
   */
  public function testValidateFormEmptyUrl() {
    $mockConfigFactory = $this->createMock('Drupal\Core\Config\ConfigFactoryInterface');
    $mockConfigFactory->method('getEditable')
      ->willReturn($this->createMock('Drupal\Core\Config\Config'));
    $mockConfigFactory->method('get')
      ->willReturn($this->createMock('Drupal\Core\Config\ImmutableConfig'));

    $form = [
      SystemStatusSettingsForm::SERVICE_URL_FIELD => [
        '#type' => 'url',
      ],
    ];

    $formState = $this->createMock(FormStateInterface::class);
    $formState->method('getValue')
      ->with(SystemStatusSettingsForm::SERVICE_URL_FIELD)
      ->willReturn('');
    $formState->expects($this->never())
      ->method('setErrorByName');

    $settingsForm = new SystemStatusSettingsForm($mockConfigFactory);
    $settingsForm->validateForm($form, $formState);
  }

  /**
   * Test constants are defined correctly.
   */
  public function testConstants() {
    $this->assertEquals('umdlib_system_status.settings', SystemStatusSettingsForm::SETTINGS);
    $this->assertEquals('service_url', SystemStatusSettingsForm::SERVICE_URL_FIELD);
  }

}
