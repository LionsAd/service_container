<?php

/**
 * @file
 * Contains \Drupal\Generic\Drupal7.
 */

namespace Drupal\Generic;

class Drupal7 {
  public function __call($method, $args) {
    if (function_exists('drupal_' . $method)) {
      return call_user_func_array('drupal_' . $method, $args);
    }
    return call_user_func_array($method, $args);
  }
}
