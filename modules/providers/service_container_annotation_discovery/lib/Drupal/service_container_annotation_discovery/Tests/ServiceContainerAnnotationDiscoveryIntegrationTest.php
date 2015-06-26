<?php

/**
 * @file
 * Contains \Drupal\service_container_annotation_discovery\Tests\ServiceContainerBlockIntegrationTest.
 */

namespace Drupal\service_container_annotation_discovery\Tests;

use Drupal\Component\Plugin\PluginBase;
use Drupal\service_container\Messenger\MessengerInterface;
use Drupal\service_container\Tests\ServiceContainerIntegrationTestBase;
use Mockery\CountValidator\Exception;
use Symfony\Component\Yaml\Exception\RuntimeException;

class ServiceContainerAnnotationDiscoveryIntegrationTest extends ServiceContainerIntegrationTestBase {

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    $modules[] = 'service_container_annotation_discovery_test';
    $modules[] = 'service_container_annotation_discovery_subtest';
    parent::setUp($modules);
    $this->container = \Drupal::getContainer();
  }

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => 'ServiceContainerAnnotationDirectoryIntegrationTest',
      'description' => 'Tests the \ServiceContainer Annotation Discovery class',
      'group' => 'service_container',
    );
  }

  /**
   * Tests if plugins with annotations are available as services.
   */
  public function testDoctrinePlugin() {
    $plugins = array(
      array(
        'owner' => 'sc_doctrine_test',
        'type' => 'Plugin1',
        'name' => 'Plugin1A',
      ),
      array(
        'owner' => 'sc_doctrine_test',
        'type' => 'Plugin2',
        'name' => 'Plugin2B',
      ),
    );
    foreach($plugins as $plugin) {
      $plugin_manager = $this->container->get($plugin['owner'] . '.' . $plugin['type']);
      $this->assertTrue($plugin_manager->hasDefinition($plugin['name']));
      $object = $plugin_manager->createInstance($plugin['name']);
      $this->assertTrue($object instanceof PluginBase);
    }

    $plugin = array(
      'owner' => 'sc_doctrine_test',
      'type' => 'Plugin3',
      'name' => 'Plugin1C',
    );
    try {
      $this->container->get($plugin['owner'] . '.' . $plugin['type']);
    }  catch (\RuntimeException $e) {
      $this->pass('Bad input correctly threw an exception');
    }

    $plugin = array(
      'owner' => 'sc_doctrine_test',
      'type' => 'Plugin1',
      'name' => 'Plugin3A',
    );
    $plugin_manager = $this->container->get($plugin['owner'] . '.' . $plugin['type']);
    $this->assertFalse($plugin_manager->hasDefinition($plugin['name']));

    $plugin = array(
      'owner' => 'sc_doctrine_test',
      'type' => 'Plugin3',
      'name' => 'Plugin3A',
    );
    $plugin_manager = $this->container->get($plugin['owner'] . '.' . $plugin['type']);
    $this->assertTrue($plugin_manager->hasDefinition($plugin['name']));
    $object = $plugin_manager->createInstance($plugin['name']);
    $this->assertEqual($object->getData(), 'Hello world!');

    $plugin = array(
      'owner' => 'sc_doctrine_test',
      'type' => 'Plugin4',
      'name' => 'Plugin4A',
    );
    $plugin_manager = $this->container->get($plugin['owner'] . '.' . $plugin['type']);
    $this->assertTrue($plugin_manager->hasDefinition($plugin['name']));
    $object = $plugin_manager->createInstance($plugin['name']);
    $this->assertTrue($object->getMessenger() instanceof MessengerInterface);
  }

  /**
   * Tests if multiple module with plugins annotations are available as services.
   */
  function testMultiple() {
    $plugins = array(
      array(
        'owner' => 'sc_doctrine_test',
        'type' => 'Plugin1',
        'name' => 'Plugin1A',
      ),
      array(
        'owner' => 'sc_doctrine_test',
        'type' => 'Plugin5',
        'name' => 'Plugin5B',
      ),
    );
    foreach ($plugins as $plugin) {
      $plugin_manager = $this->container->get($plugin['owner'] . '.' . $plugin['type']);
      $this->assertTrue($plugin_manager->hasDefinition($plugin['name']));
      $object = $plugin_manager->createInstance($plugin['name']);
      $this->assertTrue($object instanceof PluginBase);
    }
  }

  /**
   * Tests plugin_manager_name key in definition.
   */
  function testPluginManagerKey() {
    $plugins = array(
      array(
        'plugin_manager_name' => 'drupal8ftw',
        'name' => 'Plugin5A',
      ),
    );
    foreach ($plugins as $plugin) {
      $plugin_manager = $this->container->get($plugin['plugin_manager_name']);
      $this->assertTrue($plugin_manager->hasDefinition($plugin['name']));
      $object = $plugin_manager->createInstance($plugin['name']);
      $this->assertTrue($object instanceof PluginBase);
    }
  }
}
