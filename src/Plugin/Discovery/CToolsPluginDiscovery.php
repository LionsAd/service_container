<?php

/**
 * @file
 * Contains \Drupal\service_container\Plugin\Discovery\CToolsPluginDiscovery
 */

namespace Drupal\service_container\Plugin\Discovery;

use Drupal\Component\Plugin\Discovery\DiscoveryInterface;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;

/**
 * A discovery mechanism that uses ctools plugins for Drupal 7 compatibility.
 *
 * This class cannot be tested as it relies on the existance of procedural code.
 * @codeCoverageIgnore
 */
class CToolsPluginDiscovery implements DiscoveryInterface {

  /**
   * The owning module.
   *
   * @var string
   */
  protected $pluginOwner;

  /**
   * The plugin type.
   *
   * @var string
   */
  protected $pluginType;

  /**
   * Constructs a CToolsPluginDiscovery object.
   *
   * @param string $plugin_type
   *   The ctools plugin type to load.
   */
  public function __construct($plugin_owner, $plugin_type) {
    $this->pluginOwner = $plugin_owner;
    $this->pluginType = $plugin_type;
  }

  /**
   * {@inheritdoc}
   */
  public function getDefinitions() {
    ctools_include('plugins');
    return ctools_get_plugins($this->pluginOwner, $this->pluginType);
  }

  /**
   * {@inheritdoc}
   */
  public function getDefinition($plugin_id, $exception_on_invalid = TRUE) {
    ctools_include('plugins');
    $definition = ctools_get_plugins($this->pluginOwner, $this->pluginType, $plugin_id);

    if (!$definition && $exception_on_invalid) {
      throw new PluginNotFoundException($plugin_id, sprintf('The "%s" plugin does not exist.', $plugin_id));
    }

    return $definition;
  }

  /**
   * {@inheritdoc}
   */
  public function hasDefinition($plugin_id) {
    return (bool) $this->getDefinition($plugin_id, FALSE);
  }

}
