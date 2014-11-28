<?php

/**
 * @file
 * Contains \Drupal\service_container\Variable.
 */

namespace Drupal\service_container;

/**
 * Provides a injectable version of variable_set() / variable_get().
 *
 * Note: The main reason why this is not mapped to config is that config
 * has a different thought process: configs + variables inside each config file.
 *
 * In order to port code from d7 to d8, you would need additional effort here
 * anyway.
 *
 * @codeCoverageIgnore
 */
class Variable {

  public function get($name, $default = NULL) {
    return variable_get($name, $default);
  }

  public function set($name, $value) {
    variable_set($name, $value);
  }

}
