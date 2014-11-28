<?php

/**
 * @file
 * Contains \Drupal\service_container\Extension\ModuleInstaller.
 */

namespace Drupal\service_container\Extension;

use Drupal\Core\Extension\ModuleInstallerInterface;

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

}
