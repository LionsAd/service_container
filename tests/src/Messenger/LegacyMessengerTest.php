<?php

/**
 * @file
 * Contains \Drupal\Tests\service_container\Messenger\LegacyMessengerTest.
 */

namespace Drupal\Tests\service_container\Messenger;
use Drupal\service_container\Messenger\LegacyMessenger;
use Drupal\service_container\Legacy\Drupal7;

/**
 * @coversDefaultClass \Drupal\service_container\Messenger\LegacyMessenger
 */
class LegacyMessengerTest extends \PHPUnit_Framework_TestCase {

  /**
   * The Drupal7 service.
   *
   * @var \Drupal\service_container\Legacy\Drupal7
   */
  protected $drupal7_service;

  /**
   * The Messenger service.
   *
   * @var \Drupal\service_container\Messenger\LegacyMessenger
   */
  protected $messenger_service;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->drupal7_service = new Drupal7();
    $this->messenger_service = new LegacyMessenger($this->drupal7_service);
  }

  /**
   * @covers ::__construct()
   */
  public function test_construct() {
    $this->assertInstanceOf('\Drupal\service_container\Messenger\LegacyMessenger', $this->messenger_service);
    $this->assertInstanceOf('\Drupal\service_container\Legacy\Drupal7', $this->drupal7_service);
  }

  /**
   * @covers ::addMessage()
   */
  public function test_addMessage() {}

  /**
   * @covers ::getMessages()
   */
  public function test_getMessages() {}

  /**
   * @covers ::getMessagesByType()
   */
  public function test_getMessagesByType() {}

  /**
   * @covers ::deleteMessages()
   */
  public function test_deleteMessages() {}

  /**
   * @covers ::deleteMessagesByType()
   */
  public function test_deleteMessagesByType() {}

}
