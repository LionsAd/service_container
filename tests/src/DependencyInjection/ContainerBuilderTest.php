<?php

/**
 * @file
 * Contains \Drupal\Tests\service_container\DependencyInjection\ContainerBuilderTest
 */

namespace Drupal\Tests\service_container\DependencyInjection;

use Drupal\service_container\DependencyInjection\ContainerBuilder;
use Drupal\service_container\DependencyInjection\ContainerInterface;
use Drupal\service_container\DependencyInjection\ServiceProviderInterface;
use Drupal\Component\Plugin\PluginManagerInterface;

use Mockery;
use Mockery\MockInterface;

/**
 * @coversDefaultClass \Drupal\service_container\DependencyInjection\ContainerBuilder
 * @group dic
 */
class ContainerBuilderTest extends \PHPUnit_Framework_TestCase {

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    // Setup the base container definition.
    $this->containerDefinition = $this->getFakeContainerDefinition();

    // Alter the definition in a specified way.
    $altered_definition = $this->containerDefinition;

    $altered_definition['services']['some_service']['tags'][] = array('name' => 'bar');
    $altered_definition['services']['some_service']['tags'][] = array('name' => 'baz');
    $altered_definition['parameters']['some_other_config'] = 'lama';

    $this->alteredDefinition = $altered_definition;

    // Create a service provider providing these definitions.

    $service_provider = Mockery::mock('\Drupal\service_container\DependencyInjection\ServiceProviderInterface');
    $service_provider->shouldReceive('getContainerDefinition')
      ->once()
      ->andReturn($this->containerDefinition);

    $service_provider->shouldReceive('alterContainerDefinition')
      ->with(
        Mockery::on(function(&$container_definition) {
          $container_definition['services']['some_service']['tags'][] = array('name' => 'bar');
          $container_definition['services']['some_service']['tags'][] = array('name' => 'baz');
          $container_definition['parameters']['some_other_config'] = 'lama';
          return TRUE;
        })
      )
      ->once();

    $this->serviceProvider = $service_provider;

    // Set up definitions for the Fake plugin manager.
    $definitions = array(
      'fake_provider' => array(
        'class' => '\Drupal\Tests\service_container\DependencyInjection\FakeProvider',
      ),
    );

    // And create a static plugin manager mock.
    $service_provider_manager = Mockery::mock('\Drupal\Component\Plugin\PluginManagerInterface', array(
      'getDefinitions' => $definitions,
      'getDefinition' => $definitions['fake_provider'],
      'hasDefinition' => TRUE,
      'createInstance' => $this->serviceProvider,
      'getInstance' => $this->serviceProvider,
    ));
    $this->serviceProviderManager = $service_provider_manager;
  }

  /**
   * @covers ::__construct()
   * @covers ::getContainerDefinition()
   */
  public function test_getContainerDefinition() {
    // We need to use a partial mock as the alter method calls procedural code.
    $container_builder = Mockery::mock('\Drupal\service_container\DependencyInjection\ContainerBuilder[moduleAlter]', array($this->serviceProviderManager));
    $container_builder->shouldAllowMockingProtectedMethods();
    $container_builder->shouldReceive('moduleAlter')
      ->once();

    $definition = $container_builder->getContainerDefinition();
    $this->assertEquals($definition, $this->alteredDefinition, 'Definition of the container matches.');
  }

  /**
   * @covers ::getContainerDefinition()
   * @covers ::moduleAlter()
   */
  public function test_alter() {
    $container_builder = Mockery::mock('\Drupal\service_container\DependencyInjection\ContainerBuilder[moduleAlter]', array($this->serviceProviderManager));
    $container_builder->shouldAllowMockingProtectedMethods();

    $container_builder->shouldReceive('moduleAlter')
      ->with(
        Mockery::on(function(&$container_definition) {
          $container_definition['services']['foo'] = array('class' => 'FooService');
          return TRUE;
        })
      );
    $altered_definition = $this->alteredDefinition;
    $altered_definition['services']['foo'] = array('class' => 'FooService');

    $definition = $container_builder->getContainerDefinition();
    $this->assertEquals($definition, $altered_definition, 'Definition of the container when altered matches.');
  }

  /**
   * @covers ::getContainerDefinition()
   * @covers ::moduleAlter()
   */
  public function test_alterWithTags() {
    $container_builder = Mockery::mock('\Drupal\service_container\DependencyInjection\ContainerBuilder[moduleAlter]', array($this->serviceProviderManager));
    $container_builder->shouldAllowMockingProtectedMethods();

    $container_builder->shouldReceive('moduleAlter')
      ->with(
        Mockery::on(function(&$container_definition) {
          $container_definition['parameters']['services_tagged'] = implode(',', array_keys($container_definition['tags']['tagged-service']));
          $container_definition['parameters']['services_tagged_another'] = '';
          foreach ($container_definition['tags']['another-tag'] as $service => $tags) {
            $container_definition['parameters']['services_tagged_another'] .= $service . '|';
            foreach ($tags as $tag_values) {
              $values = array();
              foreach ($tag_values as $key => $value) {
                $values[] = $key . ':' . $value;
              }
              $container_definition['parameters']['services_tagged_another'] .= implode(',', $values);
            }
          }
          return TRUE;
        })
      );
    $altered_definition = $this->alteredDefinition;
    $altered_definition['parameters']['services_tagged'] = 'container,some_service';
    $altered_definition['parameters']['services_tagged_another'] = 'some_service|tag-value:42,tag-value2:23';

    $definition = $container_builder->getContainerDefinition();
    $this->assertEquals($definition, $altered_definition, 'Definition of the container matches altered definition when checking tags.');
  }

  /**
   * @covers ::compile()
   */
  public function test_compile() {
    // Create a fake container class implementing the interface.
    $fake_container = Mockery::mock('\Drupal\service_container\DependencyInjection\ContainerInterface');
    $fake_container_class = get_class($fake_container);

    $container_builder = Mockery::mock('\Drupal\service_container\DependencyInjection\ContainerBuilder[moduleAlter]', array($this->serviceProviderManager));
    $container_builder->shouldAllowMockingProtectedMethods();
    $container_builder->shouldReceive('moduleAlter')
      ->with(
        Mockery::on(function(&$container_definition) use ($fake_container_class) {
          $container_definition['services']['service_container']['class'] = $fake_container_class;
          return TRUE;
        })
      );

    $container = $container_builder->compile();

    // Check this returns the right expected class and interfaces.
    $this->assertEquals(TRUE, $container instanceof ContainerInterface, 'Container has instanceof ContainerInterface.');
    $this->assertEquals(TRUE, $container instanceof MockInterface, 'Container has instanceof MockInterface.');
    $this->assertEquals(TRUE, $container instanceof $fake_container_class, 'Container has instanceof dynamic fake container class.');
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
        array('name' => 'tagged-service'),
      ),
    );
    $services['some_service'] = array(
      'class' => '\Drupal\service_container\Service\SomeService',
      'arguments' => array('@service_container', '%some_config%'),
      'calls' => array(
        array('setContainer', array('@service_container')),
      ),
      'tags' => array(
        array('name' => 'tagged-service'),
        array('name' => 'another-tag', 'tag-value' => 42, 'tag-value2' => 23),
      ),
      'priority' => 0,
    );

    return array(
      'parameters' => $parameters,
      'services' => $services,
    );
  }
}
