<?php

/**
 * @file
 * Contains \Drupal\service_container\ServiceContainer\ServiceProvider\ServiceContainerServiceProvider
 */

namespace Drupal\service_container\ServiceContainer\ServiceProvider;

use Drupal\service_container\DependencyInjection\ServiceProviderInterface;

/**
 * Provides render cache service definitions.
 *
 * @codeCoverageIgnore
 *
 * @todo The alter and plugin_manager_parts are unit testable.
 */
class ServiceContainerServiceProvider implements ServiceProviderInterface {

  /**
   * {@inheritdoc}
   */
  public function getContainerDefinition() {
    $parameters = array();
    $parameters['service_container.static_event_listeners'] = array();
    $parameters['service_container.plugin_managers'] = array();
    $parameters['service_container.plugin_manager_types'] = array(
      'ctools' => '\Drupal\service_container\Plugin\Discovery\CToolsPluginDiscovery',
    );

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
      'class' => 'Drupal\service_container\KeyValueStore\KeyValueFactory',
      'arguments' => array('@service_container', '%factory.keyvalue%')
    );
    $services['keyvalue.database'] = array(
      'class' => 'Drupal\Core\KeyValueStore\KeyValueDatabaseFactory',
      'arguments' => array('@serialization.phpserialize', '@database')
    );
    $services['keyvalue.expirable'] = array(
      'class' => '\Drupal\service_container\KeyValueStore\KeyValueExpirableFactory',
      'arguments' => array('@service_container', '%factory.keyvalue.expirable%')
    );
    $services['keyvalue.expirable.database'] = array(
      'class' => 'Drupal\service_container\KeyValueStore\KeyValueDatabaseExpirableFactory',
      'arguments' => array('@serialization.phpserialize', '@database'),
      'tags' => array(
        array('name' => 'needs_destruction'),
      ),
    );

    $services['state'] = array(
      'class' => 'Drupal\Core\State\State',
      'arguments' => array('@keyvalue'),
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

    $services['url_generator'] = array(
      'class' => 'Drupal\service_container\UrlGenerator',
    );

    $services['link_generator'] = array(
      'class' => 'Drupal\service_container\LinkGenerator',
    );

    $services['current_user'] = array(
      'class' => 'Drupal\service_container\Session\Account',
      'arguments' => array('@variable'),
    );

    // Logging integration.
    $services['logger.factory'] = array(
      'class' => 'Drupal\service_container\Logger\LoggerChannelFactory',
      'calls' => array(
        array('addLogger', array('@logger.dblog')),
      ),
    );

    $services['logger.dblog'] = array(
      'class' => 'Drupal\service_container\Logger\WatchdogLogger',
      'tags' => array(
        array('name' => 'logger'),
      ),
    );

    $services['logger.channel.default'] = array(
      'class' => 'Drupal\service_container\Logger\LoggerChannel',
      'factory_service' => 'logger.factory',
      'factory_method' => 'get',
      'arguments' => array('system'),
    );

    $services['logger.channel.php'] = array(
      'class' => 'Drupal\service_container\Logger\LoggerChannel',
      'factory_service' => 'logger.factory',
      'factory_method' => 'get',
      'arguments' => array('php'),
    );

    $services['logger.channel.cron'] = array(
      'class' => 'Drupal\service_container\Logger\LoggerChannel',
      'factory_service' => 'logger.factory',
      'factory_method' => 'get',
      'arguments' => array('cron'),
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
          array_unshift($definition['arguments'], $definition);
          $container_definition['services'][$tag['prefix'] . $key] = $definition + array('public' => FALSE);
        }
      }
    }
  }

  /**
   * Processes plugin managers of varying types and registers them in the
   * container based on the given discovery class.
   *
   * @param array $container_definition
   *   The container definition to process.
   * @param string $discovery_class
   *   The discovery class this plugin manager type uses.
   * @param array $plugin_managers
   *   The plugin managers to register.
   */
  public function processPluginManagers(&$container_definition, $discovery_class, $plugin_managers) {
    foreach ($plugin_managers as $name => $plugin_manager) {
      if (!isset($container_definition['services'][$name]) || !empty($container_definition['services'][$name])) {
        continue;
      }

      $container_definition['services'][$name] = $this->getPluginManagerDefinition($name, $discovery_class, $plugin_manager);
      $tags = $container_definition['services'][$name]['tags'];

      foreach ($tags as $tag) {
        $tag_name = $tag['name'];
        unset($tag['name']);
        $container_definition['tags'][$tag_name][$name][] = $tag;
      }
    }
  }

  /**
   * Gets plugin manager definition to make it simpler to register plugins.
   *
   * @param string $owner
   *   The owning module of the plugin.
   * @param string $identifier
   *   The internal identifier of the plugin, used for getting from the
   *   container via $owner.$identifier and for storing the internal class.
   * @param string $type
   *   The type of the plugin.
   * @param string $plugin_type
   *   The used plugin type, e.g. ctools.plugin. (default)
   */
  protected function getPluginManagerDefinition($name, $discovery_class, $plugin_manager) {
    $prefix = "$name.internal.";
    return array(
      'class' => '\Drupal\service_container\Plugin\ContainerAwarePluginManager',
      'arguments' => array($prefix),
      'calls' => array(
        array('setContainer', array('@service_container')),
      ),
      'tags' => array(
        array(
          'name' => 'plugin_manager',
          'discovery_class' => $discovery_class,
          'prefix' => $prefix,
          'plugin_manager_definition' => $plugin_manager,
        ),
      ),
    );
  }
}
