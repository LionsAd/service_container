<?php

/**
 * @file
 * Contains \Drupal\Tests\service_container\Plugin\DefaultPluginManagerTest
 */

namespace Drupal\Tests\service_container\Plugin;

use Drupal\Component\Plugin\FallbackPluginManagerInterface;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\service_container\Plugin\DefaultPluginManager;

use Mockery;

/**
 * @coversDefaultClass \Drupal\service_container\Plugin\DefaultPluginManager
 * @group dic
 */
class DefaultPluginManagerTest extends \PHPUnit_Framework_TestCase {

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $foo = Mockery::mock('\stdClass');
    $foo_class = get_class($foo);
    $this->definitions = array(
      'foo' => array(
        'class' => $foo_class,
      ),
    );
    $discovery = Mockery::mock('\Drupal\Component\Plugin\Discovery\DiscoveryInterface');

    $discovery->shouldReceive('getDefinition')
      ->with('foo')
      ->andReturn($this->definitions['foo']);

    $discovery->shouldReceive('getDefinition')
      ->with('foo', TRUE)
      ->andReturn($this->definitions['foo']);

    $discovery->shouldReceive('getDefinition')
      ->with('foo', FALSE)
      ->andReturn($this->definitions['foo']);

    $discovery->shouldReceive('getDefinition')
      ->with('bar', FALSE);

    $discovery->shouldReceive('getDefinition')
      ->with('bar', TRUE)
      ->andThrow(new PluginNotFoundException('bar'));

    $discovery->shouldReceive('getDefinition')
      ->with('bar')
      ->andThrow(new PluginNotFoundException('bar'));

    $discovery->shouldReceive('hasDefinition')
      ->with('foo')
      ->andReturn(TRUE);
    $discovery->shouldReceive('hasDefinition')
      ->with('bar')
      ->andReturn(FALSE);

    $discovery->shouldReceive('getDefinitions')
      ->andReturn($this->definitions);

    $discovery->shouldReceive('createInstance')
      ->with('foo', array())
      ->andReturn(new $foo_class());

    $discovery->shouldReceive('getInstance')
      ->with('foo')
      ->andReturn(new $foo_class());

    $this->pluginManager = new DefaultPluginManager($discovery);
    $plugin_manager = $this->pluginManager;

    $this->fallbackPluginManager = Mockery::mock('\Drupal\Tests\service_container\Plugin\TestFallbackPluginManager[getFallbackPluginId]', array($discovery));
    $this->fallbackPluginManager
      ->shouldReceive('getFallbackPluginId')
      ->with('bar', array())
      ->andReturn('foo');
  }

  /**
   * @covers ::__construct()
   */
  public function test_construct() {
    $this->assertInstanceOf('\Drupal\service_container\Plugin\DefaultPluginManager', $this->pluginManager, 'Plugin manager was constructed successfully.');
  }

  /**
   * @covers ::getDefinition()
   */
  public function test_getDefinition() {
    $this->assertEquals($this->definitions['foo'], $this->pluginManager->getDefinition('foo', TRUE), 'Returned foo definition matches.');
    $this->assertEquals($this->definitions['foo'], $this->pluginManager->getDefinition('foo', FALSE), 'Returned foo definition matches.');
  }
   /**
   * @covers ::getDefinition()
   * @expectedException \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function test_getDefinition_exception() {
    $this->assertNull($this->pluginManager->getDefinition('bar', FALSE), 'Bar definition does not exist.');

    // This throws an exception.
    $this->pluginManager->getDefinition('bar');
  }


  /**
   * @covers ::hasDefinition()
   */
  public function test_hasDefinition() {
    $this->assertTrue($this->pluginManager->hasDefinition('foo'), 'Definition foo exists.');
    $this->assertFalse($this->pluginManager->hasDefinition('bar'), 'Definition bar exists not.');
  }


  /**
   * @covers ::getDefinitions()
   */
  public function test_getDefinitions() {
    $this->assertEquals($this->definitions, $this->pluginManager->getDefinitions(), 'Returned definitions match.');
  }

  /**
   * @covers ::createInstance()
   */
  public function test_createInstance() {
    $this->assertInstanceOf($this->definitions['foo']['class'], $this->pluginManager->createInstance('foo'), 'Returned foo instance matches.');
    $this->assertInstanceOf($this->definitions['foo']['class'], $this->fallbackPluginManager->createInstance('bar'), 'Fallback to foo was successful.');
  }

  /**
   * @covers ::createInstance()
   * @expectedException \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function test_createInstance_exception() {
    $this->pluginManager->createInstance('bar');
  }

  /**
   * @covers ::getInstance()
   */
  public function test_getInstance() {
    $this->assertInstanceOf($this->definitions['foo']['class'], $this->pluginManager->getInstance(array('id' => 'foo')), 'Returned foo instance matches.');
    $this->assertFalse($this->pluginManager->getInstance(array('x' => 'y')), 'No instance returned for wrong definition.');
  }
}

/**
 * Helper class for mockery to derive its mock from with the right interface.
 */
abstract class TestFallbackPluginManager extends DefaultPluginManager implements FallbackPluginManagerInterface {
}
