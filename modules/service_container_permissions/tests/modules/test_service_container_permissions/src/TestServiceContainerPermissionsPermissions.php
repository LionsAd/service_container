<?php

namespace Drupal\test_service_container_permissions;

class TestServiceContainerPermissionsPermissions {
  public function permissions() {
    return array(
      'permission3' => array(
        'title' => 'permission3',
        'description' => 'permission4'
      ),
      'permission4' => array(
        'title' => 'permission4',
        'description' => 'permission4'
      )
    );
  }
}
