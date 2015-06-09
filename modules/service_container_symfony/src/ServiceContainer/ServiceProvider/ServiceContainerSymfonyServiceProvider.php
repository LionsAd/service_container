<?php

/**
 * @file
 * Contains \Drupal\service_container_symfony\ServiceContainer\ServiceProvider\ServiceContainerSymfonyServiceProvider
 */

namespace Drupal\service_container_symfony\ServiceContainer\ServiceProvider;

use Drupal\service_container_symfony\DependencyInjection\Dumper\PhpArrayDumper;
use Drupal\service_container\DependencyInjection\ServiceProviderInterface;
use Drupal\service_container\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

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
    $modules = module_list();
    $container = new ContainerBuilder();
    $yaml_loader = new YamlFileLoader($container);

    foreach ($modules as $module) {
      $filename = drupal_get_filename('module', $module);
      $services = dirname($filename) . "/$module.services.yml";
      if (file_exists($services)) {
        $yaml_loader->load($services);
      }
    }

    $dumper = new PhpArrayDumper($container);
    return $dumper->getArray();
  }

  /**
   * {@inheritdoc}
   */
  public function alterContainerDefinition(&$container_definition) {}

}
