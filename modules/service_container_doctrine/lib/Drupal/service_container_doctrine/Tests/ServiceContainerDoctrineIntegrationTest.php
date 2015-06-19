<?php

/**
 * @file
 * Contains \Drupal\service_container_doctrine\Tests\ServiceContainerDoctrineIntegrationTest.
 */

namespace Drupal\service_container_doctrine\Tests;

use Drupal\service_container\Tests\ServiceContainerIntegrationTestBase;
use Mockery\CountValidator\Exception;

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
        'type' => 'Plugin1',
        'name' => 'Plugin1A',
      ),
      array(
        'module' => 'service_container_doctrine_test',
        'type' => 'Plugin2',
        'name' => 'Plugin2B',
      ),
    );
    foreach($services as $service) {
      $plugin_manager = $this->container->get($service['module'] . '.' . $service['type']);
      $this->assertTrue($plugin_manager->hasDefinition($service['name']));
    }

    $service = array(
      'module' => 'service_container_doctrine_test',
      'type' => 'Plugin3',
      'name' => 'Plugin1C',
    );
    try {
      $this->container->get($service['module'] . '.' . $service['type']);
    } catch (Exception $e) {
      $this->fail("This should fail as the service doesn't exists.");
    }

    $service = array(
      'module' => 'service_container_doctrine_test',
      'type' => 'Plugin1',
      'name' => 'Plugin3A',
    );
    $plugin_manager = $this->container->get($service['module'] . '.' . $service['type']);
    $this->assertFalse($plugin_manager->hasDefinition($service['name']));
  }
}
