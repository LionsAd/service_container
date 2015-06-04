<?php

/**
 * @file
 * Contains \Drupal\service_container\Generic\Drupal7.
 */

namespace Drupal\service_container\Generic;

class Drupal7 {
  public function __call($method, $args) {
    return call_user_func_array($method, $args);
  }
}
