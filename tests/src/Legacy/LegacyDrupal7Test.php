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
      $this->assertEquals('llama', $this->drupal7_service->drupal7_legacy_test_function(), 'Calling function via legacy service works.');
    }
  }
}

namespace {
  if (!function_exists('drupal7_legacy_test_function')) {
    function drupal7_legacy_test_function() { return 'llama'; }
  }
}
