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
    return drupal_get_messages();
  }

  /**
   * {@inheritdoc}
   */
  public function getMessagesByType($type) {
    $messages = $this->getMessages();
    return isset($messages[$type]) ? $messages[$type] : $messages;
  }

  /**
   * {@inheritdoc}
   */
  public function deleteMessages() {
    throw new \BadMethodCallException('LegacyMessenger::deleteMessages is not implemented.');
  }

  /**
   * {@inheritdoc}
   */
  public function deleteMessagesByType($type) {
    throw new \BadMethodCallException('LegacyMessenger::deleteMessagesByType is not implemented.');
  }
}
