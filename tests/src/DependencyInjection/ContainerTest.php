<?php

/**
 * @file
 * Contains \Drupal\Tests\service_container\DependencyInjection\ContainerTest
 */

namespace Drupal\Tests\service_container\DependencyInjection;

use Drupal\service_container\DependencyInjection\Container;
use Drupal\service_container\DependencyInjection\ContainerInterface;

use Mockery;

/**
 * @coversDefaultClass \Drupal\service_container\DependencyInjection\Container
 * @group dic
 */
class ContainerTest extends \PHPUnit_Framework_TestCase {

  /**
   * The tested container.
   *
   * @var \Drupal\service_container\DependencyInjection\Container
   */
  protected $container;

  /**
   * The container definition used for the test.
   *
   * @var []
   */
  protected $containerDefinition;

  public function setUp() {
    $this->containerDefinition = $this->getMockContainerDefinition();
    $this->container = new Container($this->containerDefinition);
  }

  /**
   * Tests that Container::hasDefinition() works properly.
   * @covers ::hasDefinition()
   */
  public function test_hasDefinition() {
    $this->assertEquals($this->container->hasDefinition('service_container'), TRUE, 'Container has definition of itself.');
    $this->assertEquals($this->container->hasDefinition('service.does_not_exist'), FALSE, 'Container does not have non-existent service.');
    $this->assertEquals($this->container->hasDefinition('service.provider'), TRUE, 'Container has service.provider service.');
  }

  /**
   * Tests that Container::getDefinition() works properly.
   * @covers ::getDefinition()
   */
  public function test_getDefinition() {
    $this->assertEquals( $this->containerDefinition['services']['service_container'], $this->container->getDefinition('service_container'), 'Container definition matches for container service.');
    $this->assertEquals( $this->containerDefinition['services']['service.provider'], $this->container->getDefinition('service.provider'), 'Container definition matches for service.provider service.');
  }

  /**
   * Tests that Container::getDefinition() works properly.
   * @expectedException \RuntimeException
   * @covers ::getDefinition()
   */
  public function test_getDefinition_exception() {
    $this->container->getDefinition('service_not_exist');
  }


  /**
   * Tests that Container::getDefinitions() works properly.
   * @covers ::getDefinitions()
   */
  public function test_getDefinitions() {
    $this->assertEquals($this->containerDefinition['services'], $this->container->getDefinitions(), 'Container definition matches input.');
  }

  /**
   * Tests that Container::getParameter() works properly.
   * @covers ::getParameter()
   */
  public function test_getParameter() {
    $this->assertEquals($this->containerDefinition['parameters']['some_config'], $this->container->getParameter('some_config'), 'Container parameter matches for %some_config%.');
    $this->assertEquals($this->containerDefinition['parameters']['some_other_config'], $this->container->getParameter('some_other_config'), 'Container parameter matches for %some_other_config%.');
  }

  /**
   * Tests that Container::hasParameter() works properly.
   * @covers ::hasParameter()
   */
  public function test_hasParameter() {
    $this->assertTrue($this->container->hasParameter('some_config'), 'Container parameters include %some_config%.');
    $this->assertFalse($this->container->hasParameter('some_config_not_exists'), 'Container parameters do not include %some_config_not_exists%.');
  }

  /**
   * Tests that Container::setParameter() in an unfrozen case works properly.
   *
   * @covers ::setParameter()
   */
  public function test_setParameter_unfrozenContainer() {
    $this->container = new Container($this->containerDefinition, FALSE);
    $this->container->setParameter('some_config', 'new_value');
    $this->assertEquals('new_value', $this->container->getParameter('some_config'), 'Container parameters can be set.');
  }

  /**
   * Tests that Container::setParameter() in a frozen case works properly.
   *
   * @covers ::setParameter()
   *
   * @expectedException \BadMethodCallException
   */
  public function test_setParameter_frozenContainer() {
    $this->container->setParameter('some_config', 'new_value');
  }

  /**
   * Tests that Container::get() works properly.
   * @covers ::get()
   * @covers ::getService()
   */
  public function test_get() {
    $container = $this->container->get('service_container');
    $this->assertSame($this->container, $container, 'Container can be retrieved from itself.');

    // Retrieve services of the container.
    $other_service_class = $this->containerDefinition['services']['other.service']['class'];
    $other_service = $this->container->get('other.service');
    $this->assertInstanceOf($other_service_class, $other_service, 'other.service has the right class.');

    $some_parameter = $this->containerDefinition['parameters']['some_config'];
    $some_other_parameter = $this->containerDefinition['parameters']['some_other_config'];

    $service = $this->container->get('service.provider');

    $this->assertEquals($other_service, $service->getSomeOtherService(), '@other.service was injected via constructor.');
    $this->assertEquals($some_parameter, $service->getSomeParameter(), '%some_config% was injected via constructor.');
    $this->assertEquals($this->container, $service->getContainer(), 'Container was injected via setter injection.');
    $this->assertEquals($some_other_parameter, $service->getSomeOtherParameter(), '%some_other_config% was injected via setter injection.');
  }

