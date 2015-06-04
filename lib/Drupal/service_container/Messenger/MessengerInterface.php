<?php

/**
 * @file
 * Contains \Drupal\Core\Messenger\MessengerInterface.
 */

namespace Drupal\service_container\Messenger;

interface MessengerInterface {

  /**
   * A status message.
   */
  const STATUS = 'status';

  /**
   * A warning.
   */
  const WARNING = 'warning';

  /**
   * An error.
   */
  const ERROR = 'error';

  /**
   * Adds a new message to the queue.
   *
   * @param string $message
   *   (optional) The translated message to be displayed to the user. For
   *   consistency with other messages, it should begin with a capital letter
   *   and end with a period.
   * @param string $type
   *   (optional) The message's type. Either self::STATUS, self::WARNING, or
   *   self::ERROR.
   * @param bool $repeat
   *   (optional) If this is FALSE and the message is already set, then the
   *   message won't be repeated. Defaults to FALSE.
   *
   * @return $this
   */
  public function addMessage($message, $type = self::STATUS, $repeat = FALSE);

  /**
   * Gets all messages.
   *
   * @return array[]
   *   Keys are message types and values are indexed arrays of messages. Message
   *   types are either self::STATUS, self::WARNING, or self::ERROR.
   */
  public function getMessages();

  /**
   * Gets all messages of a certain type.
   *
   * @param string $type
   *   The messages' type. Either self::STATUS, self::WARNING, or self::ERROR.
   *
   * @return string[]
   */
  public function getMessagesByType($type);

  /**
   * Deletes all messages.
   *
   * @return $this
   */
  public function deleteMessages();

  /**
   * Deletes all messages of a certain type.
   *
   * @param string $type
   *   The messages' type. Either self::STATUS, self::WARNING, or self::ERROR.
   *
   * @return $this
   */
  public function deleteMessagesByType($type);

}
