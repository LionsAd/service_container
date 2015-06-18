<?php

/**
 * @file
 * Contains \Drupal\service_container_symfony\ServiceContainer\ServiceProvider\ServiceContainerSymfonyServiceProvider
 */

namespace Drupal\service_container_doctrine\ServiceContainer\ServiceProvider;

use Drupal\service_container\ServiceContainer\ServiceProvider\ServiceContainerServiceProvider;

/**
 * Provides render cache service definitions.
 *
 * @codeCoverageIgnore
 *
 */
class ServiceContainerDoctrineServiceProvider extends ServiceContainerServiceProvider {

  /**
   * {@inheritdoc}
   */
  public function getContainerDefinition() {
    $services = array();
    $parameters['service_container.plugin_manager_types'] = array(
      'annotated' => '\Drupal\service_container_doctrine\Plugin\Discovery\AnnotatedClassDiscovery',
    );

    return array(
      'parameters' => $parameters,
      'services' => $services,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function alterContainerDefinition(&$container_definition) {

    if ($container_definition['parameters']['annotated_plugins_auto_discovery'] == TRUE) {
      $module_name = $container_definition['parameters']['annotated_plugins_auto_discovery'];
      $discovery_class = $container_definition['parameters']['service_container.plugin_manager_types']['annotated'];

      $container_definition['services'][$module_name] = array();
      $container_definition['parameters']['service_container.plugin_managers']['annotated'][$module_name] = array(
        'owner' => $module_name,
      );

      // Set empty value when its not set.
      if (empty($container_definition['tags']['plugin_manager'])) {
        $container_definition['tags']['plugin_manager'] = array();
      }

      $this->processPluginManagers($container_definition, $discovery_class, $container_definition['parameters']['service_container.plugin_managers']['annotated']);

      foreach ($container_definition['tags']['plugin_manager'][$module_name] as $tag) {
        $discovery_class = $tag['discovery_class'];

        $discovery = new $discovery_class($container_definition['parameters']['service_container.plugin_managers']['annotated'][$module_name]);
        $definitions = $discovery->getDefinitions();
        foreach ($definitions as $key => $definition) {
          // Always pass the definition as the first argument.
          $definition += array(
            'arguments' => array(),
          );
          // array_unshift() internally uses a reference, therefore creates an
          // endless recursion. Use a copy to prevent that.
          $definition_copy = $definition;
          array_unshift($definition['arguments'], $definition_copy);
          $container_definition['services'][$tag['prefix'] . $key] = $definition + array('public' => FALSE);
        }
      }
    }
  }

}
