<?php

/**
 * @file
 * Contains Drupal\service_container\Tests\KeyValue\StorageTestBase.
 */

namespace Drupal\service_container\Tests\KeyValue;

use Drupal\service_container\Tests\ServiceContainerIntegrationTestBase;

/**
 * Base class for testing key-value storages.
 */
class StorageTestBase extends ServiceContainerIntegrationTestBase {

  /**
   * The profile to install as a basis for testing.
   *
   * @var string
   */
  protected $profile = 'testing';

  /**
   * An array of random stdClass objects.
   *
   * @var array
   */
  protected $objects = array();

  /**
   * An array of data collection labels.
   *
   * @var array
   */
  protected $collections = array();

  /**
   * Whether we are using an expirable key/value store.
   *
   * @var boolean
   */
  protected $factory = 'keyvalue';

  protected function setUp() {
    $args = func_get_args();
    parent::setUp($args);

    // Define two data collections,
    $this->collections = array(0 => 'zero', 1 => 'one');

    // Create several objects for testing.
    for ($i = 0; $i <= 5; $i++) {
      $this->objects[$i] = (object) array(
        'key1' => $this->randomString(),
        'key2' => $this->randomString(),
      );
    }
  }

  protected function cleanUp() {
    foreach ($this->createStorage() as $storage) {
      $storage->deleteAll();
    }
  }

  public function testKeyValueStore() {
    $this->_testCrud();
    $this->cleanUp();
    $this->_testNonExistingKeys();
    $this->cleanUp();
    $this->_testSetIfNotExists();
    $this->cleanUp();
    $this->_testRename();
    $this->cleanUp();
  }

    /**
   * Checks to see if two objects are identical.
   *
   * @param object $object1
   *   The first object to check.
   * @param object $object2
   *   The second object to check.
   * @param $message
   *   (optional) A message to display with the assertion. Do not translate
   *   messages: use \Drupal\Component\Utility\String::format() to embed
   *   variables in the message text, not t(). If left blank, a default message
   *   will be displayed.
   * @param $group
   *   (optional) The group this message is in, which is displayed in a column
   *   in test output. Use 'Debug' to indicate this is debugging output. Do not
   *   translate this string. Defaults to 'Other'; most tests do not override
   *   this default.
   *
   * @return
   *   TRUE if the assertion succeeded, FALSE otherwise.
   */
  protected function assertIdenticalObject($object1, $object2, $message = '', $group = 'Other') {
    $message = $message ?: format_string('!object1 is identical to !object2', array(
      '!object1' => var_export($object1, TRUE),
      '!object2' => var_export($object2, TRUE),
    ));
    $identical = TRUE;
    foreach ($object1 as $key => $value) {
      $identical = $identical && isset($object2->$key) && $object2->$key === $value;
    }
    return $this->assertTrue($identical, $message, $group);
  }

  /**
   * Tests CRUD operations.
   */
  public function _testCRUD() {
    $stores = $this->createStorage();
    // Verify that each store returns its own collection name.
    $this->assertIdentical($stores[0]->getCollectionName(), $this->collections[0]);
    $this->assertIdentical($stores[1]->getCollectionName(), $this->collections[1]);

    // Verify that an item can be stored.
    $stores[0]->set('foo', $this->objects[0]);
    $this->assertTrue($stores[0]->has('foo'));
    $this->assertIdenticalObject($this->objects[0], $stores[0]->get('foo'));
    // Verify that the other collection is not affected.
    $this->assertFalse($stores[1]->has('foo'));
    $this->assertFalse($stores[1]->get('foo'));

    // Verify that an item can be updated.
    $stores[0]->set('foo', $this->objects[1]);
    $this->assertIdenticalObject($this->objects[1], $stores[0]->get('foo'));
    // Verify that the other collection is still not affected.
    $this->assertFalse($stores[1]->get('foo'));

    // Verify that a collection/name pair is unique.
    $stores[1]->set('foo', $this->objects[2]);
    $this->assertIdenticalObject($this->objects[1], $stores[0]->get('foo'));
    $this->assertIdenticalObject($this->objects[2], $stores[1]->get('foo'));

    // Verify that an item can be deleted.
    $stores[0]->delete('foo');
    $this->assertFalse($stores[0]->has('foo'));
    $this->assertFalse($stores[0]->get('foo'));

    // Verify that the other collection is not affected.
    $this->assertTrue($stores[1]->has('foo'));
    $this->assertIdenticalObject($this->objects[2], $stores[1]->get('foo'));
    $stores[1]->delete('foo');
    $this->assertFalse($stores[1]->get('foo'));

    // Verify that multiple items can be stored.
    $values = array(
      'foo' => $this->objects[3],
      'bar' => $this->objects[4],
    );
    $stores[0]->setMultiple($values);

    // Verify that multiple items can be retrieved.
    $result = $stores[0]->getMultiple(array('foo', 'bar'));
    foreach ($values as $j => $value) {
      $this->assertIdenticalObject($value, $result[$j]);
    }

    // Verify that the other collection was not affected.
    $this->assertFalse($stores[1]->get('foo'));
    $this->assertFalse($stores[1]->get('bar'));

    // Verify that all items in a collection can be retrieved.
    // Ensure that an item with the same name exists in the other collection.
    $stores[1]->set('foo', $this->objects[5]);
    $result = $stores[0]->getAll();
    // Not using assertIdentical(), since the order is not defined for getAll().
    $this->assertEqual(count($result), count($values));
    foreach ($result as $key => $value) {
      $this->assertEqual($values[$key], $value);
    }
    // Verify that all items in the other collection are different.
    $result = $stores[1]->getAll();
    $this->assertEqual($result, array('foo' => $this->objects[5]));

    // Verify that multiple items can be deleted.
    $stores[0]->deleteMultiple(array_keys($values));
    $this->assertFalse($stores[0]->get('foo'));
    $this->assertFalse($stores[0]->get('bar'));
    $this->assertFalse($stores[0]->getMultiple(array('foo', 'bar')));
    // Verify that deleting no items does not cause an error.
    $stores[0]->deleteMultiple(array());
    // Verify that the item in the other collection still exists.
    $this->assertIdenticalObject($this->objects[5], $stores[1]->get('foo'));

    $stores[0]->deleteAll();
  }

