<?php

/**
 * @file
 * Contains \Drupal\service_container\Tests\ServiceContainerIntegrationTestBase.
 */

namespace Drupal\service_container\Tests;

abstract class ServiceContainerIntegrationTestBase extends \DrupalWebTestCase {

  /**
   * The profile to install as a basis for testing.
   *
   * @var string
   */
  protected $profile = 'testing';

  /**
   * The dependency injection container usable in the test.
   *
   * @var \Drupal\service_container\DependencyInjection\ContainerInterface
   */
  protected $container;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    // Support both an array of modules and a single module.
    $modules = func_get_args();
    if (isset($modules[0]) && is_array($modules[0])) {
      $modules = $modules[0];
    }

    $modules[] = 'service_container';

    parent::setUp($modules);

    \ServiceContainer::init();
    $this->container = \Drupal::getContainer();
  }

}
