<?php

/**
 * @file
 * Contains Drupal\Core\KeyValueStore\DatabaseStorageExpirable.
 */

namespace Drupal\Core\KeyValueStore;

use Drupal\Component\Serialization\SerializationInterface;
use Drupal\Core\Database\Query\Merge;
use Drupal\Core\DestructableInterface;
use Drupal\Core\Database\Connection;

/**
 * Defines a default key/value store implementation for expiring items.
 *
 * This key/value store implementation uses the database to store key/value
 * data with an expire date.
 */
class DatabaseStorageExpirable extends DatabaseStorage implements KeyValueStoreExpirableInterface, DestructableInterface {

  /**
   * Flag indicating whether garbage collection should be performed.
   *
   * When this flag is TRUE, garbage collection happens at the end of the
   * request when the object is destructed. The flag is set during set and
   * delete operations for expirable data, when a write to the table is already
   * being performed. This eliminates the need for an external system to remove
   * stale data.
   *
   * @var bool
   */
  protected $needsGarbageCollection = FALSE;

  /**
   * Overrides Drupal\Core\KeyValueStore\StorageBase::__construct().
   *
   * @param string $collection
   *   The name of the collection holding key and value pairs.
   * @param \Drupal\Component\Serialization\SerializationInterface $serializer
   *   The serialization class to use.
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection to use.
   * @param string $table
   *   The name of the SQL table to use, defaults to key_value_expire.
   */
  public function __construct($collection, SerializationInterface $serializer, Connection $connection, $table = 'key_value_expire') {
    parent::__construct($collection, $serializer, $connection, $table);
  }

  /**
   * {@inheritdoc}
   */
  public function has($key) {
    return (bool) $this->connection->query('SELECT 1 FROM {' . $this->connection->escapeTable($this->table) . '} WHERE collection = :collection AND name = :key AND expire > :now', array(
      ':collection' => $this->collection,
      ':key' => $key,
      ':now' => REQUEST_TIME,
    ))->fetchField();
  }

  /**
   * Implements Drupal\Core\KeyValueStore\KeyValueStoreInterface::getMultiple().
   */
  public function getMultiple(array $keys) {
    $values = $this->connection->query(
      'SELECT name, value FROM {' . $this->connection->escapeTable($this->table) . '} WHERE expire > :now AND name IN (:keys) AND collection = :collection',
      array(
        ':now' => REQUEST_TIME,
        ':keys' => $keys,
        ':collection' => $this->collection,
      ))->fetchAllKeyed();
    return array_map(array($this->serializer, 'decode'), $values);
  }

  /**
   * Implements Drupal\Core\KeyValueStore\KeyValueStoreInterface::getAll().
   */
  public function getAll() {
    $values = $this->connection->query(
      'SELECT name, value FROM {' . $this->connection->escapeTable($this->table) . '} WHERE collection = :collection AND expire > :now',
      array(
        ':collection' => $this->collection,
        ':now' => REQUEST_TIME
      ))->fetchAllKeyed();
    return array_map(array($this->serializer, 'decode'), $values);
  }

  /**
   * {@inheritdoc}
   */
  function setWithExpire($key, $value, $expire) {
    // We are already writing to the table, so perform garbage collection at
    // the end of this request.
    $this->needsGarbageCollection = TRUE;
    $this->connection->merge($this->table)
      ->keys(array(
        'name' => $key,
        'collection' => $this->collection,
      ))
      ->fields(array(
        'value' => $this->serializer->encode($value),
        'expire' => REQUEST_TIME + $expire,
      ))
      ->execute();
  }

  /**
   * Implements Drupal\Core\KeyValueStore\KeyValueStoreExpirableInterface::setWithExpireIfNotExists().
   */
  function setWithExpireIfNotExists($key, $value, $expire) {
    // We are already writing to the table, so perform garbage collection at
    // the end of this request.
    $this->needsGarbageCollection = TRUE;
    $result = $this->connection->merge($this->table)
      ->insertFields(array(
        'collection' => $this->collection,
        'name' => $key,
        'value' => $this->serializer->encode($value),
        'expire' => REQUEST_TIME + $expire,
      ))
      ->condition('collection', $this->collection)
      ->condition('name', $key)
      ->execute();
    return $result == Merge::STATUS_INSERT;
  }

  /**
   * {@inheritdoc}
   */
  function setMultipleWithExpire(array $data, $expire) {
    foreach ($data as $key => $value) {
      $this->setWithExpire($key, $value, $expire);
    }
  }

  /**
   * Implements Drupal\Core\KeyValueStore\KeyValueStoreInterface::deleteMultiple().
   */
  public function deleteMultiple(array $keys) {
    // We are already writing to the table, so perform garbage collection at
    // the end of this request.
    $this->needsGarbageCollection = TRUE;
    parent::deleteMultiple($keys);
  }

  /**
   * Deletes expired items.
   */
  protected function garbageCollection() {
    $this->connection->delete($this->table)
      ->condition('expire', REQUEST_TIME, '<')
      ->execute();
  }

  /**
   * Implements Drupal\Core\DestructableInterface::destruct().
   */
  public function destruct() {
    if ($this->needsGarbageCollection) {
      $this->garbageCollection();
    }
  }

}
