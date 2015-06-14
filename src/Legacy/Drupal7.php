<?php

/**
 * @file
 * Contains \Drupal\service_container\Legacy\Drupal7.
 */

namespace Drupal\service_container\Legacy;

/**
 * Defines the Drupal 7 legacy service.
 *
 * @method void drupal_set_message(string $message = NULL, string $type = 'status', bool $repeat = TRUE)
 * @method mixed drupal_get_message(string $message = NULL, string $type = 'status', bool $repeat = TRUE)
 *
 * @codeCoverageIgnore
 */
class Drupal7 {

  public function __call($method, $args) {
    return call_user_func_array($method, $args);
  }

}
