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
    parent::setUp('service_container_test_ctools');

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
   * Tests if CTools plugin types are available as services.
   */
  public function testCToolsPluginTypes() {
    foreach(ctools_plugin_get_plugin_type_info() as $module_name => $plugins) {
      foreach($plugins as $plugin_type => $plugin_data) {
        $this->assertTrue(\Drupal::hasService($module_name . '.' . $plugin_type));
      }
    }
  }

  /**
   * Tests if a particular CTools plugin is available.
   */
  public function testCToolsPlugin() {
    $service = $this->container->get('service_container_test_ctools.ServiceContainerTestCtoolsPlugin')
      ->createInstance('ServiceContainerTestCtoolsPluginTest1');
    $this->assertTrue($service instanceof \Drupal\service_container_test_ctools\ServiceContainerTestCtoolsPlugin\ServiceContainerTestCtoolsPluginTest1);
    $this->assertEqual($service->beep(), 'beep!');
  }

}
