<?php

/**
 * @file
 * Contains \Drupal\service_container_symfony\ServiceContainer\ServiceProvider\ServiceContainerSymfonyServiceProvider
 */

namespace Drupal\service_container_doctrine\ServiceContainer\ServiceProvider;

use Drupal\service_container\DependencyInjection\Container;
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

    if (!empty($container_definition['parameters']['annotated_plugins_auto_discovery']) && $this->moduleExists('ctools')) {
      $ctools_types = $this->cToolsGetTypes();
      $filtered_types = array_intersect_key($ctools_types, $container_definition['parameters']['annotated_plugins_auto_discovery']);
      $this->registerAnnotatedPluginTypes($container_definition, $filtered_types);
    }

    // Set empty value when its not set.
    if (empty($container_definition['tags']['plugin_manager'])) {
      $container_definition['tags']['plugin_manager'] = array();
    }

    // Process plugin managers of different types.
    $plugin_manager_types = $container_definition['parameters']['service_container.plugin_manager_types'];
    $all_plugin_managers = $container_definition['parameters']['service_container.plugin_managers'];

    foreach ($all_plugin_managers as $plugin_manager_type => $plugin_managers) {
      if (empty($plugin_manager_types[$plugin_manager_type])) {
        continue;
      }
      $discovery_class = $plugin_manager_types[$plugin_manager_type];
      $this->processPluginManagers($container_definition, $discovery_class, $plugin_managers);
    }

    // Register plugin manager plugins as private services in the container.
    foreach ($container_definition['tags']['plugin_manager'] as $service => $tags) {
      foreach ($tags as $tag) {
        $discovery_class = $tag['discovery_class'];
        $discovery = new $discovery_class($tag['plugin_manager_definition']);
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

  /**
   * Automatically register all ctools plugins of the given types.
   *
   * @param array $container_definition
   *   The container definition to process.
   * @param array $ctools_types
   *   Array of plugin types, indexed by module name.
   */
  public function registerAnnotatedPluginTypes(&$container_definition, $ctools_types) {
    foreach($ctools_types as $module_name => $plugins) {
      foreach($plugins as $plugin_type => $plugin_data) {

        if (isset($container_definition['parameters']['service_container.plugin_managers']['annotated'][$module_name . '.' . $plugin_type])) {
          continue;
        }

        // Register service with original string.
        $name = $module_name . '.' . $plugin_type;
        $container_definition['services'][$name] = array();

        // Check candidates for needed aliases.
        $candidates = array();
        $candidates[$module_name . '.' . Container::underscore($plugin_type)] = TRUE;
        $candidates[$name] = FALSE;

        foreach ($candidates as $candidate => $value) {
          if ($value) {
            $container_definition['services'][$candidate] = array(
              'alias' => $name,
            );
          }
        }

        $container_definition['parameters']['service_container.plugin_managers']['annotated'][$module_name . '.' . $plugin_type] = array(
          'owner' => $module_name,
          'type' => $plugin_type,
        );
      }
    }
  }

}
