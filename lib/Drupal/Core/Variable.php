<?php

/**
 * @file
 * Contains \Drupal\Core\Variable.
 */

namespace Drupal\Core;

/**
 * Implements a injectable version of variable_set() / variable_get().
 */
class Variable {

  public function get($name, $default = NULL) {
    return variable_get($name, $default);
  }

  public function set($name, $value) {
    variable_set($name, $value);
  }

}
