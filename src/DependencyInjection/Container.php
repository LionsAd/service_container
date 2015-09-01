<?php
/**
 * @file
 * Contains \Drupal\service_container\DependencyInjection\Container
 */

namespace Drupal\service_container\DependencyInjection;

use Drupal\Component\DependencyInjection\PhpArrayContainer;

/**
 * Container is a DI container that provides services to users of the class.
 *
 * @ingroup dic
 */
class Container extends PhpArrayContainer {
  /**
   * Camelizes a string.
   *
   * @param $name
   *   The string to camelize.
   *
   * @return string
   *   The camelized string.
   *
   */
  public static function camelize($name) {
    return strtr(ucwords(strtr($name, array('_' => ' ', '\\' => '_ '))), array(' ' => ''));
  }

  /**
   * Un-camelizes a string.
   *
   * @param $name
   *   The string to underscore.
   *
   * @return string
   *   The underscored string.
   *
   */
  public static function underscore($name) {
    return strtolower(preg_replace(array('/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'), array('\\1_\\2', '\\1_\\2'), $name));
  }

  /**
   * {@inheritdoc}
   */
  public function getDefinitions() {
    return $this->serviceDefinitions;
  }

}
