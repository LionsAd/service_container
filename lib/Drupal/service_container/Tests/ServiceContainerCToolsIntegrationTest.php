<?php

/**
 * @file
 * Contains \Drupal\service_container\Tests\ServiceContainerCToolsIntegrationTest.
 */

namespace Drupal\service_container\Tests;

use Drupal\service_container\DependencyInjection\Container;

class ServiceContainerCToolsIntegrationTest extends ServiceContainerIntegrationTestBase {

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp('service_container_test_ctools');

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
   * Tests if CTools plugin types are available as services.
   */
  public function testCToolsPluginTypes() {
    foreach(ctools_plugin_get_plugin_type_info() as $module_name => $plugins) {
      if ($module_name != 'service_container_test_ctools') {
        continue;
      }
      foreach($plugins as $plugin_type => $plugin_data) {
        $services = array();
        $services[$module_name . '.' . $plugin_type] = TRUE;
        $services[$module_name . '.' . Container::underscore($plugin_type)] = TRUE;

        foreach($services as $service => $value) {
          $this->assertTrue($this->container->has($service), "Container has plugin manager $service for $module_name / $plugin_type.");
        }
      }
    }
  }

  /**
   * Tests if a particular CTools plugin is available.
   */
  public function testCToolsPlugin() {
    $service = \Drupal::service('service_container_test_ctools.ServiceContainerTestCtoolsPlugin')
      ->createInstance('ServiceContainerTestCtoolsPluginTest1');
    $this->assertEqual($service->beep(), 'beep!');

    try {
      $service = \Drupal::service('service_container_test_ctools.yolo')
        ->createInstance('yolo1');
    }
    catch (\Exception $e) {
      $this->pass('Non-existant plugin does not exist in the container.');
    }

    // @todo Test and fix instantiating non-class plugins.
  }
}
