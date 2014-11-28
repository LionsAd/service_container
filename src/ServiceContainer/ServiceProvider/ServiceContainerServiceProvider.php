<?php

/**
 * @file
 * Contains \Drupal\service_container\ServiceContainer\ServiceProvider\ServiceContainerServiceProvider
 */

namespace Drupal\service_container\ServiceContainer\ServiceProvider;

use Drupal\service_container\DependencyInjection\ServiceProviderInterface;
use Drupal\service_container\Plugin\Discovery\CToolsPluginDiscovery;

/**
 * Provides render cache service definitions.
 *
 * @codeCoverageIgnore
 *
 * @todo The alter part is unit testable.
 */
class ServiceContainerServiceProvider implements ServiceProviderInterface {

  /**
   * {@inheritdoc}
   */
  public function getContainerDefinition() {
    $parameters = array();
    $parameters['service_container.static_event_listeners'] = array();

    $services = array();
    $services['service_container'] = array(
      'class' => '\Drupal\service_container\DependencyInjection\Container',
    );

    $services['module_handler'] = array(
      'class' => '\Drupal\service_container\Extension\ModuleHandler',
      'arguments' => array(DRUPAL_ROOT),
    );

    $services['module_installer'] = array(
      'class' => '\Drupal\service_container\Extension\ModuleInstaller',
    );

    // @todo Make it  possible to register all ctools plugins here.

    return array(
      'parameters' => $parameters,
      'services' => $services,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function alterContainerDefinition(&$container_definition) {
    // Register ctools plugins as private services in the container.
    foreach ($container_definition['tags']['ctools.plugin'] as $service => $tags) {
      foreach ($tags as $tag) {
        $discovery = new CToolsPluginDiscovery($tag['owner'], $tag['type']);
        $definitions = $discovery->getDefinitions();
        foreach ($definitions as $key => $definition) {
          // Always pass the definition as the first argument.
          $definition += array(
            'arguments' => array(),
          );
          array_unshift($definition['arguments'], $definition);
          $container_definition['services'][$tag['prefix'] . $key] = $definition + array('public' => FALSE);
        }
      }
    }
  }
}
