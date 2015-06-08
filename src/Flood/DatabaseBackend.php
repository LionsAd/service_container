<?php

/**
 * @file
 * Definition of Drupal\service_container\Flood\DatabaseBackend.
 */

namespace Drupal\service_container\Flood;

use Drupal\service_container\Legacy\Drupal7;
use Drupal\Core\Database\Connection;
use Drupal\Core\Flood\FloodInterface;

/**
 * Defines the database flood backend. This is the default Drupal backend.
 */
class DatabaseBackend implements FloodInterface {

  /**
   * The database connection used to store flood event information.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * The Drupal 7 legacy service.
   *
   * @var \Drupal\service_container\Legacy\Drupal7
   */
  protected $drupal7;

  /**
   * Construct the DatabaseBackend.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection which will be used to store the flood event
   *   information.
   * @param \Drupal\service_container\Legacy\Drupal7
   *   The Drupal 7 legacy service.
   */
  public function __construct(Connection $connection, Drupal7 $drupal7) {
    $this->connection = $connection;
    $this->drupal7 = $drupal7;
  }

  /**
   * Implements Drupal\Core\Flood\FloodInterface::register().
   */
  public function register($name, $window = 3600, $identifier = NULL) {
    $this->drupal7->flood_register_event($name, $window, $identifier);
  }

  /**
   * Implements Drupal\Core\Flood\FloodInterface::clear().
   */
  public function clear($name, $identifier = NULL) {
    $this->drupal7->flood_clear_event($name, $identifier);
  }

  /**
   * Implements Drupal\Core\Flood\FloodInterface::isAllowed().
   */
  public function isAllowed($name, $threshold, $window = 3600, $identifier = NULL) {
    return $this->drupal7->flood_is_allowed($name, $threshold, $window, $identifier);
  }

  /**
   * Implements Drupal\Core\Flood\FloodInterface::garbageCollection().
   */
  public function garbageCollection() {
    return $this->connection->delete('flood')
      ->condition('expiration', REQUEST_TIME, '<')
      ->execute();
  }

}
