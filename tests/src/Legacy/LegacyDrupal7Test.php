<?php

/**
 * @file
 * Contains \Drupal\Tests\service_container\Legacy\LegacyDrupal7Test.
 */

namespace Drupal\Tests\service_container\Legacy {
  use Drupal\service_container\Legacy\Drupal7;

  /**
   * @coversDefaultClass \Drupal\service_container\Legacy\Drupal7
   */
  class LegacyDrupal7Test extends \PHPUnit_Framework_TestCase {

    /**
     * The tested Drupal7 service.
     *
     * @var \Drupal\service_container\Legacy\Drupal7
     */
    protected $drupal7_service;

    /**
     * {@inheritdoc}
     */
    protected function setUp() {
      parent::setUp();

      $this->drupal7_service = new Drupal7();
    }

    /**
     * @covers ::__call()
     */
    public function test_call() {
      $random_string = 'is_a_lovely_cat!';
      $this->assertEquals('izumi_is_a_lovely_cat!', $this->drupal7_service->drupal7_legacy_test_function($random_string), 'Calling function via legacy service works.');
    }
  }
}

namespace {
  if (!function_exists('drupal7_legacy_test_function')) {
    /**
     * Test function to test legacy service.
     *
     * @param string $var
     *   Test string to append.
     * @return string
     *   Returns a fixed string appended with $var.
     */
    function drupal7_legacy_test_function($var) {
      return 'izumi_' . $var;
    }
  }
}
