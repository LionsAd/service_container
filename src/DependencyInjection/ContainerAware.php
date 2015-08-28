<?php

/**
 * @file
 * Contains \Drupal\service_container\DependencyInjection\ContainerAware
 */

namespace Drupal\service_container\DependencyInjection;

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
  public function setContainer(ContainerInterface $container = null) {
    $this->container = $container;
  }
}
