<?php

/**
 * @file
 * Contains \Drupal\service_container_test_ctools\ServiceContainer\ServiceProvider\ServiceContainerServiceProvider.
 */

namespace Drupal\service_container_test_ctools\ServiceContainer\ServiceProvider;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\KeyValueStore\KeyValueExpirableFactory;
use Drupal\service_container\DependencyInjection\ServiceProviderInterface;

/**
 * Overrides some specific services/parameters for tests purposes.
 */
class ServiceContainerServiceProvider implements ServiceProviderInterface {

  /**
   * {@inheritdoc}
   */
  public function getContainerDefinition() {
    $services = array();
    $parameters['ctools_plugins_auto_discovery.service_container_test_ctools'] = array('service_container_test_ctools');

    return array(
      'parameters' => $parameters,
      'services' => $services,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function alterContainerDefinition(&$container_definition) {
    return;
  }

}
