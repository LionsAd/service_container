<?php

/**
 * @file
 * Contains \Drupal\Tests\service_container\DependencyInjection\CachedContainerBuilderTest
 */

namespace Drupal\Tests\service_container\DependencyInjection;

use Drupal\service_container\DependencyInjection\ContainerBuilder;
use Drupal\service_container\DependencyInjection\ContainerInterface;
use Drupal\service_container\DependencyInjection\ServiceProviderInterface;
use Drupal\Component\Plugin\PluginManagerInterface;

use Mockery;

/**
 * @coversDefaultClass \Drupal\service_container\DependencyInjection\CachedContainerBuilder
 * @group dic
 */
class CachedContainerBuilderTest extends \PHPUnit_Framework_TestCase {
 
  /**
   * @var \DrupalCacheInterface|\Mockery\MockInterface
   */
  protected $cache;

  /**
   * @var \Drupal\Component\Plugin\PluginManagerInterface
   */
  protected $serviceProviderManager;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    // Setup the serviceProviderManager that returns no services.
    $service_provider_manager = Mockery::mock('\Drupal\Component\Plugin\PluginManagerInterface', array(
      'getDefinitions' => array(),
      'getDefinition' => array(),
      'hasDefinition' => FALSE,
      'createInstance' => NULL,
      'getInstance' => NULL,
    ));

    $this->serviceProviderManager = $service_provider_manager;

  }

  /**
   * Tests that CachedContainerBuilder::isCached() works properly.
   * @covers ::__construct()
   * @covers ::isCached()
   * @covers ::getCache()
   */
  public function test_isCached() {
    // It is cached.
    $cached_container_builder = $this->getCachedContainerBuilderMock('service_container:container_definition');
    $this->assertTrue($cached_container_builder->isCached(), 'CachedContainerBuilder is cached.');

    // It is not cached.
    $uncached_container_builder = $this->getCachedContainerBuilderMock('service_container:miss_container_definition');
    $this->assertFalse($uncached_container_builder->isCached(), 'CachedContainerBuilder is not cached.');
  }


  /**
   * Tests that CachedContainerBuilder::getContainerDefinition() works properly.
   * @covers ::getContainerDefinition()
   * @covers ::getCache()
   * @covers ::setCache()
   */
  public function test_getContainerDefinition() {
    $fake_definition = $this->getFakeContainerDefinition();

    // It is cached.
    $cached_container_builder = $this->getCachedContainerBuilderMock('service_container:container_definition');
    $this->assertEquals($fake_definition, $cached_container_builder->getContainerDefinition(), 'CachedContainerBuilder definition matches when cached.');

    // It is not cached.
    $uncached_container_builder = $this->getCachedContainerBuilderMock('service_container:miss_container_definition');
    $this->assertEquals($fake_definition, $uncached_container_builder->getContainerDefinition(), 'CachedContainerBuilder definition matches when not cached.');
  }

  /**
   * @covers ::isCached()
   * @covers ::getContainerDefinition()
   * @covers ::getCache()
   * @covers ::setCache()
   */
  public function test_isCached_getContainerDefinition() {
    $fake_definition = $this->getFakeContainerDefinition();

    // Due to the nature of the isCached() functionality, here are some extra
    // tests to ensure the cached data is stored correctly.

    // It is cached, but isCached() was called before.
    $cached_container_builder = $this->getCachedContainerBuilderMock('service_container:container_definition');
    $this->assertTrue($cached_container_builder->isCached(), 'CachedContainerBuilder is cached.');
    $this->assertEquals($fake_definition, $cached_container_builder->getContainerDefinition(), 'CachedContainerBuilder definition matches when cached.');

    // It is not cached, but isCached() was called before and then its cached.
    $uncached_container_builder = $this->getCachedContainerBuilderMock('service_container:miss_container_definition');
    $this->assertFalse($uncached_container_builder->isCached(), 'CachedContainerBuilder is not cached.');
    $this->assertEquals($fake_definition, $uncached_container_builder->getContainerDefinition(), 'CachedContainerBuilder definition matches when not cached.');
    $this->assertTrue($uncached_container_builder->isCached(), 'CachedContainerBuilder is now cached.');
  }

  /**
   * @covers ::reset()
   */
  public function test_reset() {
    $cache = Mockery::mock('\DrupalCacheInterface');
    $cache->shouldReceive('get')
      ->with('service_container:container_definition')
      ->twice()
      ->andReturn((object) array('data' => $this->getFakeContainerDefinition()));

    $cache->shouldReceive('clear')
      ->with('service_container:container_definition')
      ->once();

    $cached_container_builder = $this->getCachedContainerBuilderMock('service_container:container_definition', $cache);
    $cached_container_builder->getContainerDefinition();
    $cached_container_builder->getContainerDefinition();

    $cached_container_builder->reset();
    $cached_container_builder->getContainerDefinition();
  }

  protected function getCachedContainerBuilderMock($cid, $cache = NULL) {
    $fake_definition = $this->getFakeContainerDefinition();

    if (!isset($cache)) {
      if (!isset($this->cache)) {
        $this->cache = $this->setupCache($fake_definition);
      }
      $cache = $this->cache;
    }

    $container_builder = Mockery::mock('\Drupal\service_container\DependencyInjection\CachedContainerBuilder[getCacheId,moduleAlter]', array($this->serviceProviderManager, $cache));
    $container_builder->shouldAllowMockingProtectedMethods();

    $container_builder->shouldReceive('getCacheId')
      ->andReturn($cid);
    $container_builder->shouldReceive('moduleAlter')
      ->with(
        Mockery::on(function(&$container_definition) use ($fake_definition) {
          $container_definition['parameters'] = $fake_definition['parameters'];
          $container_definition['services'] = $fake_definition['services'];
          return TRUE;
        })
      );

    return $container_builder;
  }

  /**
   * Returns a fake container definition used for testing.
   *
   * @return array
   *   The fake container definition with services and parameters.
   */
  protected function getFakeContainerDefinition() {
    $parameters = array();
    $parameters['some_config'] = 'foo';
    $parameters['some_other_config'] = 'kitten';

    $services = array();
    $services['container'] = array(
      'class' => '\Drupal\service_container\DependencyInjection\Container',
      'tags' => array(
        array('tagged-service'),
      ),
    );
    $services['some_service'] = array(
      'class' => '\Drupal\service_container\Service\SomeService',
      'arguments' => array('@service_container', '%some_config%'),
      'calls' => array(
        array('setContainer', array('@service_container')),
      ),
      'tags' => array(
        array('tagged-service'),
        array('another-tag', array('tag-value' => 42, 'tag-value2' => 23)),
      ),
      'priority' => 0,
    );

    return array(
      'parameters' => $parameters,
      'services' => $services,
    );
  }

  protected function setupCache($fake_definition) {
    // Setup the 'cache' bin.
    $cache = Mockery::mock('\DrupalCacheInterface');
    $cache->shouldReceive('get')
      ->with('service_container:container_definition')
      ->once()
      ->andReturn((object) array('data' => $fake_definition));
    $cache->shouldReceive('get')
      ->with('service_container:miss_container_definition')
      ->once()
      ->andReturn(FALSE, TRUE);
    $cache->shouldReceive('set');
    return $cache;
  }
}
