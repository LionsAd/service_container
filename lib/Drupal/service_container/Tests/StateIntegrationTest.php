<?php

/**
 * @file
 * Contains \Drupal\service_container\Tests\StateIntegrationTest.
 */

namespace Drupal\service_container\Tests;

class StateIntegrationTest extends \DrupalWebTestCase {

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => 'State',
      'description' => 'Tests the state system',
      'group' => 'service_container',
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp(array('service_container'));

    \ServiceContainer::init();
  }

  public function testState() {
    /** @var \Drupal\Core\State\StateInterface $state */
    $state = \Drupal::service('state');
    $this->assertEqual(NULL, $state->get('not-existing'));
    $this->assertEqual('default', $state->get('not-existing', 'default'));

    $state->set('key', 'value');
    $this->assertEqual('value', $state->get('key'));

    $state->setMultiple(array('key1' => 'value1', 'key2' => 'value2'));
    $this->assertEqual(array('key1' => 'value1', 'key2' => 'value2'), $state->getMultiple(array('key1', 'key2')));

    $state->delete('key1');
    $this->assertEqual(array('key2' => 'value2'), $state->getMultiple(array('key1', 'key2')));

    $state->deleteMultiple(array('key2'));
    $this->assertEqual(array(), $state->getMultiple(array('key1', 'key2')));
  }

}
