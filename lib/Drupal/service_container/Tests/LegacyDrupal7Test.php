<?php

/**
 * @file
 * Contains \Drupal\service_container\Tests\LegacyDrupal7Test.
 */

namespace Drupal\service_container\Tests;

use Drupal\service_container\Legacy\Drupal7;

class LegacyDrupal7Test extends ServiceContainerIntegrationTestBase {

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => 'LegacyDrupal7Test',
      'description' => 'Tests the legacy Drupal 7 integration service',
      'group' => 'service_container',
    );
  }

  public function testLegacyDrupal7() {
    /** @var \Drupal\service_container\Legacy\Drupal7 $drupal7_service */

    $drupal7_service = $this->container->get('drupal7');
    $this->assertTrue($drupal7_service instanceof Drupal7);

    $random_message = $this->randomString();
    $drupal7_service->drupal_set_message($random_message, 'warning');
    $messages = drupal_get_messages();
    $this->assertTrue(in_array($random_message, $messages['warning']));
  }
}
