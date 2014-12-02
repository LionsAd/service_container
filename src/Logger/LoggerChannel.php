<?php

/**
 * @file
 * Contains \Drupal\service_container\Logger\LoggerChannel.
 */

namespace Drupal\service_container\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Defines a logger channel that most implementations will use.
 */
class LoggerChannel implements LoggerChannelInterface {

  /**
   * The name of the channel of this logger instance.
   *
   * @var string
   */
  protected $channel;

  /**
   * An array of arrays of \Psr\Log\LoggerInterface keyed by priority.
   *
   * @var \Psr\Log\LoggerInterface[][]
   */
  protected $loggers = array();

  /**
   * Constructs a LoggerChannel object
   *
   * @param string $channel
   *   The channel name for this instance.
   */
  public function __construct($channel) {
    $this->channel = $channel;
  }

  /**
   * {@inheritdoc}
   */
  public function log($level, $message, array $context = array()) {
    $context = $context + array(
      'channel' => $this->channel,
    );
    foreach ($this->sortLoggers() as $logger) {
      $logger->log($level, $message, $context);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function setLoggers(array $loggers) {
    $this->loggers = $loggers;
  }

  /**
   * {@inheritdoc}
   */
  public function addLogger(LoggerInterface $logger, $priority = 0) {
    $this->loggers[$priority][] = $logger;
  }

  /**
   * Sorts loggers according to priority.
   *
   * @return \Psr\Log\LoggerInterface[]
   *   An array of sorted loggers by priority.
   */
  protected function sortLoggers() {
    $sorted = array();
    krsort($this->loggers);

    foreach ($this->loggers as $loggers) {
      $sorted = array_merge($sorted, $loggers);
    }
    return $sorted;
  }

  /**
   * {@inheritdoc}
   */
  public function emergency($message, array $context = array()) {
    return $this->log(LogLevel::EMERGENCY, $message, $context);
  }

  /**
   * {@inheritdoc}
   */
  public function alert($message, array $context = array()) {
    return $this->log(LogLevel::ALERT, $message, $context);
  }

  /**
   * {@inheritdoc}
   */
  public function critical($message, array $context = array()) {
    return $this->log(LogLevel::CRITICAL, $message, $context);
  }

  /**
   * {@inheritdoc}
   */
  public function error($message, array $context = array()) {
    return $this->log(LogLevel::ERROR, $message, $context);
  }

  /**
   * {@inheritdoc}
   */
  public function warning($message, array $context = array()) {
    return $this->log(LogLevel::WARNING, $message, $context);
  }

  /**
   * {@inheritdoc}
   */
  public function notice($message, array $context = array()) {
    return $this->log(LogLevel::NOTICE, $message, $context);
  }

  /**
   * {@inheritdoc}
   */
  public function info($message, array $context = array()) {
    return $this->log(LogLevel::INFO, $message, $context);
  }

  /**
   * {@inheritdoc}
   */
  public function debug($message, array $context = array()) {
    return $this->log(LogLevel::DEBUG, $message, $context);
  }

}
