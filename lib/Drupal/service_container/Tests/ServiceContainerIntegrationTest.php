<?php

/**
 * @file
 * Contains \Drupal\service_container\Tests\ServiceContainerIntegrationTest.
 */

namespace Drupal\service_container\Tests;

class ServiceContainerIntegrationTest extends \DrupalWebTestCase {

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => 'ServiceContainer',
      'description' => 'Tests the \ServiceContainer class',
      'group' => 'service_container',
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp(array('service_container'));
  }

  /**
   * Tests some basic
   */
  public function testInit() {
    \ServiceContainer::init();

    $this->assertTrue(\Drupal::hasService('service_container'));
    $this->assertTrue(\Drupal::hasService('module_handler'));
    $this->assertTrue(\Drupal::hasService('module_installer'));
  }

}

