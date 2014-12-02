<?php

/**
 * @file
 * Contains \Drupal\service_container\Tests\UrlGeneratorTest.
 */

namespace Drupal\service_container\Tests;

class UrlGeneratorTest extends \DrupalWebTestCase {

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => 'UrlGenerator',
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
  public function testUrl() {
    $result = \Drupal::service('url_generator')->url('test-path');
    $this->assertEqual(base_path() . 'test-path', $result);
  }

}
