<?php

/**
 * @file
 * Contains \Drupal\service_container_symfony\ServiceContainer\ServiceProvider\ServiceContainerSymfonyServiceProvider
 */

namespace Drupal\service_container_doctrine\ServiceContainer\ServiceProvider;

use Drupal\Component\FileCache\FileCacheFactory;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\YamlFileLoader;
use Drupal\service_container_symfony\DependencyInjection\Dumper\PhpArrayDumper;
use Drupal\service_container\DependencyInjection\ServiceProviderInterface;

/**
 * Provides render cache service definitions.
 *
 * @codeCoverageIgnore
 *
 */
class ServiceContainerDoctrineServiceProvider implements ServiceProviderInterface {

  /**
   * {@inheritdoc}
   */
  public function getContainerDefinition() {
    $services = array();
    $parameters['service_container.plugin_manager_types'] = array(
      'annotated' => '\Drupal\service_container\Plugin\Discovery\AnnotatedClassDiscovery',
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
      $filtered_types = array_intersect_key($ctools_types, $container_definition['parameters']['ctools_plugins_auto_discovery']);
      $this->registerAnnotatedPluginTypes($container_definition, $filtered_types);
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
        if (isset($container_definition['parameters']['service_container.plugin_managers']['ctools'][$module_name . '.' . $plugin_type])) {
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

        $container_definition['parameters']['service_container.plugin_managers']['ctools'][$module_name . '.' . $plugin_type] = array(
          'owner' => $module_name,
          'type' => $plugin_type,
        );
      }
    }
  }

}
