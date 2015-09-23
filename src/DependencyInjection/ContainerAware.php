<?php

/**
 * @file
 * Contains \Drupal\service_container\DependencyInjection\ContainerAware
 */

namespace Drupal\service_container\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerInterface as SymfonyContainerInterface;

/**
 * ContainerAware is a simple implementation of ContainerAwareInterface.
 *
 * @ingroup dic
 */
abstract class ContainerAware implements ContainerAwareInterface {
  /**
   * The injected container.
   *
   * @var ContainerInterface
   */
  protected $container;

  /**
   * {@inheritdoc}
   */
  public function setContainer(SymfonyContainerInterface $container = null) {
    $this->container = $container;
  }
}
