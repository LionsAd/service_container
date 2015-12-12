<?php

/**
 * @file
 * Contains \Drupal\service_container\DependencyInjection\ContainerAwareInterface
 */

namespace Drupal\service_container\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerInterface as SymfonyContainerInterface;

/**
 * ContainerAwareInterface should be implemented by classes that depend on a Container.
 *
 * @ingroup dic
 */
interface ContainerAwareInterface {

  /**
   * Sets the Container associated with this service.
   *
   * @param SymfonyContainerInterface|null $container
   *   A ContainerInterface instance or NULL to be injected in the service.
   */
  public function setContainer(SymfonyContainerInterface $container = NULL);
}
