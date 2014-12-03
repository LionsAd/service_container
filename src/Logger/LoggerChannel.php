<?php

/**
 * @file
 * Contains \Drupal\service_container\Logger\LoggerChannel.
 */

namespace Drupal\service_container\Logger;

use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Defines a logger channel that most implementations will use.
 */
class LoggerChannel extends \Drupal\Core\Logger\LoggerChannel implements LoggerChannelInterface {

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

}
