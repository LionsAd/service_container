<?php

/**
 * @file
 * Contains \Drupal\Tests\service_container\Flood\FloodTest.
 */

namespace Drupal\Tests\service_container\Flood;

use Drupal\Core\Database\Database;
use Drupal\service_container\Flood\DatabaseBackend;
use Drupal\service_container\Legacy\Drupal7;

/**
 * @coversDefaultClass \Drupal\service_container\Flood\DatabaseBackend
 */
class FloodTest extends \PHPUnit_Framework_TestCase {

  /**
   * The tested flood service.
   *
   * @var \Drupal\service_container\Flood\DatabaseBackend
   */
  protected $flood_service;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $drupal7 = new Drupal7();
    $connection = Database::getConnection();

    $this->flood_service = new DatabaseBackend($connection, $drupal7);
  }

  /**
   * @covers ::__construct()
   */
  public function test_construct() {
    $this->assertInstanceOf('\Drupal\service_container\Flood\DatabaseBackend', $this->flood_service);
  }

}
