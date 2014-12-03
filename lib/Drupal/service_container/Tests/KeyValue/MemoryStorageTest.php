<?php

/**
 * @file
 * Contains Drupal\system\Tests\KeyValueStore\MemoryStorageTest.
 */

namespace Drupal\service_container\Tests\KeyValue;

use Drupal\Core\KeyValueStore\KeyValueFactory;

/**
 * Tests the key-value memory storage.
 *
 * @group KeyValueStore
 */
class MemoryStorageTest extends StorageTestBase {

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => 'KeyValue MemoryStorage',
      'description' => 'Tests the key-value memory storage.',
      'group' => 'service_container',
    );
  }

  protected function setUp() {
    parent::setUp('service_container_test');

    \ServiceContainer::reset();

    $services = array();
    $services['keyvalue.memory'] = array(
      'class' => 'Drupal\Core\KeyValueStore\KeyValueMemoryFactory'
    );
    $parameters = array();
    $parameters['factory.keyvalue'][KeyValueFactory::DEFAULT_SETTING] = 'keyvalue.memory';

    $definition['services'] = $services;
    $definition['parameters'] = $parameters;

    variable_set('service_container_test_definition', $definition);

    \ServiceContainer::init();
  }

}