  /**
   * Tests that Container::set() works properly.
   *
   * @covers ::set()
   */
  public function test_set() {
    $this->assertNull($this->container->get('new_id', ContainerInterface::NULL_ON_INVALID_REFERENCE));
    $mock_service = new MockService();
    $this->container->set('new_id', $mock_service);

    $this->assertSame($mock_service, $this->container->get('new_id'), 'A manual set service works as expected.');
  }

  /**
   * Tests that Container::has() works properly.
   *
   * @covers ::has()
   */
  public function test_has() {
    $this->assertTrue($this->container->has('other.service'));
    $this->assertFalse($this->container->has('another.service'));

    // Set the service manually, ensure that its also respected.
    $mock_service = new MockService();
    $this->container->set('another.service', $mock_service);
    $this->assertTrue($this->container->has('another.service'));
  }

  /**
   * Tests that Container::get() for circular dependencies works properly.
   * @expectedException \RuntimeException
   * @covers ::get()
   * @covers ::getService()
   */
  public function test_get_circular() {
    $this->container->get('circular_dependency');
  }

  /**
   * Tests that Container::get() for non-existant dependencies works properly.
   * @expectedException \RuntimeException
   * @covers ::get()
   * @covers ::getService()
   */
  public function test_get_exception() {
    $this->container->get('service_not_exists');
  }

  /**
   * Tests that Container::get() for non-existant parameters works properly.
   * @covers ::get()
   * @covers ::getService()
   * @covers ::expandArguments()
   */
  public function test_get_notFound_parameter() {
    $service = $this->container->get('service_parameter_not_exists', ContainerInterface::NULL_ON_INVALID_REFERENCE);
    $this->assertNull($service->getSomeParameter(), 'Some parameter is NULL.');
  }

  /**
   * Tests Container::get() with an exception due to missing parameter on the second call.
   *
   * @covers ::get()
   * @covers ::getService()
   * @covers ::expandArguments()
   *
   * @expectedException \RuntimeException
   */
  public function test_get_notFound_parameterWithExceptionOnSecondCall() {
    $service = $this->container->get('service_parameter_not_exists', ContainerInterface::NULL_ON_INVALID_REFERENCE);
    $this->assertNull($service->getSomeParameter(), 'Some parameter is NULL.');

    // Reset the service.
    $this->container->set('service_parameter_not_exists', NULL);
    $this->container->get('service_parameter_not_exists');
  }

  /**
   * Tests that Container::get() for non-existant parameters works properly.
   * @expectedException \RuntimeException
   * @covers ::get()
   * @covers ::getService()
   * @covers ::expandArguments()
   */
  public function test_get_notFound_parameter_exception() {
    $this->container->get('service_parameter_not_exists');
  }

  /**
   * Tests that Container::get() for non-existent dependencies works properly.
   * @covers ::get()
   * @covers ::getService()
   * @covers ::expandArguments()
   */
  public function test_get_notFound_dependency() {
    $service = $this->container->get('service_dependency_not_exists', ContainerInterface::NULL_ON_INVALID_REFERENCE);
    $this->assertNull($service->getSomeOtherService(), 'Some other service is NULL.');
  }

  /**
   * Tests that Container::get() for non-existant dependencies works properly.
   * @expectedException \RuntimeException
   * @covers ::get()
   * @covers ::getService()
   * @covers ::expandArguments()
   */
  public function test_get_notFound_dependency_exception() {
    $this->container->get('service_dependency_not_exists');
  }


  /**
   * Tests that Container::get() for non-existant dependencies works properly.
   * @covers ::get()
   * @covers ::getService()
   */
  public function test_get_notFound() {
    $this->assertNull($this->container->get('service_not_exists', ContainerInterface::NULL_ON_INVALID_REFERENCE), 'Not found service does not throw exception.');
  }

  /**
   * Tests multiple Container::get() calls for non-existing dependencies work.
   *
   * @covers ::get()
   * @covers ::getService()
   */
  public function test_get_notFoundMultiple() {
    $container = \Mockery::mock('Drupal\service_container\DependencyInjection\Container[getDefinition]', array($this->containerDefinition));
    $container->shouldReceive('getDefinition')
      ->once()
      ->with('service_not_exists', FALSE)
      ->andReturn(NULL);

    $this->assertNull($container->get('service_not_exists', ContainerInterface::NULL_ON_INVALID_REFERENCE, 'Not found service does not throw exception.'));
    $this->assertNull($container->get('service_not_exists', ContainerInterface::NULL_ON_INVALID_REFERENCE, 'Not found service does not throw exception on second call.'));
  }

