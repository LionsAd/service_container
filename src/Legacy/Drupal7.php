<?php

/**
 * @file
 * Contains \Drupal\service_container\Legacy\Drupal7.
 */

namespace Drupal\service_container\Legacy;

class Drupal7 {
  public function __call($method, $args) {
    return call_user_func_array($method, $args);
  }
}
