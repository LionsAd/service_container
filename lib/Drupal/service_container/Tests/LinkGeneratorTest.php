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
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp(array('service_container'));

    \ServiceContainer::init();
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
