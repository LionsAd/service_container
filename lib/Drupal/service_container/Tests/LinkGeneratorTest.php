<?php

/**
 * @file
 * Contains \Drupal\service_container\Tests\LinkGeneratorTest.
 */

namespace Drupal\service_container\Tests;

class LinkGeneratorTest extends \DrupalWebTestCase {

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
    $result = l('Title', 'test-path');
    $url = url('test-path');
    $this->assertEqual("<a href=\"$url\">Title</a>", $result);
  }

}
