<?php
/**
 * @file
 * Contains \Drupal\service_container\DependencyInjection\ContainerInterface.
 */

namespace Drupal\service_container\DependencyInjection;

use Drupal\Component\Plugin\Discovery\DiscoveryInterface;
use Symfony\Component\DependencyInjection\IntrospectableContainerInterface;

/**
 * Simple DI Container Interface used to get services and discover definitions.
 *
 * @ingroup dic
 */
interface ContainerInterface extends DiscoveryInterface, IntrospectableContainerInterface {

  /**
   * Returns a service from the container.
   *
   * @param string $name
   *   The name of the service to retrieve.
   * @param int $invalidBehavior
   *   The behavior when the service does not exist
   *
   * @return object
   *   Returns the object that provides the service.
   */
  public function get($name,  $invalidBehavior = self::EXCEPTION_ON_INVALID_REFERENCE);

  /**
   * Returns a parameter from the container.
   *
   * @param string $name
   *   The name of the parameter to retrieve.
   *
   * @return mixed
   *   Returns the parameter with the given name.
   */
  public function getParameter($name);

  /**
   * Checks if a parameter exists in the container.
   *
   * @param string $name
   *   The parameter name.
   *
   * @return bool
   *   TRUE if the parameter exists, FALSE otherwise.
   */
  public function hasParameter($name);
}
