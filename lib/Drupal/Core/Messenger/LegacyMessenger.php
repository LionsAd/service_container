<?php

/**
 * @file
 * Contains \Drupal\service_container\LegacyMessenger.
 */

namespace Drupal\service_container\Messenger;
use Drupal\Core\Messenger\MessengerInterface;

/**
 * TODO
 *
 * @codeCoverageIgnore
 */
class LegacyMessenger implements MessengerInterface {
  public function addMessage($message, $type = self::STATUS, $repeat = FALSE) {
    drupal_set_message($message, $type, $repeat);
  }

  public function getMessages() {
    return drupal_get_messages();
  }

  public function getMessagesByType($type) {
    $messages = drupal_get_messages();
    return isset($messages[$type]) ? $messages[$type] : $messages;
  }

  public function deleteMessages() {
    // TODO
  }

  public function deleteMessagesByType($type) {
    // TODO
  }
}
