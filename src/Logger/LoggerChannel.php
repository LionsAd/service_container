<?php

/**
 * @file
 * Contains \Drupal\service_container\Logger\LoggerChannel.
 */

namespace Drupal\service_container\Logger;

use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\Session\AccountInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Defines a logger channel that most implementations will use.
 */
class LoggerChannel extends LoggerBase implements LoggerChannelInterface {

  /**
   * The name of the channel of this logger instance.
   *
   * @var string
   */
  protected $channel;

  /**
   * An array of arrays of \Psr\Log\LoggerInterface keyed by priority.
   *
   * @var array
   */
  protected $loggers = array();

  /**
   * The request stack object.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * The current user object.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * {@inheritdoc}
   */
  public function log($level, $message, array $context = array()) {
    $context += array(
      'channel' => $this->channel,
    ); // @codeCoverageIgnore
    foreach ($this->sortLoggers() as $logger) {
      $logger->log($level, $message, $context);
    }
  }

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
   * @return array
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
  public function setRequestStack(RequestStack $requestStack = NULL) {
    $this->requestStack = $requestStack;
  }

  /**
   * {@inheritdoc}
   */
  public function setCurrentUser(AccountInterface $current_user = NULL) {
    $this->currentUser = $current_user;
  }

}
