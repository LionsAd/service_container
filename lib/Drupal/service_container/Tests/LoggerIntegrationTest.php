<?php

/**
 * @file
 * Contains \Drupal\service_container\Tests\LoggerIntegrationTest.
 */

namespace Drupal\service_container\Tests;

class LoggerIntegrationTest extends \DrupalWebTestCase {

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
    parent::setUp(array('service_container', 'dblog'));

    \ServiceContainer::init();
    $this->container = \Drupal::getContainer();
  }

  public function testLog() {
    /** @var \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory */
    $logger_factory = $this->container->get('logger.factory');

    /** @var \Drupal\Core\Database\Connection $connection */
    $connection = $this->container->get('database');
    $connection->truncate('watchdog')->execute();

    $logger_factory->get('content')->info('Hello world @key', array('@key' => 'value'));

    $result = $connection->select('watchdog')
      ->fields('watchdog')
      ->execute()
      ->fetchAll();

    $this->assertEqual(1, count($result));
    $this->assertEqual(WATCHDOG_INFO, $result[0]->severity);
    $this->assertEqual('content', $result[0]->type);
    $this->assertEqual('Hello world @key', $result[0]->message);
    $this->assertEqual(array('@key' => 'value'), unserialize($result[0]->variables));
  }

}
