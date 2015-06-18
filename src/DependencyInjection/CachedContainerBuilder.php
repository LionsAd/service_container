<?php

/**
 * @file
 * Contains \Drupal\service_container\DependencyInjection\CachedContainerBuilder
 */

namespace Drupal\service_container\DependencyInjection;

use Drupal\Component\Plugin\PluginManagerInterface;
use DrupalCacheInterface;

/**
 * CachedContainerBuilder retrieves the container definition from cache
 * or builds it.
 *
 * The reason is to skip invoking all the ctools_* functions via the discovery
 * interface, which needs all modules loaded.
 *
 * This is especially useful to use the ServiceContainer safely within
 * hook_boot() or even earlier.
 *
 * @ingroup dic
 */
class CachedContainerBuilder extends ContainerBuilder {

  /**
   * The Drupal core cache bin.
   *
   * @var \DrupalCacheInterface
   */
  protected $cache;

  /**
   * The cached definition.
   *
   * @var array
   */
  protected $cachedDefinition;

  /**
   * Constructs a ContainerBuilder object.
   *
   * @param PluginManagerInterface $service_provider_manager
   *   The service provider manager that provides the service providers,
   *   which define the services used in the container.
   * @param \DrupalCacheInterface $cache
   *   The cache bin used to store retrieve the container to/from.
   *   To get a cache object use e.g.: $cache = _cache_get_object('cache');
   */
  public function __construct(PluginManagerInterface $service_provider_manager, DrupalCacheInterface $cache) {
    $this->cache = $cache;
    parent::__construct($service_provider_manager);
  }

  /**
   * {@inheritdoc}
   */
  public function getContainerDefinition() {
    $definition = $this->getCache();
    if (!$definition) {
      $definition = parent::getContainerDefinition();
      $this->setCache($definition);
    }

    return $definition;
  }

  /**
   * Determines if the container is cached.
   *
   * @return bool
   *   Returns TRUE if the container definition is cached, FALSE otherwise.
   */
  public function isCached() {
    $definition = $this->getCache();

    return (bool) $definition;
  }

  /**
   * Returns the cache id of the container definition.
   *
   * @return string
   *   The hardcoded cache id or via variable_get() if defined.
   *
   * @codeCoverageIgnore
   */
  protected function getCacheId() {
    return variable_get('service_container_container_cid', 'service_container:container_definition');
  }

  /**
   * Retrieves the cache id of the container definition.
   *
   * @return string
   *   The hardcoded cache id or via variable_get() if defined.
   */
  protected function getCache() {
    if (isset($this->cachedDefinition)) {
      return $this->cachedDefinition;
    }

    $cache =  $this->cache->get($this->getCacheId());

    $this->cachedDefinition = FALSE;
    if (!empty($cache->data)) {
      $this->cachedDefinition = $cache->data;
    }

    return $this->cachedDefinition;
  }

  /**
   * Caches the builded container definition.
   *
   * @param array
   *   The container definition array.
   */
  protected function setCache(array $definition) {
    $this->cache->set($this->getCacheId(), $definition);
    $this->cachedDefinition = $definition;
  }

  /**
   * Reset the internal cache.
   *
   * Note: This is just thought for tests.
   */
  public function reset() {
    $this->cachedDefinition = NULL;
    $this->cache->clear($this->getCacheId());
  }

}