  /**
   * Tests expected behavior for non-existing keys.
   */
  public function _testNonExistingKeys() {

    $stores = $this->createStorage();

    // Verify that a non-existing key returns NULL as value.
    $this->assertNull($stores[0]->get('foo'));

    // Verify that a non-existing key with a default returns the default.
    $this->assertIdentical($stores[0]->get('foo', 'bar'), 'bar');

    // Verify that a FALSE value can be stored.
    $stores[0]->set('foo', FALSE);
    $this->assertIdentical($stores[0]->get('foo'), FALSE);

    // Verify that a deleted key returns NULL as value.
    $stores[0]->delete('foo');
    $this->assertNull($stores[0]->get('foo'));

    // Verify that a non-existing key is not returned when getting multiple keys.
    $stores[0]->set('bar', 'baz');
    $values = $stores[0]->getMultiple(array('foo', 'bar'));
    $this->assertFalse(isset($values['foo']), "Key 'foo' not found.");
    $this->assertIdentical($values['bar'], 'baz');
  }

  /**
   * Tests the setIfNotExists() method.
   */
  public function _testSetIfNotExists() {
    $stores = $this->createStorage();

    $key = $this->randomName();
    // Test that setIfNotExists() succeeds only the first time.
    for ($i = 0; $i <= 1; $i++) {
      // setIfNotExists() should be TRUE the first time (when $i is 0) and
      // FALSE the second time (when $i is 1).
      $this->assertEqual(!$i, $stores[0]->setIfNotExists($key, $this->objects[$i]));
      $this->assertIdenticalObject($this->objects[0], $stores[0]->get($key));
      // Verify that the other collection is not affected.
      $this->assertFalse($stores[1]->get($key));
    }

    // Remove the item and try to set it again.
    $stores[0]->delete($key);
    $stores[0]->setIfNotExists($key, $this->objects[1]);
    // This time it should succeed.
    $this->assertIdenticalObject($this->objects[1], $stores[0]->get($key));
    // Verify that the other collection is still not affected.
    $this->assertFalse($stores[1]->get($key));
  }

  /**
   * Tests the rename operation.
   */
  public function _testRename() {
    $stores = $this->createStorage();
    $store = $stores[0];

    $store->set('old', 'thing');
    $this->assertIdentical($store->get('old'), 'thing');
    $store->rename('old', 'new');
    $this->assertIdentical($store->get('new'), 'thing');
    $this->assertNull($store->get('old'));
  }

  /**
   * Creates storage objects for each collection defined for this class.
   *
   * Storing the storage objects in a class member variable causes a fatal
   * exception in DatabaseStorageExpirableTest, because in that situation
   * garbage collection is not triggered until the test class itself is
   * destructed, after tearDown() has deleted the database tables. Instead,
   * create the storage objects locally in each test using this method.
   *
   * @see \Drupal\system\Tests\KeyValueStore\DatabaseStorageExpirable
   * @see \Drupal\Core\KeyValueStore\DatabaseStorageExpirable::garbageCollection()
   */
  protected function createStorage() {
    $stores = array();
    foreach ($this->collections as $i => $collection) {
      $stores[$i] = \Drupal::service($this->factory)->get($collection);
    }

    return $stores;
  }

}
