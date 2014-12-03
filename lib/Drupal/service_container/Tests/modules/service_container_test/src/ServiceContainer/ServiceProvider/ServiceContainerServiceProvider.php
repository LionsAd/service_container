<?php

/**
 * @file
 * Contains \Drupal\service_container_test\ServiceContainer\ServiceProvider\ServiceContainerServiceProvider.
 */

namespace Drupal\service_container_test\ServiceContainer\ServiceProvider;

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
    return variable_get('service_container_test_definition', array());
  }

  /**
   * {@inheritdoc}
   */
  public function alterContainerDefinition(&$container_definition) {
    return;
  }

}
