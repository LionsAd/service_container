<?php

/**
 * @file
 * Contains \Drupal\Tests\service_container\Flood\FloodTest.
 */

namespace Drupal\Tests\service_container\Flood;
use Drupal\Core\Database\Connection;
use Drupal\service_container\Flood\DatabaseBackend;


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
  }




}

