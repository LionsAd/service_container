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
    return variable_get('service_container_test_services', array());
  }

  /**
   * {@inheritdoc}
   */
  public function alterContainerDefinition(&$container_definition) {
    $container_definition = NestedArray::mergeDeep($container_definition, variable_get('service_container_test_parameters', array()));
    return;
    $parameter[KeyValueExpirableFactory::DEFAULT_SETTING] = 'keyvalue.expirable.database';
    $container_definition['parameters']['factory.keyvalue.expirable'] = $parameter;
  }

}
