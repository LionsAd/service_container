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
      'group' => 'service_container_symfony',
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    $modules[] = 'test_service_container_symfony';
    parent::setUp($modules);
  }

  /**
   * Tests some basic
   */
  public function testInit() {
    $this->assertTrue(\Drupal::hasService('testservicecontainersymfonyyolo'));
    $this->assertFalse(\Drupal::hasService('TestServiceContainerSymfonyYolo'));
    $this->assertFalse(\Drupal::hasService('testservicecontainersymfonyyolo1'));
  }

}

