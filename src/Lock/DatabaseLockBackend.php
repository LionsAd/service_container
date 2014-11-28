<?php

/**
 * @file
 * Contains \Drupal\service_provider\Lock\DatabaseLockBackend.
 */

namespace Drupal\service_provider\Lock;

use DatabaseConnection;
use Drupal\Core\Lock\DatabaseLockBackend as BaseDatabaseLockBackend;

/**
 * @see \Drupal\Core\Lock\DatabaseLockBackend
 *
 * @codeCoverageIgnore
 */
class DatabaseLockBackend extends BaseDatabaseLockBackend {

  /**
   * The database connection.
   *
   * @var \DatabaseConnection
   */
  protected $database;

  /**
   * Constructs a new DatabaseLockBackend.
   *
   * @param \DatabaseConnection $database
   *   The database connection.
   */
  public function __construct(DatabaseConnection $database) {
    // __destruct() is causing problems with garbage collections, register a
    // shutdown function instead.
    drupal_register_shutdown_function(array($this, 'releaseAll'));
    $this->database = $database;
  }

}

