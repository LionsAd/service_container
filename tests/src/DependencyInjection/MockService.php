<?php

/**
 * @file
 * Contains \Drupal\Tests\service_container\DependencyInjection\MockService
 */

namespace Drupal\Tests\service_container\DependencyInjection;

use ReflectionClass;
use Drupal\service_container\DependencyInjection\Container;
use Drupal\service_container\DependencyInjection\ContainerInterface;

/**
 * Helper class to test Container::get() method.
 *
 * @group dic
 */
class MockService {

  /**
   * @var ContainerInterface
   */
  protected $container;

  /**
   * @var object
   */
  protected $someOtherService;

  /**
   * @var string
   */
  protected $someParameter;

  /**
   * @var string
   */
  protected $someOtherParameter;

  /**
   * Constructs a MockService object.
   *
   * @param object $some_other_service
   *   (optional) Another injected service.
   * @param string $some_parameter
   *   (optional) An injected parameter.
   */
  public function __construct($some_other_service = NULL, $some_parameter = NULL) {
    if (is_array($some_other_service)) {
      $some_other_service = $some_other_service[0];
    }
    $this->someOtherService = $some_other_service;
    $this->someParameter = $some_parameter;
  }

  /**
   * Sets the container object.
   *
   * @param ContainerInterface $container
   *   The container to inject via setter injection.
   */
  public function setContainer(ContainerInterface $container) {
    $this->container = $container;
  }

  /**
   * Gets the container object.
   *
   * @return ContainerInterface
   *   The internally set container.
   */
  public function getContainer() {
    return $this->container;
  }

  /**
   * Gets the someOtherService object.
   *
   * @return object
   *   The injected service.
   */
  public function getSomeOtherService() {
    return $this->someOtherService;
  }

  /**
   * Gets the someParameter property.
   *
   * @return string
   *   The injected parameter.
   */
  public function getSomeParameter() {
    return $this->someParameter;
  }

  /**
   * Sets the someOtherParameter property.
   *
   * @param string $some_other_parameter
   *   The setter injected parameter.
   */
  public function setOtherConfigParameter($some_other_parameter) {
    $this->someOtherParameter = $some_other_parameter;
  }

  /**
   * Gets the someOtherParameter property.
   *
   * @return string
   *   The injected parameter.
   */
  public function getSomeOtherParameter() {
    return $this->someOtherParameter;
  }

  /**
   * Provides a factory method to get a service.
   *
   * @param string $class
   *   The class name of the class to instantiate
   * @param array $arguments
   *   (optional) Arguments to pass to the new class.
   *
   * @return object
   *   The instantiated service object.
   */
  public static function getFactoryMethod($class, $arguments = array()) {
    $r = new ReflectionClass($class);
    $service = ($r->getConstructor() === NULL) ? $r->newInstance() : $r->newInstanceArgs($arguments);

    return $service;
  }

}
