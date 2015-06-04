<?php

/**
 * @file
 * Contains \Drupal\service_container\Tests\LegacyDrupal7.
 */

namespace Drupal\service_container\Tests;

use Drupal\service_container\Legacy\Drupal7;

class LegacyDrupal7 extends ServiceContainerIntegrationTestBase {

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => 'LegacyDrupal7',
      'description' => 'Tests the legacy Drupal 7 integration',
      'group' => 'service_container',
    );
  }

  public function testLegacyDrupal7() {
    /** @var \Drupal\service_container\Legacy\Drupal7 $drupal7_service */

    $drupal7_service = $this->container->get('messenger');
    $this->assertTrue($drupal7_service instanceof Drupal7);

    $random_message = $this->randomString();
    drupal_set_message($random_message, 'warning');
    $messages = $drupal7_service->drupal_get_messages();
    $warning_messages = $messages['warning'];
    $this->assertTrue(in_array($random_message, $warning_messages));
  }
}
