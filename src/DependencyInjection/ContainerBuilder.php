<?php

/**
 * @file
 * Contains \Drupal\service_container\DependencyInjection\ContainerBuilder
 */

namespace Drupal\service_container\DependencyInjection;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Component\Utility\NestedArray;

/**
 * ContainerBuilder retrieves container definitions from service providers to
 * build a Container.
 *
 * @ingroup dic
 */
class ContainerBuilder implements ContainerBuilderInterface {

  /**
   * The plugin manager that provides the service definition providers.
   *
   * @var PluginManagerInterface
   */
  protected $serviceProviderManager;

  /**
   * The container class to instantiate.
   *
   * To override this, use a service called container and set a class
   * attribute.
   */
  protected $containerClass = '\Drupal\service_container\DependencyInjection\Container';

  /**
   * Constructs a ContainerBuilder object.
   *
   * @param PluginManagerInterface $service_provider_manager
   *   The service provider manager that provides the service providers,
   *   which define the services used in the container.
   */
  public function __construct(PluginManagerInterface $service_provider_manager) {
    $this->serviceProviderManager = $service_provider_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function getContainerDefinition() {
    $definitions = $this->serviceProviderManager->getDefinitions();
    $container_definition = array();
    $service_providers = array();

    // Populate service providers.
    foreach ($definitions as $plugin_id => $definition) {
      $service_providers[$plugin_id] = $this->serviceProviderManager->createInstance($plugin_id);
    }

    // Get container definition of each service provider and merge them.
    foreach ($definitions as $plugin_id => $definition) {
      $service_provider = $service_providers[$plugin_id];
      $container_definition = NestedArray::mergeDeep($container_definition, $service_provider->getContainerDefinition());
    }

    $container_definition += array(
      'services' => array(),
      'parameters' => array(),
    ); // @codeCoverageIgnore

    // Find and setup tags for container altering.
    $container_definition['tags'] = array();

    // Setup the tags structure.
    foreach ($container_definition['services'] as $service => $definition) {
      if (isset($definition['tags'])) {
        foreach ($definition['tags'] as $tag) {
          $tag_name = $tag['name'];
          unset($tag['name']);
          $container_definition['tags'][$tag_name][$service][] = $tag;
        }
      }
    }

    // Ensure container definition can be altered.
    foreach ($definitions as $plugin_id => $definition) {
      $service_provider = $service_providers[$plugin_id];
      $service_provider->alterContainerDefinition($container_definition);
    }

    // Last give a chance for traditional modules to alter this.
    $this->moduleAlter($container_definition);

    // Remove the tags again, not needed for the final build of the container.
    unset($container_definition['tags']);

    return $container_definition;
  }

  /**
   * {@inheritdoc}
   */
  public function compile() {
    $definition = $this->getContainerDefinition();

    if (!empty($definition['services']['service_container']['class'])) {
      $this->containerClass = $definition['services']['service_container']['class'];
    }

    return new $this->containerClass($definition);
  }

  /**
   * Provides class based version of drupal_alter() to allow testing.
   *
   * This function must be mocked for unit tests.
   *
   * Note: Only the container builder needs this, other classes should
   *       use the ModuleHandler within the container.
   *
   * @param $container_definition
   *   The fully build container definition that can be altered by modules now.
   *
   * @codeCoverageIgnore
   */
  protected function moduleAlter(&$container_definition) {
    drupal_alter('service_container_container_build', $container_definition);
  }
}
