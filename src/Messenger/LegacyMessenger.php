<?php

/**
 * @file
 * Contains \Drupal\service_container\LegacyMessenger.
 */

namespace Drupal\service_container\Messenger;

/**
 * Class that manage the messages in Drupal.
 *
 * @codeCoverageIgnore
 */
class LegacyMessenger implements MessengerInterface {
  /**
   * {@inheritdoc}
   */
  public function addMessage($message, $type = self::STATUS, $repeat = FALSE) {
    drupal_set_message($message, $type, $repeat);
  }

  /**
   * {@inheritdoc}
   */
  public function getMessages() {
    return drupal_get_messages(NULL, FALSE);
  }

  /**
   * {@inheritdoc}
   */
  public function getMessagesByType($type) {
    $messages = drupal_get_messages($type, FALSE);
    return isset($messages[$type]) ? $messages[$type] : $messages;
  }

  /**
   * {@inheritdoc}
   */
  public function deleteMessages() {
    return drupal_get_messages(NULL, TRUE);
  }

  /**
   * {@inheritdoc}
   */
  public function deleteMessagesByType($type) {
    return drupal_get_messages($type, TRUE);
  }
}
