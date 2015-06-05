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
  protected $drupal7;

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

    // We can't use the mock object here because LegacyMessenger needs a
    // \Drupal\service_container\Legacy\Drupal7 argument.
    // Do we need to create an interface for it instead ?
    $this->drupal7 = new Drupal7();
    $this->messenger_service = new LegacyMessenger($this->drupal7);
  }

  /**
   * @covers ::__construct()
   */
  public function test_construct() {
    $this->assertInstanceOf('\Drupal\service_container\Messenger\MessengerInterface', $this->messenger_service);
    $this->assertInstanceOf('\Drupal\service_container\Legacy\Drupal7', $this->drupal7);
  }

  /**
   * @covers ::addMessage()
   */
  public function test_addMessage() {
    $drupal7 = \Mockery::mock('Drupal\service_container\Legacy\Drupal7');
    $message = 'Izumi';
    $type = 'warning';

    $drupal7
      ->shouldReceive('drupal_set_message')
      ->once()
      ->with($message, $type, FALSE);

    $this->messenger_service->addMessage($message, $type);
  }

  /**
   * @covers ::getMessages()
   */
  public function test_getMessages() {
    $drupal7 = \Mockery::mock('Drupal\service_container\Legacy\Drupal7');

    $drupal7
      ->shouldReceive('drupal_get_messages')
      ->once()
      ->with(NULL, FALSE);

    $this->messenger_service->getMessages();
  }

  /**
   * @covers ::getMessagesByType()
   */
  public function test_getMessagesByType() {
    $drupal7 = \Mockery::mock('Drupal\service_container\Legacy\Drupal7');

    $drupal7
      ->shouldReceive('drupal_get_messages')
      ->once()
      ->with('warning', FALSE);

    $this->messenger_service->getMessages('warning');
  }

  /**
   * @covers ::deleteMessages()
   */
  public function test_deleteMessages() {
    $drupal7 = \Mockery::mock('Drupal\service_container\Legacy\Drupal7');

    $drupal7
      ->shouldReceive('drupal_get_messages')
      ->once()
      ->with(NULL, TRUE);

    $this->messenger_service->deleteMessages();
  }

  /**
   * @covers ::deleteMessagesByType()
   */
  public function test_deleteMessagesByType() {
    $drupal7 = \Mockery::mock('Drupal\service_container\Legacy\Drupal7');

    $drupal7
      ->shouldReceive('drupal_get_messages')
      ->once()
      ->with('warning', TRUE);

    $this->messenger_service->deleteMessagesByType('warning');
  }

}
