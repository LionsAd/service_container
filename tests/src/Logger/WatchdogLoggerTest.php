<?php

/**
 * @file
 * Contains \Drupal\Tests\service_container\Logger\WatchdogLoggerTest.
 */

namespace Drupal\Tests\service_container\Logger;
use Drupal\service_container\Legacy\Drupal7;
use Drupal\service_container\Logger\WatchdogLogger;

/**
 * @coversDefaultClass \Drupal\service_container\Logger\WatchdogLogger
 */
class WatchdogLoggerTest extends \PHPUnit_Framework_TestCase {

  /**
   * The tested watchdog logger.
   *
   * @var \Drupal\service_container\Logger\WatchdogLogger
   */
  protected $watchdogLogger;

  /**
   * The Drupal 7 legacy service
   *
   * @var \Drupal\service_container\Legacy\Drupal7
   */
  protected $drupal7;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->drupal7 = \Mockery::mock('\Drupal\service_container\Legacy\Drupal7');
    $this->watchdogLogger = new WatchdogLogger($this->drupal7);
  }

  /**
   * @covers ::__construct()
   */
  public function test_construct() {
    $this->assertInstanceOf('\Drupal\service_container\Logger\WatchdogLogger', $this->watchdogLogger);
  }

}
