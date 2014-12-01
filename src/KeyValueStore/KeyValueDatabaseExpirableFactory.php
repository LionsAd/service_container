<?php

/**
 * @file
 * Contains \Drupal\service_container\KeyValueStore\KeyValueDatabaseExpirableFactory.
 */

namespace Drupal\service_container\KeyValueStore;

/**
 * @codeCoverageIgnore
 */
class KeyValueDatabaseExpirableFactory extends \Drupal\Core\KeyValueStore\KeyValueDatabaseExpirableFactory {

  /**
   * {@inheritdoc}
   */
  public function get($collection) {
    if (!isset($this->storages[$collection])) {
      $this->storages[$collection] = new DatabaseStorageExpirable($collection, $this->serializer, $this->connection);
    }
    return $this->storages[$collection];
  }

}
