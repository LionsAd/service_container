<?php

/**
 * @file
 * Contains \Drupal\service_container\Tests\LoggerIntegrationTest.
 */

namespace Drupal\service_container\Tests;

use Drupal\Core\Database\Connection;

class LoggerIntegrationTest extends ServiceContainerIntegrationTestBase {

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => 'Logger',
      'description' => 'Some integration test for the logger.',
      'group' => 'service_container',
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp(array('dblog'));
  }

  public function testLog() {
    /** @var \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory */
    $logger_factory = $this->container->get('logger.factory');

    /** @var \Drupal\Core\Database\Connection $connection */
    $connection = $this->container->get('database');

    // Use both the factory and the logger channel directly.
    $connection->truncate('watchdog')->execute();
    $logger_factory->get('system')->info('Hello world @key', array('@key' => 'value'));
    $this->doTestEntry($connection);

    $connection->truncate('watchdog')->execute();
    $logger_channel = $this->container->get('logger.channel.default');
    $logger_channel->info('Hello world @key', array('@key' => 'value'));
    $this->doTestEntry($connection);
  }

  /**
   * Checks whether the expected logging entry got written.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   The database collection.
   */
  protected function doTestEntry(Connection $connection) {
    $result = $connection->select('watchdog')
      ->fields('watchdog')
      ->execute()
      ->fetchAll();

    $this->assertEqual(1, count($result));
    $this->assertEqual(WATCHDOG_INFO, $result[0]->severity);
    $this->assertEqual('system', $result[0]->type);
    $this->assertEqual('Hello world @key', $result[0]->message);
    $this->assertEqual(array('@key' => 'value'), unserialize($result[0]->variables));
  }

}
