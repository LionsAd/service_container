<?php

/**
 * @file
 * Contains \Drupal\Tests\service_container\Logger\LoggerChannelTest.
 */

namespace Drupal\Tests\service_container\Logger;

use Drupal\service_container\Logger\LoggerChannel;
use Psr\Log\LogLevel;

/**
 * @coversDefaultClass \Drupal\service_container\Logger\LoggerChannel
 */
class LoggerChannelTest extends \PHPUnit_Framework_TestCase {

  /**
   * The tested logger channel.
   *
   * @var \Drupal\service_container\Logger\LoggerChannel
   */
  protected $loggerChannel;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->loggerChannel = new LoggerChannel('test');
  }

  /**
   * @covers ::__construct()
   */
  public function test_construct() {
    $this->assertInstanceOf('\Drupal\service_container\Logger\LoggerChannel', $this->loggerChannel);
    $this->assertAttributeEquals('test', 'channel', $this->loggerChannel);
  }

  /**
   * @covers ::log()
   * @covers ::addLogger()
   *
   * @dataProvider providerLogLevels
   */
  public function test_log_singleLogger($log_method = NULL, $log_level = LogLevel::INFO) {
    $logger = \Mockery::mock('Psr\Log\LoggerInterface');
    $logger->shouldReceive('log')
      // Ensure that the channel is passed along.
      ->with($log_level, 'test-message', array('key' => 'value', 'channel' => 'test'));

    $this->loggerChannel->addLogger($logger);

    if (is_null($log_method)) {
      $this->loggerChannel->log($log_level, 'test-message', array('key' => 'value'));
    }
    else {

      $this->loggerChannel->{$log_method}('test-message', array('key' => 'value'));
    }
  }

  /**
   * @covers ::log()
   * @covers ::setLoggers()
   */
  public function test_setLoggers() {
    $logger1 = \Mockery::mock('Psr\Log\LoggerInterface');
    $logger2 = \Mockery::mock('Psr\Log\LoggerInterface');

    // Note: We use globally()/ordered() in order to ensure that $logger2 is
    // called first.
    $logger2->shouldReceive('log')
      // Ensure that the channel is passed along.
      ->globally()
      ->ordered()
      ->with(LogLevel::INFO, 'test-message', array('key' => 'value', 'channel' => 'test'));

    $logger1->shouldReceive('log')
      // Ensure that the channel is passed along.
      ->globally()
      ->ordered()
      ->with(LogLevel::INFO, 'test-message', array('key' => 'value', 'channel' => 'test'));


    $this->loggerChannel->setLoggers(array(0 => array($logger1), 10 => array($logger2)));

    $this->loggerChannel->log(LogLevel::INFO, 'test-message', array('key' => 'value'));
  }

  /**
   * @covers ::log()
   * @covers ::sortLoggers()
   * @covers ::addLogger()
   *
   * @covers ::emergency()
   * @covers ::alert()
   * @covers ::critical()
   * @covers ::error()
   * @covers ::warning()
   * @covers ::notice()
   * @covers ::info()
   * @covers ::debug()
   *
   * @dataProvider providerLogLevels
   */
  public function test_sortLogger($log_method = NULL, $log_level = LogLevel::INFO) {
    $logger1 = \Mockery::mock('Psr\Log\LoggerInterface');
    $logger2 = \Mockery::mock('Psr\Log\LoggerInterface');

    // Note: We use globally()/ordered() in order to ensure that $logger2 is
    // called first.
    $logger2->shouldReceive('log')
      // Ensure that the channel is passed along.
      ->globally()
      ->ordered()
      ->with($log_level, 'test-message', array('key' => 'value', 'channel' => 'test'));

    $logger1->shouldReceive('log')
      // Ensure that the channel is passed along.
      ->globally()
      ->ordered()
      ->with($log_level, 'test-message', array('key' => 'value', 'channel' => 'test'));


    $this->loggerChannel->addLogger($logger1, 0);
    $this->loggerChannel->addLogger($logger2, 10);

    if (is_null($log_method)) {
      $this->loggerChannel->log($log_level, 'test-message', array('key' => 'value'));
    }
    else {

      $this->loggerChannel->{$log_method}('test-message', array('key' => 'value'));
    }
  }

  public function providerLogLevels() {
    $data = array();

    $data[] = array('emergency', LogLevel::EMERGENCY);
    $data[] = array('alert', LogLevel::ALERT);
    $data[] = array('critical', LogLevel::CRITICAL);
    $data[] = array('error', LogLevel::ERROR);
    $data[] = array('warning', LogLevel::WARNING);
    $data[] = array('notice', LogLevel::NOTICE);
    $data[] = array('info', LogLevel::INFO);
    $data[] = array('debug', LogLevel::DEBUG);

    return $data;
  }

  /**
   * @covers ::setRequestStack()
   */
  public function test_setRequestStack() {
    $request_stack = \Mockery::mock('alias:Symfony\Component\HttpFoundation\RequestStack');
    $this->loggerChannel->setRequestStack($request_stack);
    $this->assertAttributeEquals($request_stack, 'requestStack', $this->loggerChannel);
  }

  /**
   * @covers ::setCurrentUser()
   */
  public function test_setCurrentUser() {
    $current_user = \Mockery::mock('alias:Drupal\Core\Session\AccountInterface');
    $this->loggerChannel->setCurrentUser($current_user);
    $this->assertAttributeEquals($current_user, 'currentUser', $this->loggerChannel);
  }

}

