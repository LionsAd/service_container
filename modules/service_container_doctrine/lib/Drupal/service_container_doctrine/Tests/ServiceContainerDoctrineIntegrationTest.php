<?php

/**
 * @file
 * Contains \Drupal\service_container_doctrine\Tests\ServiceContainerDoctrineIntegrationTest.
 */

namespace Drupal\service_container_doctrine\Tests;

use Drupal\service_container\Tests\ServiceContainerIntegrationTestBase;

class ServiceContainerDoctrineIntegrationTest extends ServiceContainerIntegrationTestBase {

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp('service_container_doctrine_test');

    $this->container = \Drupal::getContainer();
  }

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => 'ServiceContainerDoctrineIntegrationTest',
      'description' => 'Tests the \ServiceContainer class (Doctrine integration)',
      'group' => 'service_container',
    );
  }

  /**
   * Tests if plugins with annotations are available as services.
   */
  public function testDoctrinePlugin() {
    $services = array(
      array(
        'module' => 'service_container_doctrine_test',
        'type' => 'Plugin',
        'name' => 'PluginA',
      ),
      array(
        'module' => 'service_container_doctrine_test',
        'type' => 'Plugin',
        'name' => 'PluginB',
      ),
    );
    foreach($services as $service) {
      $this->assertTrue($this->container->has($service['module'] . '.' . $service['type']), "Container has plugin manager.");
      $this->assertTrue($this->container->hasDefinition($service['module']  . '.' . $service['type'] . '.internal.' . $service['name']), "Container has plugin definition.");
    }

    $service = array(
      'module' => 'service_container_doctrine_test',
      'type' => 'Plugin',
      'name' => 'PluginC',
    );
    $this->assertFalse($this->container->hasDefinition($service['module']  . '.' . $service['type'] . '.internal.' . $service['name']));
  }
}
