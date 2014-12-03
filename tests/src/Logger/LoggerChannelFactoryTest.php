<?php

/**
 * @file
 * Contains \Drupal\Tests\service_container\Logger\LoggerChannelFactoryTest.
 */

namespace Drupal\Tests\service_container\Logger;

use Drupal\service_container\Logger\LoggerChannelFactory;

/**
 * @coversDefaultClass \Drupal\service_container\Logger\LoggerChannelFactory
 */
class LoggerChannelFactoryTest extends \PHPUnit_Framework_TestCase {

  /**
   * The tested logger channel.
   *
   * @var \Drupal\service_container\Logger\LoggerChannelFactory
   */
  protected $loggerChannelFactory;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->loggerChannelFactory = new LoggerChannelFactory();
  }

  /**
   * @covers ::get()
   */
  public function test_get_noLoggers() {
    $logger_channel1 = $this->loggerChannelFactory->get('test');
    $this->assertInstanceOf('Drupal\service_container\Logger\LoggerChannel', $logger_channel1);

    $logger_channel2 = $this->loggerChannelFactory->get('test');
    $this->assertSame($logger_channel1, $logger_channel2);

    $logger_channel3 = $this->loggerChannelFactory->get('test2');
    $this->assertInstanceOf('Drupal\service_container\Logger\LoggerChannel', $logger_channel3);
    $this->assertNotSame($logger_channel1, $logger_channel3);
  }

  /**
   * @covers ::get()
   * @covers ::addLogger()
   */
  public function test_get_withExistingLoggers() {
    $logger1 = \Mockery::mock('Psr\Log\LoggerInterface');
    $logger2 = \Mockery::mock('Psr\Log\LoggerInterface');

    $this->loggerChannelFactory->addLogger($logger1);
    $this->loggerChannelFactory->addLogger($logger2);

    $logger_channel = $this->loggerChannelFactory->get('test');
    $this->assertAttributeEquals(array(0 => array($logger1, $logger2)), 'loggers', $logger_channel);
  }

  /**
   * @covers ::get()
   * @covers ::addLogger()
   */
  public function test_get_withExistingLoggersWithPriority() {
    $logger1 = \Mockery::mock('Psr\Log\LoggerInterface');
    $logger2 = \Mockery::mock('Psr\Log\LoggerInterface');

    $this->loggerChannelFactory->addLogger($logger1, 0);
    $this->loggerChannelFactory->addLogger($logger2, 10);

    $logger_channel = $this->loggerChannelFactory->get('test');
    $this->assertAttributeEquals(array(0 => array($logger1), 10 => array($logger2)), 'loggers', $logger_channel);
  }

  /**
   * @covers ::addLogger()
   */
  public function test_addLogger_withExistingLoggerChannel() {
    $logger_channel1 = $this->loggerChannelFactory->get('test');
    $logger_channel2 = $this->loggerChannelFactory->get('test2');

    $logger = \Mockery::mock('Psr\Log\LoggerInterface');

    $this->loggerChannelFactory->addLogger($logger);

    $this->assertAttributeEquals(array(0 => array($logger)), 'loggers', $logger_channel1);
    $this->assertAttributeEquals(array(0 => array($logger)), 'loggers', $logger_channel2);
  }

}
