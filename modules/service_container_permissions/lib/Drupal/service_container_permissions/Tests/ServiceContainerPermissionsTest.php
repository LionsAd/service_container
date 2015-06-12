<?php

/**
 * @file
 * Contains \Drupal\service_container_permissions\Tests\ServiceContainerPermissionsTest.
 */

namespace Drupal\service_container_permissions\Tests;

use Drupal\service_container\Tests\ServiceContainerIntegrationTestBase;

class ServiceContainerPermissionsTest extends ServiceContainerIntegrationTestBase {

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => 'ServiceContainerPermissionsTest',
      'description' => 'Tests the \ServiceContainerPermissionsPermissions class',
      'group' => 'service_container_permissions',
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    $modules[] = 'test_service_container_permissions';
    parent::setUp($modules);
  }

  /**
   * Tests some basic
   */
  public function testInit() {
    $permissions = array(
      'permission1',
      'permission2',
      'permission3',
      'permission4',
    );

    $this->assertTrue($this->checkPermissions($permissions));
  }

}

