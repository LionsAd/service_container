<?php
/**
 * @file
 * Contains ServiceContainer
 */

use Drupal\service_container\DependencyInjection\CachedContainerBuilder;
use Drupal\service_container\DependencyInjection\ServiceProviderPluginManager;

/**
 * Static Service Container wrapper extension - initializes the container.
 */
class ServiceContainer extends Drupal {

  /**
   * Initializes the container.
   *
   * This can be safely called from hook_boot() because the container will
   * only be build if we have reached the DRUPAL_BOOTSTRAP_FULL phase.
   *
   * @return bool
   *   TRUE when the container was initialized, FALSE otherwise.
   */
  public static function init() {
    // If this is set already, just return.
    if (isset(static::$container)) {
      return TRUE;
    }

    $container_builder = static::getContainerBuilder();

    if ($container_builder->isCached()) {
      static::$container = $container_builder->compile();
      static::dispatchStaticEvent('containerReady', array(static::$container));
      return TRUE;
    }

    // If we have not yet fully bootstrapped, we can't build the container.
    if (drupal_bootstrap(NULL, FALSE) != DRUPAL_BOOTSTRAP_FULL) {
      return FALSE;
    }

    // Rebuild the container.
    static::$container = $container_builder->compile();
    static::dispatchStaticEvent('containerReady', array(static::$container));

    return (bool) static::$container;
  }

  /**
   * Dispatches an event to static classes.
   *
   * This is needed to inform other static classes when the container is ready.
   *
   * @param string $event
   *   The member function to call.
   * @param array $arguments
   *   The arguments to pass.
   */
  protected static function dispatchStaticEvent($event, $arguments) {
    $event_listeners = static::$container->getParameter('service_container.static_event_listeners');
    foreach ($event_listeners as $class) {
      $function = $class . '::' . $event;
      if (is_callable($function)) {
        call_user_func_array($function, $arguments);
      }
    }
  }

  /**
   * Reset the internal cache.
   *
   * Note: This is just thought for tests.
   */
  public static function reset() {
    static::getContainerBuilder()->reset();
    static::$container = NULL;
  }

  /**
   * @return \Drupal\service_container\DependencyInjection\CachedContainerBuilder
   */
  protected static function getContainerBuilder() {
    $service_provider_manager = new ServiceProviderPluginManager();
    // This is an internal API, but we need the cache object.
    $cache = _cache_get_object('cache');

    $container_builder = new CachedContainerBuilder($service_provider_manager, $cache);
    return $container_builder;
  }
}
