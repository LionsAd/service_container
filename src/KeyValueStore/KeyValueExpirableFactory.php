<?php

/**
 * @file
 * Contains \Drupal\service_container\KeyValueStore\KeyValueExpirableFactory.
 */

namespace Drupal\service_container\KeyValueStore;

use Drupal\Core\KeyValueStore\KeyValueExpirableFactoryInterface;

/**
 * Defines the key/value store factory.
 *
 * @codeCoverageIgnore
 */
class KeyValueExpirableFactory extends KeyValueFactory implements KeyValueExpirableFactoryInterface {

  const DEFAULT_SERVICE = 'keyvalue.expirable.database';

  const SPECIFIC_PREFIX = 'keyvalue_expirable_service_';

  const DEFAULT_SETTING = 'keyvalue_expirable_default';

}

