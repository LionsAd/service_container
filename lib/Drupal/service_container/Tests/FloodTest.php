<?php

/**
 * @file
 * Contains \Drupal\service_container\Tests\FloodTest.
 */

namespace Drupal\service_container\Tests;

class FloodTest extends ServiceContainerIntegrationTestBase {

  /**
   * A random name for an event.
   *
   * @var string
   */
  protected $eventName;

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => 'Flood control mechanism',
      'description' => 'Functional tests for the flood control mechanism',
      'group' => 'service_container',
    );
  }

  /**
   * The the flood mechanisms.
   */
  public function testFlood() {
    $threshold = 1;
    $window_expired = -1;
    $name = 'flood_test';

    // Register expired event.
    \Drupal::service('flood')->register($name, $window_expired);
    $this->assertFalse(\Drupal::service('flood')->isAllowed($name, $threshold));
    $this->cronRun();
    $this->assertTrue(\Drupal::service('flood')->isAllowed($name, $threshold));

    // Register unexpired event.
    \Drupal::service('flood')->register($name);
    $this->assertFalse(\Drupal::service('flood')->isAllowed($name, $threshold));
    $this->cronRun();
    $this->assertFalse(\Drupal::service('flood')->isAllowed($name, $threshold));
  }

}
