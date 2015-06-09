<?php

/**
 * @file
 * Contains \Drupal\service_container\Variable.
 */

namespace Drupal\service_container;

use Drupal\service_container\Legacy\Drupal7;

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

  /**
   * The Drupal7 service.
   *
   * @var \Drupal\service_container\Legacy\Drupal7
   */
  protected $drupal7;

  /**
   * Constructs a new Variable instance.
   *
   * @param \Drupal\service_container\Legacy\Drupal7 $drupal7
   *   The Drupal7 service.
   */
  public function __construct(Drupal7 $drupal7) {
    $this->drupal7 = $drupal7;
  }

  public function get($name, $default = NULL) {
    return $this->drupal7->variable_get($name, $default);
  }

  public function set($name, $value) {
    $this->drupal7->variable_set($name, $value);
  }

}
