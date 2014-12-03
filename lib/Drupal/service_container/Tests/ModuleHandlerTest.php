<?php

/**
 * @file
 * Contains \Drupal\service_container\Tests\ModuleHandlerTest.
 */

namespace Drupal\service_container\Tests;

/**
 * Tests the module_handler implementation of the service_container.
 */
class ModuleHandlerTest extends ServiceContainerIntegrationTestBase {

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => 'ModuleHandler',
      'description' => 'Tests the module handler.',
      'group' => 'service_container',
    );
  }

  /**
   * The basic functionality of retrieving enabled modules.
   */
  function testModuleList() {
    // Build a list of modules, sorted alphabetically.
    $profile_info = install_profile_info('testing', 'en');
    $module_list = $profile_info['dependencies'];

    // Installation profile is a module that is expected to be loaded.
    $module_list[] = 'testing';
    $module_list[] = 'service_container';

    sort($module_list);
    // Compare this list to the one returned by the module handler. We expect
    // them to match, since all default profile modules have a weight equal to 0
    // (except for block.module, which has a lower weight but comes first in
    // the alphabet anyway).
    $this->assertModuleList($module_list, 'Testing profile');

    // Try to install a new module.
    $this->moduleInstaller()->install(array('ban'));
    $module_list[] = 'ban';
    sort($module_list);
    $this->assertModuleList($module_list, 'After adding a module');

    // Try to mess with the module weights.
    // @FIXME
//    module_set_weight('ban', 20);

    // Move ban to the end of the array.
    unset($module_list[array_search('ban', $module_list)]);
    $module_list[] = 'ban';
    $this->assertModuleList($module_list, 'After changing weights');

    // Test the fixed list feature.
    $fixed_list = array(
      'system' => 'core/modules/system/system.module',
      'menu' => 'core/modules/menu/menu.module',
    );
    $this->moduleHandler()->setModuleList($fixed_list);
    $new_module_list = array_combine(array_keys($fixed_list), array_keys($fixed_list));
    $this->assertModuleList($new_module_list, t('When using a fixed list'));
  }

  /**
   * Assert that the extension handler returns the expected values.
   *
   * @param array $expected_values
   *   The expected values, sorted by weight and module name.
   * @param $condition
   */
  protected function assertModuleList(Array $expected_values, $condition) {
    $expected_values = array_values(array_unique($expected_values));
    $enabled_modules = array_keys($this->container->get('module_handler')->getModuleList());
    $enabled_modules = sort($enabled_modules);
    $this->assertEqual($expected_values, $enabled_modules, format_string('@condition: extension handler returns correct results', array('@condition' => $condition)));
  }

  /**
   * Test module_implements() caching.
   */
  function testModuleImplements() {
    // Make sure group include files are detected properly even when the file is
    // already loaded when the cache is rebuilt.
    // For that activate the module_test which provides the file to load.
    $this->moduleInstaller()->install(array('module_test'));

    $this->moduleHandler()->loadInclude('module_test', 'inc', 'module_test.file');
    $modules = $this->moduleHandler()->getImplementations('test_hook');

    $this->assertTrue(in_array('module_test', $modules), 'Hook found.');
  }

  /**
   * Test that module_invoke() can load a hook defined in hook_hook_info().
   */
  function testModuleInvoke() {
    $this->moduleInstaller()->install(array('module_test'), FALSE);

    $result = $this->moduleHandler()->invoke('module_test', 'test_hook');
    $this->assertEqual('success!', $result['module_test']);
  }

  /**
   * Test that module_invoke_all() can load a hook defined in hook_hook_info().
   */
  function testModuleInvokeAll() {
    $this->moduleInstaller()->install(array('module_test'), FALSE);

    $result = $this->moduleHandler()->invokeAll('test_hook');
    $this->assertEqual('success!', $result['module_test']);
  }

  /**
   * Tests dependency resolution.
   *
   * Intentionally using fake dependencies added via hook_system_info_alter()
   * for modules that normally do not have any dependencies.
   *
   * To simplify things further, all of the manipulated modules are either
   * purely UI-facing or live at the "bottom" of all dependency chains.
   *
   * @see module_test_system_info_alter()
   * @see https://drupal.org/files/issues/dep.gv__0.png
   */
  function testDependencyResolution() {
    return;
    $this->moduleInstaller()->install(array('module_test'));
    $this->assertTrue($this->moduleHandler()->moduleExists('module_test'), 'Test module is enabled.');

    // Ensure that modules are not enabled.
    $this->assertFalse($this->moduleHandler()->moduleExists('color'), 'Color module is disabled.');
    $this->assertFalse($this->moduleHandler()->moduleExists('config'), 'Config module is disabled.');
    $this->assertFalse($this->moduleHandler()->moduleExists('help'), 'Help module is disabled.');

    // Create a missing fake dependency.
    // Color will depend on Config, which depends on a non-existing module Foo.
    // Nothing should be installed.
    \Drupal::state()->set('module_test.dependency', 'missing dependency');
    drupal_static_reset('system_rebuild_module_data');

    $result = $this->moduleInstaller()->install(array('color'));
    $this->assertFalse($result, 'ModuleHandler::install() returns FALSE if dependencies are missing.');
    $this->assertFalse($this->moduleHandler()->moduleExists('color'), 'ModuleHandler::install() aborts if dependencies are missing.');

    // Fix the missing dependency.
    // Color module depends on Config. Config depends on Help module.
    \Drupal::state()->set('module_test.dependency', 'dependency');
    drupal_static_reset('system_rebuild_module_data');

    $result = $this->moduleInstaller()->install(array('color'));
    $this->assertTrue($result, 'ModuleHandler::install() returns the correct value.');

    // Verify that the fake dependency chain was installed.
    $this->assertTrue($this->moduleHandler()->moduleExists('config') && $this->moduleHandler()->moduleExists('help'), 'Dependency chain was installed.');

    // Verify that the original module was installed.
    $this->assertTrue($this->moduleHandler()->moduleExists('color'), 'Module installation with dependencies succeeded.');

    // Verify that the modules were enabled in the correct order.
    $module_order = \Drupal::state()->get('module_test.install_order') ?: array();
    $this->assertEqual($module_order, array('help', 'config', 'color'));

    // Uninstall all three modules explicitly, but in the incorrect order,
    // and make sure that ModuleHandler::uninstall() uninstalled them in the
    // correct sequence.
    $result = $this->moduleInstaller()->uninstall(array('config', 'help', 'color'));
    $this->assertTrue($result, 'ModuleHandler::uninstall() returned TRUE.');

    foreach (array('color', 'config', 'help') as $module) {
      $this->assertEqual(drupal_get_installed_schema_version($module), SCHEMA_UNINSTALLED, "$module module was uninstalled.");
    }
    $uninstalled_modules = \Drupal::state()->get('module_test.uninstall_order') ?: array();
    $this->assertEqual($uninstalled_modules, array('color', 'config', 'help'), 'Modules were uninstalled in the correct order.');

    // Enable Color module again, which should enable both the Config module and
    // Help module. But, this time do it with Config module declaring a
    // dependency on a specific version of Help module in its info file. Make
    // sure that Drupal\Core\Extension\ModuleHandler::install() still works.
    \Drupal::state()->set('module_test.dependency', 'version dependency');
    drupal_static_reset('system_rebuild_module_data');

    $result = $this->moduleInstaller()->install(array('color'));
    $this->assertTrue($result, 'ModuleHandler::install() returns the correct value.');

    // Verify that the fake dependency chain was installed.
    $this->assertTrue($this->moduleHandler()->moduleExists('config') && $this->moduleHandler()->moduleExists('help'), 'Dependency chain was installed.');

    // Verify that the original module was installed.
    $this->assertTrue($this->moduleHandler()->moduleExists('color'), 'Module installation with version dependencies succeeded.');

    // Finally, verify that the modules were enabled in the correct order.
    $enable_order = \Drupal::state()->get('module_test.install_order') ?: array();
    $this->assertIdentical($enable_order, array('help', 'config', 'color'));
  }

  /**
   * Tests uninstalling a module that is a "dependency" of a profile.
   */
  function testUninstallProfileDependency() {
    return;
    $profile = 'minimal';
    $dependency = 'dblog';
    $this->settingsSet('install_profile', $profile);
    $this->enableModules(array('module_test', $profile));

    drupal_static_reset('system_rebuild_module_data');
    $data = system_rebuild_module_data();
    $this->assertTrue(isset($data[$profile]->requires[$dependency]));

    $this->moduleInstaller()->install(array($dependency));
    $this->assertTrue($this->moduleHandler()->moduleExists($dependency));

    // Uninstall the profile module "dependency".
    $result = $this->moduleInstaller()->uninstall(array($dependency));
    $this->assertTrue($result, 'ModuleHandler::uninstall() returns TRUE.');
    $this->assertFalse($this->moduleHandler()->moduleExists($dependency));
    $this->assertEqual(drupal_get_installed_schema_version($dependency), SCHEMA_UNINSTALLED, "$dependency module was uninstalled.");

    // Verify that the installation profile itself was not uninstalled.
    $uninstalled_modules = \Drupal::state()->get('module_test.uninstall_order') ?: array();
    $this->assertTrue(in_array($dependency, $uninstalled_modules), "$dependency module is in the list of uninstalled modules.");
    $this->assertFalse(in_array($profile, $uninstalled_modules), 'The installation profile is not in the list of uninstalled modules.');
  }

  /**
   * Tests whether the correct module metadata is returned.
   */
  function testModuleMetaData() {
    // Generate the list of available modules.
    $modules = system_rebuild_module_data();
    // Check that the mtime field exists for the system module.
    $this->assertTrue(!empty($modules['system']->info['mtime']), 'The system.info.yml file modification time field is present.');
    // Use 0 if mtime isn't present, to avoid an array index notice.
    $test_mtime = !empty($modules['system']->info['mtime']) ? $modules['system']->info['mtime'] : 0;
    // Ensure the mtime field contains a number that is greater than zero.
    $this->assertTrue(is_numeric($test_mtime) && ($test_mtime > 0), 'The system.info.yml file modification time field contains a timestamp.');
  }

  /**
   * Tests whether the correct theme metadata is returned.
   */
  function testThemeMetaData() {
    // Generate the list of available themes.
    $themes = system_rebuild_theme_data();
    // Check that the mtime field exists for the bartik theme.
    $this->assertTrue(!empty($themes['bartik']->info['mtime']), 'The bartik.info.yml file modification time field is present.');
    // Use 0 if mtime isn't present, to avoid an array index notice.
    $test_mtime = !empty($themes['bartik']->info['mtime']) ? $themes['bartik']->info['mtime'] : 0;
    // Ensure the mtime field contains a number that is greater than zero.
    $this->assertTrue(is_numeric($test_mtime) && ($test_mtime > 0), 'The bartik.info.yml file modification time field contains a timestamp.');
  }

  /**
   * Returns the ModuleHandler.
   *
   * @return \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected function moduleHandler() {
    return $this->container->get('module_handler');
  }

  /**
   * Returns the ModuleInstaller.
   *
   * @return \Drupal\Core\Extension\ModuleInstallerInterface
   */
  protected function moduleInstaller() {
    return $this->container->get('module_installer');
  }

}
