<?php

/**
 * @file
 * Contains \Drupal\service_container\Extension\ModuleInstaller.
 */

namespace Drupal\service_container\Extension;

use Drupal\Core\Extension\ModuleInstallerInterface;
use Drupal\Core\Extension\ModuleUninstallValidatorInterface;

/**
 * Provides a module installer compatible with D7.
 *
 * @codeCoverageIgnore
 */
class ModuleInstaller implements ModuleInstallerInterface {

  /**
   * {@inheritdoc}
   */
  public function install(array $module_list, $enable_dependencies = TRUE) {
    module_enable($module_list, $enable_dependencies);
  }

  /**
   * {@inheritdoc}
   */
  public function uninstall(array $module_list, $uninstall_dependents = TRUE) {
    module_disable($module_list, $uninstall_dependents);
    drupal_uninstall_modules($module_list);
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
