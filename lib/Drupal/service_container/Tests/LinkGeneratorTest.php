<?php

/**
 * @file
 * Contains \Drupal\service_container\Tests\LinkGeneratorTest.
 */

namespace Drupal\service_container\Tests;

class LinkGeneratorTest extends ServiceContainerIntegrationTestBase {

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => 'LinkGenerator',
      'description' => '',
      'group' => 'service_container',
    );
  }

  /**
   * Adds some really basic integration test.
   */
  public function testL() {
    $result = \Drupal::service('link_generator')->l('Title', 'test-path');
    $url = url('test-path');
    $this->assertEqual("<a href=\"$url\">Title</a>", $result);
  }

}
