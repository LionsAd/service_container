<?php

/**
 * @file
 * Contains \Drupal\service_container\Logger\WatchdogLogger.
 */

namespace Drupal\service_container\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Implements the PSR-3 logger with watchdog.
 *
 * @codeCoverageIgnore
 */
class WatchdogLogger implements LoggerInterface {

  /**
   * Constructs a new WatchdogLogger.
   *
   * @param string $type
   *   The watchdog category.
   */
  public function __construct($type) {
    $this->type = $type;
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

  /**
   * {@inheritdoc}
   */
  public function log($level, $message, array $context = array()) {
    $map = array(
      LogLevel::EMERGENCY => WATCHDOG_EMERGENCY,
      LogLevel::DEBUG => WATCHDOG_DEBUG,
      LogLevel::INFO => WATCHDOG_INFO,
      LogLevel::ALERT => WATCHDOG_ALERT,
      LogLevel::CRITICAL => WATCHDOG_CRITICAL,
      LogLevel::ERROR => WATCHDOG_ERROR,
      LogLevel::NOTICE => WATCHDOG_NOTICE,
    );

    $watchdog_level = $map[$level];

    // Map the logger channel to the watchdog type.
    $type = isset($context['channel']) ? $context['channel'] : 'default';
    unset($context['channel']);

    watchdog($type, $message, $context, $watchdog_level);
  }

}
