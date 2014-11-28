<?php

/**
 * @file
 * Contains Drupal\service_container\Extension\ModuleHandler.
 */

namespace Drupal\service_container\Extension;

use Drupal\Core\Extension\Extension;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Class that manages modules in a Drupal installation.
 *
 * @codeCoverageIgnore
 *
 * @todo: Some of this might be unit-testable.
 */
class ModuleHandler implements ModuleHandlerInterface {

  /**
   * The app root.
   *
   * @var string
   */
  protected $root;

  /**
   * Constructs a ModuleHandler object.
   *
   * @param string $root
   *   The app root.
   * @param array $module_list
   *   An associative array whose keys are the names of installed modules and
   *   whose values are Extension class parameters. This is normally the
   *   %container.modules% parameter being set up by DrupalKernel.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend for storing module hook implementation information.
   *
   * @see \Drupal\Core\DrupalKernel
   * @see \Drupal\Core\CoreServiceProvider
   */
  public function __construct($root, array $module_list = array()) {
    $this->root = $root;
  }

  public static function create() {
    return new static(
      DRUPAL_ROOT
    );
  }

  /**
   * {@inheritdoc}
   */
  public function load($name) {
    return drupal_load('module', $name);
  }

  /**
   * {@inheritdoc}
   */
  public function loadAll() {
    module_load_all();
  }

  /**
   * {@inheritdoc}
   */
  public function reload() {
    module_load_all();
  }

  /**
   * {@inheritdoc}
   */
  public function isLoaded() {
    return module_load_all(NULL);
  }

  /**
   * {@inheritdoc}
   */
  public function getModuleList() {
    $module_list = array();
    foreach (module_list() as $module) {
      $module_list[$module] = $this->getModule($module);
    }
    return $module_list;
  }

  /**
   * {@inheritdoc}
   */
  public function getModule($name) {
    if (!module_exists($name)) {
      throw new \InvalidArgumentException(sprintf('The module %s does not exist.', $name));
    }

    $filename = drupal_get_filename('module', $name);
    return new Extension($this->root, 'module', $filename, $name . '.info');
  }

  /**
   * {@inheritdoc}
   */
  public function setModuleList(array $module_list = array()) {
    // Convert an array of module filenames to an array of module info, keyed by
    // module name.
    foreach ($module_list as $module_name => $filename) {
      $module_list[$module_name] = array(
        'filename' => $filename,
      );
    }
    module_list(FALSE, FALSE, FALSE, $module_list);
  }

  /**
   * {@inheritdoc}
   */
  public function addModule($name, $path) {
    throw new \BadMethodCallException('ModuleHandler::addModule is not implemented.');
  }

  /**
   * {@inheritdoc}
   */
  public function addProfile($name, $path) {
    throw new \BadMethodCallException('ModuleHandler::addProfile is not implemented.');
  }

  /**
   * {@inheritdoc}
   */
  public function buildModuleDependencies(array $modules) {
    // @TODO
  }

  /**
   * {@inheritdoc}
   */
  public function moduleExists($module) {
    return module_exists($module);
  }

  /**
   * {@inheritdoc}
   */
  public function loadAllIncludes($type, $name = NULL) {
    module_load_all_includes($type, $name);
  }

  /**
   * {@inheritdoc}
   */
  public function loadInclude($module, $type, $name = NULL) {
    module_load_include($type, $module, $name);
  }

  /**
   * {@inheritdoc}
   */
  public function getHookInfo() {
    return module_hook_info();
  }

  /**
   * {@inheritdoc}
   */
  public function getImplementations($hook) {
    return module_implements($hook);
  }

  /**
   * {@inheritdoc}
   */
  public function writeCache() {
    module_implements_write_cache();
  }

  /**
   * {@inheritdoc}
   */
  public function resetImplementations() {
    drupal_static_reset('module_implements');
  }

  /**
   * {@inheritdoc}
   */
  public function implementsHook($module, $hook) {
    $implementations = module_implements($hook);
    return in_array($module, $implementations);
  }

  /**
   * {@inheritdoc}
   */
  public function invoke($module, $hook, array $args = array()) {
    return module_invoke($module, $hook, $args);
  }

  /**
   * {@inheritdoc}
   */
  public function invokeAll($hook, array $args = array()) {
    return module_invoke_all($hook, $args);
  }

  /**
   * {@inheritdoc}
   */
  public function alter($type, &$data, &$context1 = NULL, &$context2 = NULL) {
    // @todo Sadly ::alter() does not allow three $context values.
    drupal_alter($type, $data, $context1, $context2);
  }

  /**
   * {@inheritdoc}
   */
  public function getModuleDirectories() {
    $dirs = array();
    foreach ($this->getModuleList() as $name => $module) {
      $dirs[$name] = $this->root . '/' . $module->getPath();
    }
    return $dirs;
  }

  /**
   * {@inheritdoc}
   */
  public function getName($module) {
    $module_data = system_rebuild_module_data();
    return $module_data[$module]->info['name'];
  }

}
