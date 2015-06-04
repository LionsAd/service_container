<?php

/**
 * @file
 * Contains \Drupal\service_container\Tests\LegacyMessengerTest.
 */

namespace Drupal\service_container\Tests;

use Drupal\service_container\Messenger\LegacyMessenger;

class LegacyMessengerTest extends ServiceContainerIntegrationTestBase {

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => 'LegacyMessenger',
      'description' => 'Tests the messenger integration',
      'group' => 'service_container',
    );
  }

  public function testLegacyMessenger() {
    /** @var \Drupal\service_container\Messenger\LegacyMessenger $messenger_service */

    $messenger_service = $this->container->get('messenger');
    $this->assertTrue($messenger_service instanceof LegacyMessenger);

    $random_message = $this->randomString();
    drupal_set_message($random_message, $messenger_service::WARNING);
    $messages = $messenger_service->getMessages();
    $warning_messages = $messages[$messenger_service::WARNING];
    $this->assertTrue(in_array($random_message, $warning_messages));

    $random_message = $this->randomString();
    $messenger_service->addMessage($random_message, $messenger_service::STATUS);
    $messages = drupal_get_messages();
    $status_messages = $messages[$messenger_service::STATUS];
    $this->assertTrue(in_array($random_message, $status_messages));
  }
}
