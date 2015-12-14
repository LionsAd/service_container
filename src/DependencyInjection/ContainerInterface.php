<?php
/**
 * @file
 * Contains \Drupal\service_container\DependencyInjection\ContainerInterface.
 */

namespace Drupal\service_container\DependencyInjection;

use Drupal\Component\Plugin\Discovery\DiscoveryInterface;
use Symfony\Component\DependencyInjection\IntrospectableContainerInterface;

/**
 * Simple DI Container Interface used to get services and discover definitions.
 *
 * @ingroup dic
 */
interface ContainerInterface extends DiscoveryInterface, IntrospectableContainerInterface {
}
