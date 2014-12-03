<?php

/**
 * @file
 * Contains \Drupal\service_container\KeyValueStore\DatabaseStorageExpirable.
 */

namespace Drupal\service_container\KeyValueStore;

/**
 * Overrides the KV store from Drupal to call MergeQuery::key instead of ::keys.
 *
 * @codeCoverageIgnore
 */
class DatabaseStorageExpirable extends \Drupal\Core\KeyValueStore\DatabaseStorageExpirable {

  /**
   * {@inheritdoc}
   */
  function setWithExpire($key, $value, $expire) {
    // We are already writing to the table, so perform garbage collection at
    // the end of this request.
    $this->needsGarbageCollection = TRUE;
    $this->connection->merge($this->table)
      ->key(array(
        'name' => $key,
        'collection' => $this->collection,
      ))
      ->fields(array(
        'value' => $this->serializer->encode($value),
        'expire' => REQUEST_TIME + $expire,
      ))
      ->execute();
  }

}
