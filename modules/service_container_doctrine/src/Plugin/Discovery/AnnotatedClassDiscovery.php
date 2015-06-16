<?php

/**
 * @file
 * Contains \Drupal\service_container\Plugin\Discovery\AnnotatedClassDiscovery
 */

namespace Drupal\service_container_doctrine\Plugin\Discovery;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Doctrine\Common\Reflection\StaticReflectionParser;
use Drupal\Component\Annotation\Reflection\MockFileFinder;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\service_container\Plugin\Discovery\CToolsPluginDiscovery;

/**
 * TODO
 *
 * This class cannot be tested as it relies on the existence of procedural code.
 * @codeCoverageIgnore
 */
class AnnotatedClassDiscovery extends CToolsPluginDiscovery {

  /**
   * The namespaces within which to find plugin classes.
   *
   * @var string[]
   */
  protected $pluginNamespaces;

  /**
   * The name of the annotation that contains the plugin definition.
   *
   * The class corresponding to this name must implement
   * \Drupal\Component\Annotation\AnnotationInterface.
   *
   * @var string
   */
  protected $pluginDefinitionAnnotationName;

  /**
   * The doctrine annotation reader.
   *
   * @var \Doctrine\Common\Annotations\Reader
   */
  protected $annotationReader;

  /**
   * Constructs a new instance.
   *
   * @param string[] $plugin_namespaces
   *   (optional) An array of namespace that may contain plugin implementations.
   *   Defaults to an empty array.
   * @param string $plugin_definition_annotation_name
   *   (optional) The name of the annotation that contains the plugin definition.
   *   Defaults to 'Drupal\Component\Annotation\Plugin'.
   */
  function __construct($plugin_manager_definition, $plugin_namespaces = array(), $plugin_definition_annotation_name = 'Drupal\Component\Annotation\Plugin') {
    parent::__construct($plugin_manager_definition);

    $directory = module_invoke($plugin_manager_definition['owner'], 'ctools_plugin_directory', $plugin_manager_definition['owner'], $plugin_manager_definition['type']);
    $base_directory = DRUPAL_ROOT . '/' . drupal_get_path('module', $plugin_manager_definition['owner']) . '/' . $directory;
    $namespace = new \ArrayObject(array('Drupal\\' . $plugin_manager_definition['owner'] . '\\' . $plugin_manager_definition['owner'] => array($base_directory)));

    $this->pluginNamespaces = $namespace;
    $this->pluginDefinitionAnnotationName = $plugin_definition_annotation_name;
  }

  /**
   * Gets the used doctrine annotation reader.
   *
   * @return \Doctrine\Common\Annotations\Reader
   *   The annotation reader.
   */
  protected function getAnnotationReader() {
    if (!isset($this->annotationReader)) {
      $this->annotationReader = new SimpleAnnotationReader();

      // Add the namespaces from the main plugin annotation, like @EntityType.
      $namespace = substr($this->pluginDefinitionAnnotationName, 0, strrpos($this->pluginDefinitionAnnotationName, '\\'));
      $this->annotationReader->addNamespace($namespace);
    }
    return $this->annotationReader;
  }

  /**
   * Gets an array of PSR-0 namespaces to search for plugin classes.
   *
   * @return string[]
   */
  protected function getPluginNamespaces() {
    return $this->pluginNamespaces;
  }

  /**
   * {@inheritdoc}
   */
  public function getDefinitions() {
    $definitions = array();

    $reader = $this->getAnnotationReader();

    // Clear the annotation loaders of any previous annotation classes.
    AnnotationRegistry::reset();
    // Register the namespaces of classes that can be used for annotations.
    AnnotationRegistry::registerLoader('class_exists');

    // Search for classes within all PSR-0 namespace locations.
    foreach ($this->getPluginNamespaces() as $namespace => $dirs) {
      foreach ($dirs as $dir) {
        if (file_exists($dir)) {
          $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS)
          );
          foreach ($iterator as $fileinfo) {
            if ($fileinfo->getExtension() == 'php') {
              $sub_path = $iterator->getSubIterator()->getSubPath();
              $sub_path = $sub_path ? str_replace(DIRECTORY_SEPARATOR, '\\', $sub_path) . '\\' : '';
              $class = $namespace . '\\' . $sub_path . $fileinfo->getBasename('.php');

              // The filename is already known, so there is no need to find the
              // file. However, StaticReflectionParser needs a finder, so use a
              // mock version.

              $finder = MockFileFinder::create($fileinfo->getPathName());
              $parser = new StaticReflectionParser($class, $finder, TRUE);

              /** @var $annotation \Drupal\Component\Annotation\AnnotationInterface */
              if ($annotation = $reader->getClassAnnotation($parser->getReflectionClass(), $this->pluginDefinitionAnnotationName)) {
                // @TODO: Do we need this ?
                //$this->prepareAnnotationDefinition($annotation, $class);
                $definitions[$annotation->getId()] = $annotation->get();
              }
            }
          }
        }
      }
    }

    // Don't let annotation loaders pile up.
    AnnotationRegistry::reset();

    return $definitions;
  }

  /**
   * Prepares the annotation definition.
   *
   * @param \Drupal\Component\Annotation\AnnotationInterface $annotation
   *   The annotation derived from the plugin.
   * @param string $class
   *   The class used for the plugin.
   */
  protected function prepareAnnotationDefinition(AnnotationInterface $annotation, $class) {
    $annotation->setClass($class);
  }

  /**
   * {@inheritdoc}
   */
  public function getDefinition($plugin_id, $exception_on_invalid = TRUE) {
    ctools_include('plugins');
    $definition = ctools_get_plugins($this->pluginOwner, $this->pluginType, $plugin_id);

    if (!$definition && $exception_on_invalid) {
      throw new PluginNotFoundException($plugin_id, sprintf('The "%s" plugin does not exist.', $plugin_id));
    }

    return $definition;
  }

  /**
   * {@inheritdoc}
   */
  public function hasDefinition($plugin_id) {
    return (bool) $this->getDefinition($plugin_id, FALSE);
  }

}
