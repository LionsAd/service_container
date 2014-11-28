<?php

/**
 * @file
 * Contains \Drupal\Core\Database\Connection.
 */

namespace Drupal\Core\Database;

/**
 */
class Connection {

  /**
   * @var \DatabaseConnection
   */
  protected $connection;

  public function __construct(\DatabaseConnection $connection) {
    $this->connection = $connection;
  }

  public static function open(array &$connection_options = array()) {
    throw new \Exception('not implemented yet');
  }

  public function destroy() {
    $this->connection->destroy();
  }

  public function getConnectionOptions() {
    return $this->connection->getConnectionOptions();
  }

  public function prefixTables($sql) {
    return $this->connection->prefixTables($sql);
  }

  public function tablePrefix($table = 'default') {
    return $this->connection->tablePrefix($table);
  }

  public function prepareQuery($query) {
    return $this->connection->prepareQuery($query);
  }

  public function setTarget($target = NULL) {
    return $this->connection->setTarget($target);
  }

  public function getTarget() {
    return $this->connection->getTarget();
  }

  public function setKey($key) {
    return $this->connection->setKey($key);
  }

  public function getKey() {
    return $this->connection->getKey();
  }

  public function setLogger(Log $logger) {
    return $this->connection->setLogger($logger);
  }

  public function getLogger() {
    return $this->connection->getLogger();
  }

  public function makeSequenceName($table, $field) {
    return $this->connection->makeSequenceName($table, $field);
  }

  public function makeComment($comments) {
    return $this->connection->makeComment($comments);
  }

  public function query($query, array $args = array(), $options = array()) {
    return $this->connection->query($query, $args, $options);
  }

  public function getDriverClass($class) {
    return $this->connection->getDriverClass($class);
  }

  public function select($table, $alias = NULL, array $options = array()) {
    return $this->connection->select($table, $alias, $options);
  }

  public function insert($table, array $options = array()) {
    return $this->connection->insert($table, $options);
  }

  public function merge($table, array $options = array()) {
    return $this->connection->merge($table, $options);
  }

  public function update($table, array $options = array()) {
    return $this->connection->update($table, $options);
  }

  public function delete($table, array $options = array()) {
    return $this->connection->delete($table, $options);
  }

  public function truncate($table, array $options = array()) {
    return $this->connection->truncate($table, $options);
  }

  public function schema() {
    return $this->connection->schema();
  }

  public function escapeDatabase($database) {
    return preg_replace('/[^A-Za-z0-9_.]+/', '', $database);
  }

  public function escapeTable($table) {
    return $this->connection->escapeTable($table);
  }

  public function escapeField($field) {
    return $this->connection->escapeField($field);
  }

  public function escapeAlias($field) {
    return $this->connection->escapeAlias($field);
  }

  public function escapeLike($string) {
    return $this->connection->escapeLike($string);
  }

  public function inTransaction() {
    return $this->connection->inTransaction();
  }

  public function transactionDepth() {
    return $this->connection->transactionDepth();
  }

  public function startTransaction($name = '') {
    return $this->connection->startTransaction($name);
  }

  public function rollback($savepoint_name = 'drupal_transaction') {
    return $this->connection->rollback($savepoint_name);
  }

  public function pushTransaction($name) {
    return $this->connection->pushTransaction($name);
  }

  public function popTransaction($name) {
    return $this->connection->popTransaction($name);
  }

  public function queryRange($query, $from, $count, array $args = array(), array $options = array()) {
    return $this->connection->queryRange($query, $from, $count, $args, $options);
  }

  public function queryTemporary($query, array $args = array(), array $options = array()) {
    return $this->connection->queryTemporary($query, $args, $options);
  }

  public function driver() {
    return $this->connection->driver();
  }

  public function version() {
    return $this->connection->version();
  }

  public function supportsTransactions() {
    return $this->connection->supportsTransactions();
  }

  public function supportsTransactionalDDL() {
    return $this->connection->supportsTransactionalDDL();
  }

  public function databaseType() {
    return $this->connection->databaseType();
  }

  public function createDatabase($database) {
    throw new \Exception('Create database is not implemented.');
  }

  public function mapConditionOperator($operator) {
    return $this->connection->mapConditionOperator($operator);
  }

  public function commit() {
    return $this->connection->commit();
  }

  public function nextId($existing_id = 0) {
    return $this->connection->nextId($existing_id);
  }

  public function prepare($statement, array $driver_options = array()) {
    return $this->connection->nextId($statement, $driver_options);
  }

  public function quote($string, $parameter_type = \PDO::PARAM_STR) {
    return $this->connection->quote($string, $parameter_type);
  }

  public function serialize() {
    throw new \Exception('Serialize is not implemented yet.');
    $connection = clone $this;
    // Don't serialize the PDO connection and other lazy-instantiated members.
    unset($connection->connection, $connection->schema, $connection->driverClasses);
    return serialize(get_object_vars($connection));
  }

  public function unserialize($serialized) {
    throw new \Exception('unserialize is not implemented yet.');
    $data = unserialize($serialized);
    foreach ($data as $key => $value) {
      $this->{$key} = $value;
    }
    // Re-establish the PDO connection using the original options.
    $this->connection = static::open($this->connectionOptions);

    // Re-set a Statement class if necessary.
    if (!empty($this->statementClass)) {
      $this->connection->setAttribute(\PDO::ATTR_STATEMENT_CLASS, array($this->statementClass, array($this)));
    }
  }

}
