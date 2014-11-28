<?php

/**
 * @file
 * Contains \Drupal\Core\Database\Database.
 */

namespace Drupal\Core\Database;

use \Database as BaseDatabase;

class Database {

  final public static function startLog($logging_key, $key = 'default') {
    return BaseDatabase::startLog($logging_key, $key);
  }

  final public static function getLog($logging_key, $key = 'default') {
    return BaseDatabase::getLog($logging_key, $key);
  }

  /**
   * @return \Drupal\Core\Database\Connection
   */
  final public static function getConnection($target = 'default', $key = NULL) {
    return new Connection(BaseDatabase::getConnection($target, $key));
  }

  final public static function isActiveConnection() {
    return BaseDatabase::isActiveConnection();
  }

  final public static function setActiveConnection($key = 'default') {
    return BaseDatabase::setActiveConnection($key);
  }

  final public static function parseConnectionInfo(array $info) {
    BaseDatabase::parseConnectionInfo();
  }

  final public static function addConnectionInfo($key, $target, array $info) {
    BaseDatabase::addConnectionInfo($key, $target, $info);
  }

  final public static function getConnectionInfo($key = 'default') {
    return BaseDatabase::getConnectionInfo($key);
  }

  final public static function getAllConnectionInfo() {
    throw new \Exception('not available/implemented in d7');
  }

  final public static function setMultipleConnectionInfo(array $databases) {
    throw new \Exception('not available/implemented yet in d7');
  }

  final public static function renameConnection($old_key, $new_key) {
    return BaseDatabase::getConnectionInfo($old_key, $new_key);
  }

  final public static function removeConnection($key) {
    return BaseDatabase::removeConnection($key);
  }

  final protected static function openConnection($key, $target) {
    throw new \Exception('not available/implemented yet in d7');
  }

  public static function closeConnection($target = NULL, $key = NULL) {
    throw new \Exception('not available/implemented yet in d7');
  }

  public static function ignoreTarget($key, $target) {
    BaseDatabase::ignoreTarget($key, $target);
  }

}
