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

    $this->drupal7 = \Mockery::mock('\Drupal\service_container\Legacy\Drupal7');
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
   * @dataProvider addMessageDataProvider
   */
  public function test_addMessage($type, $message, $repeat) {
    $this->drupal7
      ->shouldReceive('drupal_set_message')
      ->once()
      ->with($message, $type, $repeat);

    $this->assertSame($this->messenger_service, $this->messenger_service->addMessage($message, $type, $repeat));
  }

  /**
   * @covers ::getMessages()
   * @dataProvider getMessagesDataProvider
   */
  public function test_getMessages($type, $expected) {
    $this->drupal7
      ->shouldReceive('drupal_get_messages')
      ->once()
      ->with(NULL, FALSE)
      ->andReturn($expected);

    $this->assertEquals($expected, $this->messenger_service->getMessages());
  }
   /**
   * @covers ::getMessagesByType()
   * @dataProvider getMessagesDataProvider
   */
  public function test_getMessagesByType($type, $expected) {
    $this->drupal7
      ->shouldReceive('drupal_get_messages')
      ->once()
      ->with($type, FALSE)
      ->andReturn($expected);

    $result = isset($expected[$type]) ? $expected[$type] : array();
    $this->assertEquals($result, $this->messenger_service->getMessagesByType($type));
  }

  /**
   * Data Provider for addMessage().
   */
  public function addMessageDataProvider() {
    return array(
      array('status', 'Hello Status!', FALSE),
      array('status', 'Hello Status!', TRUE),
      array('warning', 'Hello World!', FALSE),
      array('warning', 'Hello World!', TRUE),
      array('error', 'Hello Error!', FALSE),
      array('error', 'Hello Error!', TRUE),
    );
  }

  /**
   * Data Provider for getMessages() and getMessagesByType().
   */
  public function getMessagesDataProvider() {
    return array(
      array('status', array()),
      array('status', array('status' => array('Hello Status!'))),
      array('warning', array('warning' => array('Hello World!'))),
      array('error', array('error' => array('Hello Error!'))),
    );
  }


  /**
   * @covers ::deleteMessages()
   */
  public function test_deleteMessages() {
    $this->drupal7
      ->shouldReceive('drupal_get_messages')
      ->once()
      ->with(NULL, TRUE);

    $this->assertSame($this->messenger_service, $this->messenger_service->deleteMessages());
  }

  /**
   * @covers ::deleteMessagesByType()
   */
  public function test_deleteMessagesByType() {
    $this->drupal7
      ->shouldReceive('drupal_get_messages')
      ->once()
      ->with('warning', TRUE);

    $this->assertSame($this->messenger_service, $this->messenger_service->deleteMessagesByType('warning'));
  }

}
