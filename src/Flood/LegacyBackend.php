<?php

/**
 * @file
 * Definition of Drupal\service_container\Flood\LegacyBackend.
 */

namespace Drupal\service_container\Flood;

use Drupal\Core\Database\Connection;
use Drupal\Core\Flood\FloodInterface;
use Drupal\service_container\Legacy\Drupal7;

/**
 * Defines the database flood backend. This is the default Drupal backend.
 * @codeCoverageIgnore
 */
class LegacyBackend implements FloodInterface {

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
   *
   * Registers an event for the current visitor to the flood control mechanism.
   *
   * @param $name
   *   The name of an event.
   * @param $window
   *   Optional number of seconds before this event expires. Defaults to 3600 (1
   *   hour). Typically uses the same value as the flood_is_allowed() $window
   *   parameter. Expired events are purged on cron run to prevent the flood table
   *   from growing indefinitely.
   * @param $identifier
   *   Optional identifier (defaults to the current user's IP address).
   */
  public function register($name, $window = 3600, $identifier = NULL) {
    $this->drupal7->flood_register_event($name, $window, $identifier);
  }

  /**
   * Implements Drupal\Core\Flood\FloodInterface::clear().
   *
   * Makes the flood control mechanism forget an event for the current visitor.
   *
   * @param $name
   *   The name of an event.
   * @param $identifier
   *   Optional identifier (defaults to the current user's IP address).
   */
  public function clear($name, $identifier = NULL) {
    $this->drupal7->flood_clear_event($name, $identifier);
  }

  /**
   * Implements Drupal\Core\Flood\FloodInterface::isAllowed().
   *
   * Checks whether a user is allowed to proceed with the specified event.
   *
   * Events can have thresholds saying that each user can only do that event
   * a certain number of times in a time window. This function verifies that the
   * current user has not exceeded this threshold.
   *
   * @param $name
   *   The unique name of the event.
   * @param $threshold
   *   The maximum number of times each user can do this event per time window.
   * @param $window
   *   Number of seconds in the time window for this event (default is 3600
   *   seconds, or 1 hour).
   * @param $identifier
   *   Unique identifier of the current user. Defaults to their IP address.
   *
   * @return bool
   *   TRUE if the user is allowed to proceed. FALSE if they have exceeded the
   *   threshold and should not be allowed to proceed.
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
