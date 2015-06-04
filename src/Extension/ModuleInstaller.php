<?php

/**
 * @file
 * Contains \Drupal\service_container\Extension\ModuleInstaller.
 */

namespace Drupal\service_container\Extension;

use Drupal\Core\Extension\ModuleInstallerInterface;
use Drupal\Core\Extension\ModuleUninstallValidatorInterface;
use Drupal\service_container\Legacy\Drupal7;

/**
 * Provides a module installer compatible with D7.
 *
 * @codeCoverageIgnore
 */
class ModuleInstaller implements ModuleInstallerInterface {

  /**
   * The Drupal7 service.
   *
   * @var \Drupal\service_container\Legacy\Drupal7
   */
  protected $drupal7;

  /**
   * Constructs a new ModuleInstaller instance.
   *
   * @param \Drupal\service_container\Legacy\Drupal7 $drupal7
   *   The Drupal7 service.
   */
  public function __construct(Drupal7 $drupal7) {
    $this->drupal7 = $drupal7;
  }

  /**
   * {@inheritdoc}
   */
  public function install(array $module_list, $enable_dependencies = TRUE) {
    $this->drupal7->module_enable($module_list, $enable_dependencies);
  }

  /**
   * {@inheritdoc}
   */
  public function uninstall(array $module_list, $uninstall_dependents = TRUE) {
    $this->drupal7->module_disable($module_list, $uninstall_dependents);
    $this->drupal7->drupal_uninstall_modules($module_list);
  }

  /**
   * {@inheritdoc}
   */
  public function addUninstallValidator(ModuleUninstallValidatorInterface $uninstall_validator) {
    throw new \BadMethodCallException(sprintf('%s is not implemented', __FUNCTION__));
  }

  /**
   * {@inheritdoc}
   */
  public function validateUninstall(array $module_list) {
    throw new \BadMethodCallException(sprintf('%s is not implemented', __FUNCTION__));
  }

}
