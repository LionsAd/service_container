<?php

/**
 * @file
 * Contains Drupal\system\Tests\KeyValueStore\DatabaseStorageTest.
 */

namespace Drupal\service_container\Tests\KeyValue;

/**
 * Tests the key-value database storage.
 *
 * @group KeyValueStore
 */
class DatabaseStorageTest extends StorageTestBase {

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => 'KeyValue Database',
      'description' => 'Tests the key-value database storage.',
      'group' => 'service_container',
    );
  }

}
