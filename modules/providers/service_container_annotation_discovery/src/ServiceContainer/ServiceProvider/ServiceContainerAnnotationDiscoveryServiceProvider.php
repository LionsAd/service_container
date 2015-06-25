<?php

/**
 * @file
 * Contains \Drupal\service_container_annotation_discovery\ServiceContainer\ServiceProvider\ServiceContainerAnnotationDiscoveryServiceProvider
 */

namespace Drupal\service_container_annotation_discovery\ServiceContainer\ServiceProvider;

use Drupal\service_container\ServiceContainer\ServiceProvider\ServiceContainerServiceProvider;

/**
 * Provides render cache service definitions.
 *
 * @codeCoverageIgnore
 *
 */
class ServiceContainerAnnotationDiscoveryServiceProvider extends ServiceContainerServiceProvider {

  /**
   * {@inheritdoc}
   */
  public function getContainerDefinition() {
    $services = array();
    $parameters['service_container.plugin_managers'] = array();
    $parameters['service_container.plugin_manager_types'] = array(
      'annotated' => '\Drupal\service_container_annotation_discovery\Plugin\Discovery\AnnotatedClassDiscovery',
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
    if (!empty($container_definition['parameters']['annotated_plugins_auto_discovery']) && $this->moduleExists('service_container_annotation_discovery')) {
      $this->registerAnnotatedPluginTypes($container_definition, $container_definition['parameters']['annotated_plugins_auto_discovery']);
    }
    parent::alterContainerDefinition($container_definition);
  }

  /**
   * Automatically register all annotated Plugins.
   *
   * @param array $container_definition
   *   The container definition to process.
   * @param array $definition
   *   The parameter definition.
   */
  public function registerAnnotatedPluginTypes(&$container_definition, $parameter_definitions) {
    foreach($parameter_definitions as $definition) {
      $owner = $definition['owner'];
      $type = $definition['type'];

      $this->registerAliasServices($container_definition, $owner, $type);

      $container_definition['services'][$owner . '.' . $type] = array();
      $container_definition['parameters']['service_container.plugin_managers']['annotated'][$owner . '.' . $type] = $definition;
    }
  }
}
