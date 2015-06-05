<?php

/**
 * @file
 * Contains \Drupal\service_container\LegacyMessenger.
 */

namespace Drupal\service_container\Messenger;

use Drupal\service_container\Legacy\Drupal7;

/**
 * Class that manage the messages in Drupal.
 */
class LegacyMessenger implements MessengerInterface {
  protected $drupal7;

  public function __construct(Drupal7 $drupal7_service) {
    $this->drupal7 = $drupal7_service;
  }

  /**
   * {@inheritdoc}
   */
  public function addMessage($message, $type = self::STATUS, $repeat = FALSE) {
    $this->drupal7->drupal_set_message($message, $type, $repeat);
  }

  /**
   * {@inheritdoc}
   */
  public function getMessages() {
    return $this->drupal7->drupal_get_messages(NULL, FALSE);
  }

  /**
   * {@inheritdoc}
   */
  public function getMessagesByType($type) {
    $messages = $this->drupal7->drupal_get_messages($type, FALSE);
    return isset($messages[$type]) ? $messages[$type] : array();
  }

  /**
   * {@inheritdoc}
   */
  public function deleteMessages() {
    return $this->drupal7->drupal_get_messages(NULL, TRUE);
  }

  /**
   * {@inheritdoc}
   */
  public function deleteMessagesByType($type) {
    return $this->drupal7->drupal_get_messages($type, TRUE);
  }
}
