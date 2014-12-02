<?php

/**
 * @file
 * Contains \Drupal\service_container\Logger\LoggerChannelInterface.
 */

namespace Drupal\service_container\Logger;

use Psr\Log\LoggerInterface;

/**
 * Logger channel interface.
 */
interface LoggerChannelInterface extends LoggerInterface {

  /**
   * Sets the loggers for this channel.
   *
   * @param array $loggers
   *   An array of arrays of \Psr\Log\LoggerInterface keyed by priority.
   */
  public function setLoggers(array $loggers);

  /**
   * Adds a logger.
   *
   * @param \Psr\Log\LoggerInterface $logger
   *   The PSR-3 logger to add.
   * @param int $priority
   *   The priority of the logger being added.
   */
  public function addLogger(LoggerInterface $logger, $priority = 0);

}

