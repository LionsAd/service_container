<?php
/**
 * @file
 * Contains \Drupal\service_container\DependencyInjection\Container
 */

namespace Drupal\service_container\DependencyInjection;

use Drupal\Component\DependencyInjection\PhpArrayContainer;
use ReflectionClass;
use RuntimeException;
use Symfony\Component\DependencyInjection\ScopeInterface;

/**
 * Container is a DI container that provides services to users of the class.
 *
 * @ingroup dic
 */
class Container extends PhpArrayContainer {

  /**
   * {@inheritdoc}
   */
  public function has($id) {
    return isset($this->services[$id]) || isset($this->serviceDefinitions[$id]);
  }

  /**
   * {@inheritdoc}
   */
  public function createInstance($plugin_id, $service_definition) {
    $temporary_name = 'plugin_' . $plugin_id;
    $this->serviceDefinitions[$temporary_name] = $service_definition;

    $plugin = $this->get($temporary_name);
    unset($this->serviceDefinitions[$temporary_name]);
    unset($this->services[$temporary_name]);

    return $plugin;
  }

  /**
   * {@inheritdoc}
   */
  public function getDefinition($plugin_id, $exception_on_invalid = TRUE) {
    $definition = isset($this->serviceDefinitions[$plugin_id]) ? $this->serviceDefinitions[$plugin_id] : NULL;

    if (!$definition && $exception_on_invalid) {
      throw new RuntimeException(sprintf('The "%s" service definition does not exist.', $plugin_id));
    }

    return $definition;
  }

  /**
   * {@inheritdoc}
   */
  public function getDefinitions() {
    return $this->serviceDefinitions;
  }

  /**
   * {@inheritdoc}
   */
  public function hasDefinition($plugin_id) {
    return isset($this->serviceDefinitions[$plugin_id]);
  }

  /**
   * {@inheritdoc}
   */
  public function getParameter($name) {
    return isset($this->parameters[$name]) ? $this->parameters[$name] : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function hasParameter($name) {
    return isset($this->parameters[$name]);
  }

  /**
   * {@inheritdoc}
   */
  public function setParameter($name, $value) {
    if ($this->frozen) {
      throw new \BadMethodCallException("Container parameters can't be changed on runtime.");
    }
    $this->parameters[$name] = $value;
  }

  /**
   * Expands arguments from %parameter and @service to the resolved values.
   *
   * @param array $arguments
   *   The arguments to expand.
   * @param int $invalidBehavior
   *   The behavior when the service does not exist
   *
   * @return array
   *   The expanded arguments.
   *
   * @throws \RuntimeException if a parameter/service could not be resolved.
   */
  protected function expandArguments($arguments, $invalidBehavior = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE) {
    foreach ($arguments as $key => $argument) {
      if ($argument instanceof \stdClass) {
        $name = $argument->id;
        $this->serviceDefinitions[$name] = $argument->value;
        $arguments[$key] = $this->get($name, $invalidBehavior);
        unset($this->serviceDefinitions[$name]);
        unset($this->services[$name]);
        continue;
      }

      if (is_array($argument)) {
        $arguments[$key] = $this->expandArguments($argument, $invalidBehavior);
        continue;
      }

      if (!is_string($argument)) {
        continue;
      }

      if (strpos($argument, '%') === 0) {
        $name = substr($argument, 1, -1);
        if (!isset($this->parameters[$name])) {
          if ($invalidBehavior === ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE) {
            throw new RuntimeException("Could not find parameter: $name");
          }
          $arguments[$key] = NULL;
          continue;
        }
        $arguments[$key] = $this->parameters[$name];
      }
      else if (strpos($argument, '@') === 0) {
        $name = substr($argument, 1);
        if (strpos($name, '?') === 0) {
          $name = substr($name, 1);
          $invalidBehavior = ContainerInterface::NULL_ON_INVALID_REFERENCE;
        }
        if (!isset($this->serviceDefinitions[$name])) {
          if ($invalidBehavior === ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE) {
            throw new RuntimeException("Could not find service: $name");
          }
          $arguments[$key] = NULL;
          continue;
        }
        $arguments[$key] = $this->get($name, $invalidBehavior);
      }
    }

    return $arguments;
  }

  /**
   * {@inheritdoc}
   *
   * @codeCoverageIgnore
   */
  public function enterScope($name) {
    throw new \BadMethodCallException(sprintf("'%s' is not implemented", __FUNCTION__));
  }

  /**
   * {@inheritdoc}
   *
   * @codeCoverageIgnore
   */
  public function leaveScope($name) {
    throw new \BadMethodCallException(sprintf("'%s' is not implemented", __FUNCTION__));
  }

  /**
   * {@inheritdoc}
   *
   * @codeCoverageIgnore
   */
  public function addScope(ScopeInterface $scope) {
    throw new \BadMethodCallException(sprintf("'%s' is not implemented", __FUNCTION__));
  }

  /**
   * {@inheritdoc}
   *
   * @codeCoverageIgnore
   */
  public function hasScope($name) {
    throw new \BadMethodCallException(sprintf("'%s' is not implemented", __FUNCTION__));
  }

  /**
   * {@inheritdoc}
   *
   * @codeCoverageIgnore
   */
  public function isScopeActive($name) {
    throw new \BadMethodCallException(sprintf("'%s' is not implemented", __FUNCTION__));
  }

  /**
   * {@inheritdoc}
   */
  public function initialized($id) {
    return isset($this->services[$id]);
  }

  /**
   * Camelizes a string.
   *
   * @param $name
   *   The string to camelize.
   *
   * @return string
   *   The camelized string.
   *
   */
  public static function camelize($name) {
    return strtr(ucwords(strtr($name, array('_' => ' ', '\\' => '_ '))), array(' ' => ''));
  }

  /**
   * Un-camelizes a string.
   *
   * @param $name
   *   The string to underscore.
   *
   * @return string
   *   The underscored string.
   *
   */
  public static function underscore($name) {
    return strtolower(preg_replace(array('/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'), array('\\1_\\2', '\\1_\\2'), $name));
  }
}
