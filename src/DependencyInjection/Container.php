<?php
/**
 * @file
 * Contains \Drupal\service_container\DependencyInjection\Container
 */

namespace Drupal\service_container\DependencyInjection;

use Drupal\Component\DependencyInjection\PhpArrayContainer;
use ReflectionClass;
use RuntimeException;
use Symfony\Component\DependencyInjection\ScopeInterface;

/**
 * Container is a DI container that provides services to users of the class.
 *
 * @ingroup dic
 */
class Container extends PhpArrayContainer implements ContainerInterface {

  /**
   * {@inheritdoc}
   */
  public function createInstance($plugin_id, $service_definition) {
    $temporary_name = 'plugin_' . $plugin_id;
    $this->serviceDefinitions[$temporary_name] = $service_definition;

    $plugin = $this->get($temporary_name);
    unset($this->serviceDefinitions[$temporary_name]);
    unset($this->services[$temporary_name]);

    return $plugin;
  }

  /**
   * {@inheritdoc}
   */
  public function getDefinition($plugin_id, $exception_on_invalid = TRUE) {
    $definition = isset($this->serviceDefinitions[$plugin_id]) ? $this->serviceDefinitions[$plugin_id] : NULL;

    if (!$definition && $exception_on_invalid) {
      throw new RuntimeException(sprintf('The "%s" service definition does not exist.', $plugin_id));
    }

    return $definition;
  }

  /**
   * {@inheritdoc}
   */
  public function getDefinitions() {
    return $this->serviceDefinitions;
  }

  /**
   * {@inheritdoc}
   */
  public function hasDefinition($plugin_id) {
    return isset($this->serviceDefinitions[$plugin_id]);
  }

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
}
