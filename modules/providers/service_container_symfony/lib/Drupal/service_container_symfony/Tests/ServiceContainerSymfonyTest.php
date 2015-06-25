<?php

/**
 * @file
 * Contains \Drupal\service_container_symfony\Tests\ServiceContainerSymfonyTest.
 */

namespace Drupal\service_container_symfony\Tests;

use Drupal\service_container\Tests\ServiceContainerIntegrationTestBase;

class ServiceContainerSymfonyTest extends ServiceContainerIntegrationTestBase {

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => 'ServiceContainerSymfonyTest',
      'description' => 'Tests the \ServiceContainerSymfony class',
      'group' => 'service_container',
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    $modules[] = 'service_container_symfony_test';
    $modules[] = 'service_container_symfony_subtest';
    parent::setUp($modules);
 }

  /**
   * Tests some basic
   */
  public function testInit() {
    $this->assertTrue(\Drupal::hasService('service_container_symfony_test_yolo'));
    $this->assertFalse(\Drupal::hasService('service_container_symfony_test_yolo1'));
  }

  /**
   * Tests with multiple modules enabled.
   */
  public function testMultiple() {
    $this->assertTrue(\Drupal::hasService('service_container_symfony_test_yolo'));
    $this->assertFalse(\Drupal::hasService('service_container_symfony_test_yolo1'));
    $this->assertTrue(\Drupal::hasService('service_container_symfony_subtest_yolo'));
    $this->assertFalse(\Drupal::hasService('service_container_symfony_subtest_yolo1'));
  }
}
