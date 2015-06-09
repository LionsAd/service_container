<?php

/**
 * @file
 * Contains \Drupal\service_container_symfony\ServiceContainer\ServiceProvider\ServiceContainerSymfonyServiceProvider
 */

namespace Drupal\service_container_symfony\ServiceContainer\ServiceProvider;

use Drupal\service_container\DependencyInjection\ServiceProviderInterface;
use Drupal\service_container\DependencyInjection\Container;

/**
 * Provides render cache service definitions.
 *
 * @codeCoverageIgnore
 *
 */
class ServiceContainerSymfonyServiceProvider implements ServiceProviderInterface {

  /**
   * {@inheritdoc}
   */
  public function getContainerDefinition() {
    $parameters = array();
    $services = array();

    return array(
      'parameters' => $parameters,
      'services' => $services,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function alterContainerDefinition(&$container_definition) {}

}
