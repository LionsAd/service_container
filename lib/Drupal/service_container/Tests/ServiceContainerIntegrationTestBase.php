<?php

/**
 * @file
 * Contains \Drupal\service_container\Tests\ServiceContainerIntegrationTestBase.
 */

namespace Drupal\service_container\Tests;

abstract class ServiceContainerIntegrationTestBase extends \DrupalWebTestCase {

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp(array('service_container'));

    \ServiceContainer::init();
    $this->container = \Drupal::getContainer();
  }

}