  /**
   * Tests multiple Container::get() calls with exception on the second time.
   *
   * @covers ::get()
   * @covers ::getService()
   *
   * @expectedException \RuntimeException
   */
  public function test_get_notFoundMulitpleWithExceptionOnSecondCall() {
    $this->assertNull($this->container->get('service_not_exists', ContainerInterface::NULL_ON_INVALID_REFERENCE, 'Not found service does nto throw exception.'));
    $this->container->get('service_not_exists');
  }

  /**
   * Tests that Container::get() for factories via services works properly.
   * @covers ::get()
   * @covers ::getService()
   */
  public function test_get_factoryService() {
    $factory_service = $this->container->get('factory_service');
    $factory_service_class = $this->container->getParameter('factory_service_class');
    $this->assertInstanceOf($factory_service_class, $factory_service);
  }

  /**
   * Tests that Container::get() for factories via factory_class works.
   * @covers ::get()
   * @covers ::getService()
   */
  public function test_get_factoryClass() {
    $service = $this->container->get('service.provider');
    $factory_service= $this->container->get('factory_class');

    $this->assertInstanceOf(get_class($service), $factory_service);
    $this->assertEquals('bar', $factory_service->getSomeParameter(), 'Correct parameter was passed via the factory class instantiation.');
    $this->assertEquals($this->container, $factory_service->getContainer(), 'Container was injected via setter injection.');
  }

  /**
   * Tests that Container::get() for circular dependencies works properly.
   * @expectedException \RuntimeException
   * @covers ::get()
   * @covers ::getService()
   */
  public function test_get_factoryWrong() {
    $this->container->get('wrong_factory');
  }

  /**
   * Returns a mock container definition.
   *
   * @return array
   *   Associated array with parameters and services.
   */
  protected function getMockContainerDefinition() {
    $fake_service = Mockery::mock('alias:Drupal\Tests\service_container\DependencyInjection\FakeService');
    $parameters = array();
    $parameters['some_config'] = 'foo';
    $parameters['some_other_config'] = 'lama';
    $parameters['factory_service_class'] = get_class($fake_service);

    $services = array();
    $services['service_container'] = array(
      'class' => '\Drupal\service_container\DependencyInjection\Container',
    );
    $services['other.service'] = array(
      // @todo Support parameter expansion for classes.
      'class' => get_class($fake_service),
    );
    $services['service.provider'] = array(
      'class' => '\Drupal\Tests\service_container\DependencyInjection\MockService',
      'arguments' => array('@other.service', '%some_config%'),
      'calls' => array(
        array('setContainer', array('@service_container')),
        array('setOtherConfigParameter', array('%some_other_config%')),
       ),
      'priority' => 0,
    );
    $services['factory_service'] = array(
      'class' => '\Drupal\service_container\ServiceContainer\ControllerInterface',
      'factory_method' => 'getFactoryMethod',
      'factory_service' => 'service.provider',
      'arguments' => array('%factory_service_class%'),
    );
    $services['factory_class'] = array(
      'class' => '\Drupal\service_container\ServiceContainer\ControllerInterface',
      'factory_method' => 'getFactoryMethod',
      'factory_class' => '\Drupal\Tests\service_container\DependencyInjection\MockService',
      'arguments' => array(
        '\Drupal\Tests\service_container\DependencyInjection\MockService',
        array(NULL, 'bar'),
      ),
      'calls' => array(
        array('setContainer', array('@service_container')),
      ),
    );
    $services['wrong_factory'] = array(
      'class' => '\Drupal\service_container\ServiceContainer\ControllerInterface',
      'factory_method' => 'getFactoryMethod',
    );
    $services['circular_dependency'] = array(
      'class' => '\Drupal\Tests\service_container\DependencyInjection\MockService',
      'arguments' => array('@circular_dependency'),
    );
    $services['service_parameter_not_exists'] = array(
      'class' => '\Drupal\Tests\service_container\DependencyInjection\MockService',
      'arguments' => array('@service.provider', '%not_exists', -1),
    );
    $services['service_dependency_not_exists'] = array(
      'class' => '\Drupal\Tests\service_container\DependencyInjection\MockService',
      'arguments' => array('@service_not_exists', '%some_config'),
    );

    return array(
      'parameters' => $parameters,
      'services' => $services,
    );
  }
}
