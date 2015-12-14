<?php

/**
 * @file
 * Contains \Drupal\Tests\service_container\DependencyInjection\Dumper\PhpArrayDumperTest
 */

namespace Drupal\Tests\service_container\DependencyInjection\Dumper;

use Drupal\Component\DependencyInjection\Dumper\PhpArrayDumper;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @coversDefaultClass \Drupal\Component\DependencyInjection\Dumper\PhpArrayDumper
 * @group dic
 */
class PhpArrayDumperTest extends \PHPUnit_Framework_TestCase {

  /**
   * @var ContainerBuilder
   */
  protected $container_builder;
  /**
   * @var PhpArrayDumper
   */
  protected $dumper;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $this->container_builder = new ContainerBuilder();
    $this->container_builder->register('foo', 'My/Class');
    $this->container_builder->setAlias('bar', 'foo');
    $this->dumper = new PhpArrayDumper($this->container_builder);
  }

  /**
   * @covers ::dump
   */
  public function test_dump() {
    $dump = unserialize($this->dumper->dump());

    $this->assertTrue(is_array($dump));
    $this->assertEquals($dump['aliases']['bar'], 'foo');
    $this->assertEquals($dump['services']['foo'], array('class' => 'My/Class', 'arguments_count' => 0));
  }

  /**
   * @covers ::getArray
   */
  public function test_getArray() {
    $dump = $this->dumper->getArray();

    $this->assertTrue(is_array($dump));
    $this->assertEquals($dump['aliases']['bar'], 'foo');
    $this->assertEquals($dump['services']['foo'], array('class' => 'My/Class', 'arguments_count' => 0));
  }

}
