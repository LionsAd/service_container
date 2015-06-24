<?php

/**
 * @file
 * Contains \Drupal\service_container\Plugin\ContainerAwarePluginManager
 */

namespace Drupal\service_container\Plugin;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\service_container\DependencyInjection\ContainerAware;

/**
 * Base class for plugin managers.
 */
class ContainerAwarePluginManager extends ContainerAware implements PluginManagerInterface {

  /**
   * Constructs a ContainerAwarePluginManager object.
   *
   * @param string $service_prefix
   *   The service prefix used to get the plugin instances from the container.
   */
  public function __construct($service_prefix) {
    $this->servicePrefix = $service_prefix;
  }

  /**
   * {@inheritdoc}
   */
  public function getDefinition($plugin_id, $exception_on_invalid = TRUE) {
    return $this->container->getDefinition($this->servicePrefix . $plugin_id, $exception_on_invalid);
  }

  /**
   * {@inheritdoc}
   */
  public function getDefinitions() {
    $definitions =  $this->container->getDefinitions();
    $prefix = $this->servicePrefix;

    // Note: ARRAY_FILTER_USE_BOTH is not supported in HHVM and PHP 5.4.
    $keys = array_filter(array_keys($definitions),
      function($key) use ($prefix) {
        return strpos($key, $prefix) === 0;
      });
    return array_intersect_key($definitions, array_flip($keys));
  }

  /**
   * {@inheritdoc}
   */
  public function hasDefinition($plugin_id) {
    return $this->container->hasDefinition($this->servicePrefix . $plugin_id);
  }

  /**
   * {@inheritdoc}
   */
  public function createInstance($plugin_id, array $configuration = array()) {
    $plugin_definition_copy = $plugin_definition = $this->getDefinition($plugin_id);
    $plugin_class = static::getPluginClass($plugin_id, $plugin_definition);

    // If the plugin provides a factory method, pass the container to it.
    if (is_subclass_of($plugin_class, 'Drupal\Core\Plugin\ContainerFactoryPluginInterface')) {
      return $plugin_class::create($this->container, $configuration, $plugin_id, $plugin_definition);
    }

    $plugin_definition += array(
      'arguments' => array(),
    );

    array_unshift($plugin_definition['arguments'], $plugin_definition_copy);
    array_unshift($plugin_definition['arguments'], $plugin_id);
    array_unshift($plugin_definition['arguments'], $configuration);

    // Otherwise, create the plugin directly.
    return $this->container->createInstance($this->servicePrefix . $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public function getInstance(array $options) {
    // 90% of core does not use the generic $mapper functionality, so use a
    // sane default function.
    if (isset($options['id'])) {
      return $this->createInstance($options['id']);
    }
    return FALSE;
  }

  /**
   * Finds the class relevant for a given plugin.
   *
   * @param string $plugin_id
   *   The id of a plugin.
   * @param mixed $plugin_definition
   *   The plugin definition associated with the plugin ID.
   * @param string $required_interface
   *   (optional) THe required plugin interface.
   *
   * @return string
   *   The appropriate class name.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   *   Thrown when there is no class specified, the class doesn't exist, or
   *   the class does not implement the specified required interface.
   *
   */
  public static function getPluginClass($plugin_id, $plugin_definition = NULL, $required_interface = NULL) {
    if (empty($plugin_definition['class'])) {
      throw new PluginException(sprintf('The plugin (%s) did not specify an instance class.', $plugin_id));
    }

    $class = $plugin_definition['class'];

    if (!class_exists($class)) {
      throw new PluginException(sprintf('Plugin (%s) instance class "%s" does not exist.', $plugin_id, $class));
    }

    if ($required_interface && !is_subclass_of($plugin_definition['class'], $required_interface)) {
      throw new PluginException(sprintf('Plugin "%s" (%s) in %s should implement interface %s.', $plugin_id, $plugin_definition['class'], $plugin_definition['provider'], $required_interface));
    }

    return $class;
  }
}
