<?php

/**
 * @file
 * Contains \Drupal\Core\Messenger\SessionMessenger.
 */

namespace Drupal\Core\Messenger;

use Drupal\Component\Utility\SafeMarkup;
use Drupal\Core\PageCache\ResponsePolicy\KillSwitch;
use Drupal\Core\Session\SessionManagerInterface;

/**
 * Provides a session-based messenger.
 */
class SessionMessenger implements MessengerInterface {

  /**
     * The page caching kill switch.
     *
     * @var \Drupal\Core\PageCache\ResponsePolicy\KillSwitch
     */
  protected $pageCacheKillSwitch;

  /**
     * The session manager.
     *
     * @var \Drupal\Core\Session\SessionManagerInterface
     */
  protected $sessionManager;

  /**
     * Constructs a new instance.
     *
     * @param \Drupal\Core\Session\SessionManagerInterface
     * @param \Drupal\Core\PageCache\ResponsePolicy\KillSwitch $page_cache_kill_switch
     *   The page caching kill switch.
     */
  public function __construct(SessionManagerInterface $session_manager, KillSwitch $page_cache_kill_switch) {
        $this->pageCacheKillSwitch = $page_cache_kill_switch;
        $this->sessionManager = $session_manager;
      }

  /**
     * {@inheritdoc}
     */
  public function addMessage($message, $type = self::STATUS, $repeat = FALSE) {
        $this->sessionManager->start();
        if ($repeat || !array_key_exists('messages', $_SESSION) || !array_key_exists($type, $_SESSION['messages']) || !in_array($message, $_SESSION['messages'][$type])) {
            $_SESSION['messages'][$type][] = array(
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
        $this->sessionManager->start();
        $messages = isset($_SESSION['messages']) ? $_SESSION['messages'] : array();
        foreach ($messages as $type => $messages_by_type) {
            $messages[$type] = $this->processMessages($messages_by_type);
          }

    return $messages;
  }

  /**
     * {@inheritdoc}
     */
  public function getMessagesByType($type) {
        $this->sessionManager->start();
        $messages = isset($_SESSION['messages']) && isset($_SESSION['messages'][$type]) ? $_SESSION['messages'][$type] : array();

        return $this->processMessages($messages);
  }

  /**
     * {@inheritdoc}
     */
  public function deleteMessages() {
        $this->sessionManager->start();
        unset($_SESSION['messages']);

    return $this;
  }

  /**
     * {@inheritdoc}
     */
  public function deleteMessagesByType($type) {
        $this->sessionManager->start();
        unset($_SESSION['messages'][$type]);

    return $this;
  }

  /**
     * Proccesses safe markup.
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
