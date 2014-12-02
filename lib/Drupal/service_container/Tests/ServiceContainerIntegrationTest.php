<?php

/**
 * @file
 * Contains \Drupal\service_container\Tests\ServiceContainerIntegrationTest.
 */

namespace Drupal\service_container\Tests;

class ServiceContainerIntegrationTest extends ServiceContainerIntegrationTestBase {

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
   * Tests some basic
   */
  public function testInit() {
    $this->assertTrue(\Drupal::hasService('service_container'));
    $this->assertTrue(\Drupal::hasService('module_handler'));
    $this->assertTrue(\Drupal::hasService('module_installer'));
  }

}

