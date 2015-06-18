<?php

/**
 * @file
 * Contains \Drupal\service_container\KeyValueStore\KeyValueFactory.
 */

namespace Drupal\service_container\KeyValueStore;

use Drupal\Core\KeyValueStore\KeyValueFactory as BaseKeyValueFactory;
use Drupal\service_container\DependencyInjection\ContainerInterface;

/**
 * Overrides the core KV factory to use the 'service_container' container.
 *
 * @codeCoverageIgnore
 */
class KeyValueFactory extends BaseKeyValueFactory {}
