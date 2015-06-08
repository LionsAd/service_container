<?php

/**
 * @file
 * Contains \Drupal\service_container\Logger\WatchdogLogger.
 */

namespace Drupal\service_container\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Drupal\service_container\Legacy\Drupal7;

/**
 * Implements the PSR-3 logger with watchdog.
 *
 * @codeCoverageIgnore
 */
class WatchdogLogger extends LoggerBase implements LoggerInterface {

  /**
   * The Drupal7 service.
   *
   * @var \Drupal\service_container\Legacy\Drupal7
   */
  protected $drupal7;

  /**
   * {@inheritdoc}
   */
  function __construct(Drupal7 $drupal7) {
    $this->drupal7 = $drupal7;
  }

  /**
   * {@inheritdoc}
   * @codeCoverageIgnore
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

    $this->drupal7->watchdog($type, $message, $context, $watchdog_level);
  }

}
