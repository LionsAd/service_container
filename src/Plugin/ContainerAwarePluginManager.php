<?php

/**
 * @file
 * Contains \Drupal\service_container\Plugin\ContainerAwarePluginManager
 */

namespace Drupal\service_container\Plugin;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
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
    // @todo: Use ->expandArguments() when get() disallows getting private
    //        services.
    $plugin = clone $this->container->get($this->servicePrefix . $plugin_id);
    return $plugin;
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
  }
}
