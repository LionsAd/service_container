<?php

/**
 * @file
 * Contains \Drupal\service_container\Tests\ServiceContainerCToolsIntegrationTest.
 */

namespace Drupal\service_container\Tests;

class ServiceContainerCToolsIntegrationTest extends ServiceContainerIntegrationTestBase {

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    // Support both an array of modules and a single module.
    $modules = func_get_args();
    if (isset($modules[0]) && is_array($modules[0])) {
      $modules = $modules[0];
    }

    $modules[] = 'service_container_test_ctools';

    parent::setUp($modules);

    \ServiceContainer::init();
    $this->container = \Drupal::getContainer();
  }

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => 'ServiceContainerCToolsIntegration',
      'description' => 'Tests the \ServiceContainer class (CTools integration)',
      'group' => 'service_container',
    );
  }

  /**
   * Tests if service is available
   */
  public function testInit() {
    $this->assertTrue(\Drupal::hasService('service_container_test_ctools'));
  }

  /**
   * Tests if CTools plugin is available
   */
  public function testCToolsPlugin() {
    $service = \Drupal::service('service_container_test_ctools')
      ->createInstance('ServiceContainerTestCtoolsPluginTest1');
    $this->assertTrue($service instanceof \Drupal\service_container_test_ctools\ServiceContainerTestCtoolsPlugin\ServiceContainerTestCtoolsPluginTest1);
    $this->assertTrue($service->beep() == 'beep!');
  }
}

