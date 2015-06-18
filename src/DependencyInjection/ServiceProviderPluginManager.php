<?php
/**
 * @file
 * Contains \Drupal\service_container\DependencyInjection\ServiceProviderPluginManager
 */

namespace Drupal\service_container\DependencyInjection;

use Drupal\service_container\Plugin\DefaultPluginManager;
use Drupal\service_container\Plugin\Discovery\CToolsPluginDiscovery;

/**
 * Defines a plugin manager used for discovering container service definitions.
 */
class ServiceProviderPluginManager extends DefaultPluginManager {

  /**
   * Constructs a ServiceProviderPluginManager object.
   *
   * This uses ctools for discovery of service_container ServiceProvider objects.
   *
   * @codeCoverageIgnore
   */
  public function __construct() {
   $discovery = new CToolsPluginDiscovery(array(
     'owner' => 'service_container',
     'type' => 'ServiceProvider',
   ));
   parent::__construct($discovery);
  }
}
