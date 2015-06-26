<?php

/**
 * @file
 * Contains \Drupal\service_container\ServiceContainer\ServiceProvider\ServiceContainerServiceProvider
 */

namespace Drupal\service_container\ServiceContainer\ServiceProvider;

use Drupal\service_container\DependencyInjection\Container;
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

    $services['drupal7'] = array(
      'class' => 'Drupal\service_container\Legacy\Drupal7',
    );

    $services['service_container'] = array(
      'class' => '\Drupal\service_container\DependencyInjection\Container',
    );

    $services['module_handler'] = array(
      'class' => '\Drupal\service_container\Extension\ModuleHandler',
      'arguments' => array(DRUPAL_ROOT, '@drupal7'),
    );

    $services['module_installer'] = array(
      'class' => '\Drupal\service_container\Extension\ModuleInstaller',
      'arguments' => array('@drupal7'),
    );

    $services['database'] = array(
      'class' => 'Drupal\Core\Database\Connection',
      'factory_class' => 'Drupal\Core\Database\Database',
      'factory_method' => 'getConnection',
      'arguments' => array('default'),
    );

    $services['flood'] = array(
      'class' => '\Drupal\service_container\Flood\LegacyBackend',
      'arguments' => array('@database', '@drupal7'),
      'tags' => array(
        array('name' => 'backend_overridable'),
      ),
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
      'arguments' => array('@drupal7'),
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

    $services['messenger'] = array(
      'class' => 'Drupal\service_container\Messenger\LegacyMessenger',
      'arguments' => array('@drupal7'),
    );

    $services['url_generator'] = array(
      'class' => 'Drupal\service_container\UrlGenerator',
      'arguments' => array('@drupal7'),
    );

    $services['link_generator'] = array(
      'class' => 'Drupal\service_container\LinkGenerator',
      'arguments' => array('@drupal7'),
    );

    $services['current_user'] = array(
      'class' => 'Drupal\service_container\Session\Account',
      'arguments' => array('@drupal7', '@variable'),
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
      'arguments' => array('@drupal7'),
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

    return array(
      'parameters' => $parameters,
      'services' => $services,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function alterContainerDefinition(&$container_definition) {
    foreach(array('annotated_plugins_auto_discovery', 'ctools_plugins_auto_discovery') as $prefix) {
      $container_definition['parameters'][$prefix] = array();
      foreach($container_definition['parameters'] as $parameter => $value) {
        if (strpos($parameter, $prefix) === 0) {
          $container_definition['parameters'][$prefix] = array_merge($container_definition['parameters'][$prefix], $value);
        }
      }
    }

    if (!empty($container_definition['parameters']['ctools_plugins_auto_discovery']) && $this->moduleExists('ctools')) {
      $ctools_types = $this->cToolsGetTypes();
      $filtered_types = array_intersect_key($ctools_types, array_flip($container_definition['parameters']['ctools_plugins_auto_discovery']));
      $this->registerCToolsPluginTypes($container_definition, $filtered_types);
    }

    if (!empty($container_definition['parameters']['annotated_plugins_auto_discovery']) && $this->moduleExists('service_container_annotation_discovery')) {
      $this->registerAnnotatedPluginTypes($container_definition, $container_definition['parameters']['annotated_plugins_auto_discovery']);
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
          $container_definition['services'][$tag['prefix'] . $key] = $definition + array('public' => FALSE);
        }
      }
    }
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
      if (isset($definition['plugin_manager_name']) && !empty($definition['plugin_manager_name'])) {
        $plugin_manager_name = $definition['plugin_manager_name'];
      } else {
        $plugin_manager_name  = $definition['owner'] . '.' . $definition['type'];
        $this->registerAliasServices($container_definition, $definition['owner'], $definition['type']);
      }

      $container_definition['services'][$plugin_manager_name] = array();
      $container_definition['parameters']['service_container.plugin_managers']['annotated'][$plugin_manager_name] = $definition;
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
  public function registerCToolsPluginTypes(&$container_definition, $ctools_types) {
    foreach($ctools_types as $owner => $plugins) {
      foreach($plugins as $plugin_type => $plugin_data) {
        if (isset($container_definition['parameters']['service_container.plugin_managers']['ctools'][$owner . '.' . $plugin_type])) {
          continue;
        }
        $this->registerAliasServices($container_definition, $owner, $plugin_type);

        $container_definition['parameters']['service_container.plugin_managers']['ctools'][$owner . '.' . $plugin_type] = array(
          'owner' => $owner,
          'type' => $plugin_type,
        );
      }
    }
  }

  /**
   * Register aliases for the service.
   *
   * @param array $container_definition
   *   The container definition to process.
   * @param string $owner
   *   The owner, here, the name of the module
   * @param string $plugin_type
   *   The type of plugin
   */
  public function registerAliasServices(&$container_definition, $owner, $plugin_type) {
    // Register service with original string.
    $name = $owner . '.' . $plugin_type;
    $container_definition['services'][$name] = array();

    // Check candidates for needed aliases.
    $candidates = array();
    $candidates[$owner . '.' . Container::underscore($plugin_type)] = TRUE;
    $candidates[$name] = FALSE;

    foreach ($candidates as $candidate => $value) {
      if ($value) {
        $container_definition['services'][$candidate] = array(
          'alias' => $name,
        );
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
   * @param string $name
   *   The owning module of the plugin.
   * @param string $discovery_class
   *   The discovery class in use to discover plugins.
   * @param array $plugin_manager
   *   The plugin manager definition
   *
   * @return array
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

  /**
   * Return the full list of plugin type info for all plugin types registered in
   * the current system.
   *
   * This function manages its own cache getting/setting, and should always be
   * used as the way to initially populate the list of plugin types. Make sure you
   * call this function to properly populate the ctools_plugin_type_info static
   * variable.
   *
   * @return array
   *   A multilevel array of plugin type info, the outer array keyed on module
   *   name and each inner array keyed on plugin type name.
   */
  public function cToolsGetTypes() {
    ctools_include('plugins');
    return ctools_plugin_get_plugin_type_info();
  }

  /**
   * Determines whether a given module exists.
   *
   * @param string $name
   *   The name of the module (without the .module extension).
   *
   * @return bool
   *   TRUE if the module is both installed and enabled, FALSE otherwise.
   */
  public function moduleExists($name) {
    return module_exists($name);
  }
}
