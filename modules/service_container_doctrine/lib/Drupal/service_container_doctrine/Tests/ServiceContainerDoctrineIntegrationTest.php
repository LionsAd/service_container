<?php

/**
 * @file
 * Contains \Drupal\service_container_doctrine\Tests\ServiceContainerDoctrineIntegrationTest.
 */

namespace Drupal\service_container_doctrine\Tests;

use Drupal\service_container\Tests\ServiceContainerIntegrationTestBase;
use Mockery\CountValidator\Exception;
use Symfony\Component\Yaml\Exception\RuntimeException;

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
    $plugins = array(
      array(
        'module' => 'sc_doctrine_test',
        'type' => 'Plugin1',
        'name' => 'Plugin1A',
      ),
      array(
        'module' => 'sc_doctrine_test',
        'type' => 'Plugin2',
        'name' => 'Plugin2B',
      ),
    );
    foreach($plugins as $plugin) {
      $plugin_manager = $this->container->get($plugin['module'] . '.' . $plugin['type']);
      $this->assertTrue($plugin_manager->hasDefinition($plugin['name']));
    }

    $plugin = array(
      'module' => 'sc_doctrine_test',
      'type' => 'Plugin3',
      'name' => 'Plugin1C',
    );
    try {
      $this->container->get($plugin['module'] . '.' . $plugin['type']);
    }  catch (\RuntimeException $e) {
      $this->pass('Bad input correctly threw an exception');
    }

    $plugin = array(
      'module' => 'sc_doctrine_test',
      'type' => 'Plugin1',
      'name' => 'Plugin3A',
    );
    $plugin_manager = $this->container->get($plugin['module'] . '.' . $plugin['type']);
    $this->assertFalse($plugin_manager->hasDefinition($plugin['name']));
  }
}
