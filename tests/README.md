# System Status Module Tests

This directory contains unit and functional tests for the umdlib_system_status module.

## Running Tests

### Run all tests for this module

```bash
cd /path/to/site
php ./vendor/bin/phpunit modules/path/to/system_status/tests/
```

### Run specific test file

```bash
php ./vendor/bin/phpunit modules/path/to/system_status/tests/src/Unit/SystemStatusControllerTest.php
```

### Run tests with specific group

```bash
php ./vendor/bin/phpunit --group umdlib_system_status
```

## Test Coverage

### SystemStatusControllerTest

* `testErrorResponse` - Verifies error responses return JSON with error messages
* `testGetSystemStatusUrl` - Tests retrieval of configured service URL
* `testGetSystemStatusUrlNotConfigured` - Verifies null return when URL not configured
* `testGetJsonMissingConfiguration` - Tests error handling when upstream URL is
missing

### SystemStatusSettingsFormTest

* `testGetFormId` - Verifies correct form ID is returned
* `testGetEditableConfigNames` - Tests editable config names retrieval
* `testValidateFormInvalidUrlNoProtocol` - Tests validation fails for URLs
    without protocol
* `testValidateFormValidHttpsUrl` - Tests validation passes for HTTPS URLs
* `testValidateFormValidHttpUrl` - Tests validation passes for HTTP URLs
* `testValidateFormEmptyUrl` - Tests validation passes for empty URLs
* `testConstants` - Verifies module constants are correctly defined

## Test Design

* **Unit Tests**: Test individual methods and components in isolation using mocks
* **Mock Objects**: Drupal services (ConfigFactory, Config) are mocked to avoid
    dependencies
* **Reflection**: Used to test protected/private methods
* **Group Tag**: All tests are tagged with `@group umdlib_system_status` for easy
filtering
