<?php
/**
 * @file
 * Contains Drupal 7 foreward compatibility layer for Drupal 8.
 */

/**
 * Static Service Container wrapper.
 *
 * Generally, code in Drupal should accept its dependencies via either
 * constructor injection or setter method injection. However, there are cases,
 * particularly in legacy procedural code, where that is infeasible. This
 * class acts as a unified global accessor to arbitrary services within the
 * system in order to ease the transition from procedural code to injected OO
 * code.
 *
 */
class Drupal {

  /**
   * The currently active container object.
   *
   * @var \Drupal\service_container\DependencyInjection\ContainerInterface
   */
  protected static $container;

  /**
   * Returns the currently active global container.
   *
   * @deprecated This method is only useful for the testing environment. It
   * should not be used otherwise.
   *
   * @return \Drupal\service_container\DependencyInjection\ContainerInterface
   */
  public static function getContainer() {
    return static::$container;
  }

  /**
   * Retrieves a service from the container.
   *
   * Use this method if the desired service is not one of those with a dedicated
   * accessor method below. If it is listed below, those methods are preferred
   * as they can return useful type hints.
   *
   * @param string $id
   *   The ID of the service to retrieve.
   * @return mixed
   *   The specified service.
   */
  public static function service($id) {
    return static::$container->get($id);
  }

  /**
   * Indicates if a service is defined in the container.
   *
   * @param string $id
   *   The ID of the service to check.
   *
   * @return bool
   *   TRUE if the specified service exists, FALSE otherwise.
   */
  public static function hasService($id) {
    // @todo Add ->has method to the container to be compatible.
    return static::$container && static::$container->hasDefinition($id);
  }
}
