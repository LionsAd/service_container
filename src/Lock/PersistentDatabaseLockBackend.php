<?php

/**
 * @file
 * Contains \Drupal\service_provider\Lock\PersistentDatabaseLockBackend.
 */

namespace Drupal\service_provider\Lock;

use DatabaseConnection;

/**
 * @see \Drupal\Core\Lock\PersistentDatabaseLockBackend
 *
 * @codeCoverageIgnore
 */
class PersistentDatabaseLockBackend extends DatabaseLockBackend {

  /**
   * Constructs a new PersistentDatabaseLockBackend.
   *
   * @param \DatabaseConnection $database
   *   The database connection.
   */
  public function __construct(DatabaseConnection $database) {
    // Do not call the parent constructor to avoid registering a shutdown
    // function that releases all the locks at the end of a request.
    $this->database = $database;
    // Set the lockId to a fixed string to make the lock ID the same across
    // multiple requests. The lock ID is used as a page token to relate all the
    // locks set during a request to each other.
    // @see \Drupal\Core\Lock\LockBackendInterface::getLockId()
    $this->lockId = 'persistent';
  }

}
