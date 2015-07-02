<?php

/**
 * @file
 * Contains \Drupal\service_container_annotation_discovery\Plugin\Discovery\AnnotatedClassDiscovery
 */

namespace Drupal\service_container_annotation_discovery\Plugin\Discovery;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Doctrine\Common\Reflection\StaticReflectionParser;
use Drupal\Component\Annotation\Reflection\MockFileFinder;
use Drupal\Component\Plugin\Discovery\DiscoveryInterface;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;

/**
 * TODO
 *
 * This class cannot be tested as it relies on the existence of procedural code.
 * @codeCoverageIgnore
 */
class AnnotatedClassDiscovery implements DiscoveryInterface {

  /**
   * The plugin definition.
   *
   * @var array
   */
  protected $pluginManagerDefinition;

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
   * @param string[] $plugin_manager_definition
   *   (optional) An array of namespace that may contain plugin implementations.
   *   Defaults to an empty array.
   * @param string $plugin_definition_annotation_name
   *   (optional) The name of the annotation that contains the plugin definition.
   *   Defaults to 'Drupal\Component\Annotation\Plugin'.
   */
  function __construct($plugin_manager_definition, $plugin_definition_annotation_name = 'Drupal\Component\Annotation\Plugin') {
    $namespaces = array();

    foreach(module_list() as $module_name) {
      $directory = DRUPAL_ROOT . '/' . drupal_get_path('module', $module_name) . '/src/' . trim($plugin_manager_definition['directory'], DIRECTORY_SEPARATOR);
      $namespaces['Drupal\\' . $module_name] = array($directory);
    }

    $this->pluginNamespaces = new \ArrayObject($namespaces);
    $this->pluginDefinitionAnnotationName = isset($plugin_manager_definition['class']) ? $plugin_manager_definition['class'] : $plugin_definition_annotation_name;
    $this->pluginManagerDefinition = $plugin_manager_definition;
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
              $sub_path = $sub_path ? str_replace('/', '\\', $sub_path) . '\\' : '';
              $class = $namespace . '\\' . str_replace('/', '\\', $this->pluginManagerDefinition['directory']) . '\\' . $sub_path . $fileinfo->getBasename('.php');

              // The filename is already known, so there is no need to find the
              // file. However, StaticReflectionParser needs a finder, so use a
              // mock version.

              $finder = MockFileFinder::create($fileinfo->getPathName());
              $parser = new StaticReflectionParser($class, $finder, TRUE);

              if ($annotation = $reader->getClassAnnotation($parser->getReflectionClass(), $this->pluginDefinitionAnnotationName)) {
                $this->prepareAnnotationDefinition($annotation, $class);
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
  protected function prepareAnnotationDefinition($annotation, $class) {
    $annotation->setClass($class);
  }

  /**
   * {@inheritdoc}
   */
  public function getDefinition($plugin_id, $exception_on_invalid = TRUE) {
    $definitions = $this->getDefinitions();
    $definition = isset($definitions[$plugin_id]) ? $definitions['$plugin_id'] : FALSE;

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
