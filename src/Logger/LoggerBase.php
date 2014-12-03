<?php

/**
 * @file
 * Contains \Drupal\service_container\Logger\LoggerBase.
 */

namespace Drupal\service_container\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Provides a common base class for loggers to reduce boilerplate code.
 */
abstract class LoggerBase implements LoggerInterface {

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

