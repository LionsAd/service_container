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
        'plugin' => 'PluginA',
      ),
      array(
        'module' => 'service_container_doctrine_test',
        'plugin' => 'PluginB',
      ),
    );
    foreach($services as $service) {
      $this->assertTrue($this->container->has($service['module']), "Container has plugin manager " . $service['module']);
      $this->assertTrue($this->container->hasDefinition($service['module'] . '.internal.' . $service['plugin']), "Container has plugin definition " . $service['plugin']);
    }

    $service = array(
      'module' => 'service_container_doctrine_test',
      'plugin' => 'PluginC',
    );
    $this->assertFalse($this->container->hasDefinition($service['module'] . '.internal.' . $service['plugin']), "Container has plugin definition " . $service['plugin']);
  }
}
