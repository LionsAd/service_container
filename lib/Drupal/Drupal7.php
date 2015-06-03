<?php

/**
 * @file
 * Contains \Drupal\Drupal7.
 */

namespace Drupal;

class Drupal7 {
  public function __call($method, $args) {
    return call_user_func_array($method, $args);
  }
}
