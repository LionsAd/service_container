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

    $services['database'] = array(
      'class' => 'Drupal\Core\Database\Connection',
      'factory_class' => 'Drupal\Core\Database\Database',
      'factory_method' => 'getConnection',
      'arguments' => array('default'),
    );

    $services['serialization.phpserialize'] = array(
      'class' => 'Drupal\Component\Serialization\PhpSerialize',
    );

    $parameters['factory.keyvalue'] = array();
    $parameters['factory.keyvalue.expirable'] = array();
    $services['keyvalue'] = array(
      'class' => 'Drupal\Core\KeyValueStore\KeyValueFactory',
      'arguments' => array('@service_container', '%factory.keyvalue%')
    );
    $services['keyvalue.database'] = array(
      'class' => 'Drupal\Core\KeyValueStore\KeyValueDatabaseFactory',
      'arguments' => array('@serialization.phpserialize', '@database')
    );
    $services['keyvalue.expirable'] = array(
      'class' => 'Drupal\Core\KeyValueStore\KeyValueExpirableFactory',
      'arguments' => array('@service_container', '%factory.keyvalue.expirable%')
    );
    $services['keyvalue.expirable.database'] = array(
      'class' => 'Drupal\Core\KeyValueStore\KeyValueDatabaseExpirableFactory',
      'arguments' => array('@serialization.phpserialize', '@database'),
      'tags' => array(
        array('name' => 'needs_destruction'),
      ),
    );

    $services['variable'] = array(
      'class' => 'Drupal\service_container\Variable',
    );

    $services['lock'] = array(
      'class' => 'Drupal\Core\Lock\DatabaseLockBackend',
      'arguments' => array('@database'),
      'tags' => array(
        array('name' => 'backend_overridable'),
      ),
    );

    $services['lock.persistent'] = array(
      'class' => 'Drupal\Core\Lock\PersistentDatabaseLockBackend',
      'arguments' => array('@database'),
      'tags' => array(
        array('name' => 'backend_overridable'),
      ),
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
