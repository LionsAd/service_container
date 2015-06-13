<?php

/**
 * @file
 * Contains \Drupal\service_container\Messenger\StaticMessenger.
 */

namespace Drupal\service_container\Messenger;

use Drupal\Component\Utility\SafeMarkup;
use Drupal\Core\PageCache\ResponsePolicy\KillSwitch;

/**
 * Provides a messenger that stores messages for this request only.
 */
class StaticMessenger implements MessengerInterface {

  /**
   * The messages that have been set.
   *
   * @var array[]
   *   Keys are either self::STATUS, self::WARNING, or self::ERROR. Values are
   *   arrays of arrays with the following keys:
   *   - message (string): the message.
   *   - safe (bool): whether the message is marked as safe markup.
   */
  protected $messages = array();

  /**
   * The page caching kill switch.
   *
   * @var \Drupal\Core\PageCache\ResponsePolicy\KillSwitch
   */
  protected $pageCacheKillSwitch;

  /**
   * Constructs a new instance.
   *
   * @param \Drupal\Core\PageCache\ResponsePolicy\KillSwitch $page_cache_kill_switch
   *   The page caching kill switch.
   */
  public function __construct(KillSwitch $page_cache_kill_switch) {
    $this->pageCacheKillSwitch = $page_cache_kill_switch;
  }

  /**
   * {@inheritdoc}
   */
  public function addMessage($message, $type = self::STATUS, $repeat = FALSE) {
    if ($repeat || !array_key_exists($type, $this->messages) || !in_array($message, $this->messages[$type])) {
      $this->messages[$type][] = array(
        'safe' => SafeMarkup::isSafe($message),
        'message' => $message,
      );
      $this->pageCacheKillSwitch->trigger();
    }

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getMessages() {
    $messages = isset($this->messages) ? $this->messages : array();
    foreach ($messages as $type => $messages_by_type) {
      $messages[$type] = $this->processMessages($messages_by_type);
    }

    return $messages;
  }

  /**
   * {@inheritdoc}
   */
  public function getMessagesByType($type) {
    $messages = isset($this->messages) && isset($this->messages[$type]) ? $this->messages[$type] : array();

    return $this->processMessages($messages);
  }

  /**
   * {@inheritdoc}
   */
  public function deleteMessages() {
    unset($this->messages);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function deleteMessagesByType($type) {
    unset($this->messages[$type]);

    return $this;
  }

  /**
   * Processes safe markup.
   *
   * @param array[]
   *   An array of arrays with the following keys:
   *   - message: the message string.
   *   - safe: a boolean indicating whether the message contains safe markup.
   *
   * @return string[]
   *   The messages.
   */
  protected function processMessages(array $messages) {
    $processed_messages = array();
    foreach ($messages as $message_data) {
      $processed_messages[] = $message_data['safe'] ? SafeMarkup::set($message_data['message']) : $message_data['message'];
    }

    return $processed_messages;
  }

}
