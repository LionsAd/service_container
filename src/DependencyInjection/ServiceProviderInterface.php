<?php

/**
 * @file
 * Contains \Drupal\service_container\DependencyInjection\ServiceProviderInterface
 */
namespace Drupal\service_container\DependencyInjection;

/**
 * Defines render cache service provider objects.
 *
 * Those can be used for creating a service container definition.
 */
interface ServiceProviderInterface {

  /**
   * Gets a service container definition.
   *
   * @return array
   *   Returns an associative array with the following keys:
   *     - parameters: Simple key-value store of container parameters
   *     - services: Services like defined in services.yml
   *   factory methods, arguments and tags are supported for services.
   *
   *   @see core.services.yml in Drupal 8
   */
  public function getContainerDefinition();

  /**
   * Allows to alter the container definition.
   *
   * @param array $container_definition
   *   An associative array with the following keys:
   *     - parameters: Simple key-value store of container parameters.
   *     - services: Services like defined in services.yml
   *     - tags: Associative array keyed by tag names with
   *             array('service_name' => $tag_args) as values.
   *
   * @see ServiceProviderInterface::getContainerDefinition()
   */
  public function alterContainerDefinition(&$container_definition);
}
