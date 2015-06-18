<?php
/**
 * @file
 * Contains \Drupal\service_container\Plugin\DefaultPluginManager
 */

namespace Drupal\service_container\Plugin;

use Drupal\Component\Plugin\Discovery\DiscoveryInterface;
use Drupal\Component\Plugin\Factory\DefaultFactory;

/**
 * Defines a plugin manager used for discovering generic plugins.
 */
class DefaultPluginManager extends PluginManagerBase {

  /**
   * Constructs a DefaultPluginManager object.
   *
   * @param DiscoveryInterface $discovery
   *   The discovery object used to find plugins.
   */
  public function __construct(DiscoveryInterface $discovery) {
    $this->discovery = $discovery;
    // Use a generic factory.
    $this->factory = new DefaultFactory($this->discovery);
  }
}
