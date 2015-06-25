<?php

/**
 * @file
 * Contains \Drupal\service_container_symfony\ServiceContainer\ServiceProvider\ServiceContainerSymfonyServiceProvider
 */

namespace Drupal\service_container_symfony\ServiceContainer\ServiceProvider;

use Drupal\Component\FileCache\FileCacheFactory;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\YamlFileLoader;
use Drupal\Core\DependencyInjection\Dumper\PhpArrayDumper;
use Drupal\service_container\DependencyInjection\ServiceProviderInterface;

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
    FileCacheFactory::setConfiguration(
      array(
        'default' => array(
          'class' => '\Drupal\Component\FileCache\NullFileCache'
        )
      )
    );
    $container_builder = new ContainerBuilder();
    $yaml_loader = new YamlFileLoader($container_builder);
    $dumper = new PhpArrayDumper($container_builder);
    $container_definitions = array();

    foreach (module_list() as $module) {
      $services = drupal_get_path('module', $module) . '/' . $module . '.services.yml';
      if (file_exists($services)) {
        $yaml_loader->load($services);
        $container_definitions = array_merge_recursive($container_definitions, $dumper->getArray());
      }
    }

    // Disabled for now.
    // $container_builder->compile();
    return $container_definitions;
  }

  /**
   * {@inheritdoc}
   */
  public function alterContainerDefinition(&$container_definition) {}

}
