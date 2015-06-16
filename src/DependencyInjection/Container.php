<?php
/**
 * @file
 * Contains \Drupal\service_container\DependencyInjection\Container
 */

namespace Drupal\service_container\DependencyInjection;

use ReflectionClass;
use RuntimeException;
use Symfony\Component\DependencyInjection\ScopeInterface;

/**
 * Container is a DI container that provides services to users of the class.
 *
 * @ingroup dic
 */
class Container implements ContainerInterface {

  /**
   * The parameters of the container.
   *
   * @var array
   */
  protected $parameters = array();

  /**
   * The service definitions of the container.
   *
   * @var array
   */
  protected $serviceDefinitions = array();

  /**
   * The instantiated services.
   *
   * @var array
   */
  protected $services = array();

  /**
   * The currently loading services.
   *
   * @var array
   */
  protected $loading = array();

  /**
   * Can the container parameters still be changed.
   *
   * For testing purposes the container needs to be changed.
   *
   * @var bool
   */
  protected $frozen = TRUE;

  /**
   * Constructs a new Container instance.
   *
   * @param array $container_definition
   *   An array containing the 'services' and 'parameters'
   * @param bool $frozen
   *   (optional) Determines whether the container parameters can be changed,
   *   defaults to TRUE;
   */
  public function __construct(array $container_definition, $frozen = TRUE) {
    $this->parameters = $container_definition['parameters'];
    $this->serviceDefinitions = $container_definition['services'];
    $this->services['service_container'] = $this;
    $this->frozen = $frozen;
  }

  /**
   * {@inheritdoc}
   */
  public function get($name, $invalidBehavior = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE) {
    if (isset($this->services[$name]) || ($invalidBehavior === ContainerInterface::NULL_ON_INVALID_REFERENCE && array_key_exists($name, $this->services))) {
      return $this->services[$name];
    }

    if (isset($this->loading[$name])) {
      throw new RuntimeException(sprintf('Circular reference detected for service "%s", path: "%s".', $name, implode(' -> ', array_keys($this->loading))));
    }

    $definition = isset($this->serviceDefinitions[$name]) ? $this->serviceDefinitions[$name] : NULL;

    if (!$definition && $invalidBehavior === ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE) {
      throw new RuntimeException(sprintf('The "%s" service definition does not exist.', $name));
    }

    if (!$definition) {
      $this->services[$name] = NULL;
      return $this->services[$name];
    }

    if (isset($definition['alias'])) {
      return $this->get($definition['alias'], $invalidBehavior);
    }

    $this->loading[$name] = TRUE;

    $definition += array(
      'class' => '',
      'factory' => '',
      'factory_class' => '',
      'factory_method' => '',
      'factory_service' => '',
      'arguments' => array(),
      'properties' => array(),
      'calls' => array(),
      'tags' => array(),
    ); // @codeCoverageIgnore

    try {
      if (!empty($definition['arguments'])) {
        $arguments = $this->expandArguments($definition['arguments'], $invalidBehavior);
      } else {
        $arguments = array();
      }
      if (!empty($definition['factory'])) {
        $factory = $definition['factory'];
        if (is_array($factory)) {
          $factory = $this->expandArguments($factory, $invalidBehavior);
        }
        $service = call_user_func_array($factory, $arguments);
      }
      elseif (!empty($definition['factory_method'])) {
        $method = $definition['factory_method'];

        if (!empty($definition['factory_class'])) {
          $factory = $definition['factory_class'];
        }
        elseif (!empty($definition['factory_service'])) {
          $factory = $this->get($definition['factory_service'], $invalidBehavior);
        }
        else {
          throw new RuntimeException(sprintf('Cannot create service "%s" from factory method without a factory service or factory class.', $name));
        }
        $service = call_user_func_array(array($factory, $method), $arguments);
      }
      else {
        // @todo Allow dynamic class definitions via parameters.
        $class = $definition['class'];
        $length = count($arguments);

        switch ($length) {
          case 0:
            $service = new $class();
            break;
          case 1:
            $service = new $class($arguments[0]);
            break;
          case 2:
            $service = new $class($arguments[0], $arguments[1]);
            break;
          case 3:
            $service = new $class($arguments[0], $arguments[1], $arguments[2]);
            break;
          // @codeCoverageIgnoreStart
          case 4:
            $service = new $class($arguments[0], $arguments[1], $arguments[2], $arguments[3]);
            break;
          case 5:
            $service = new $class($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4]);
            break;
          case 6:
            $service = new $class($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4], $arguments[5]);
            break;
          case 7:
            $service = new $class($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4], $arguments[5], $arguments[6]);
            break;
          case 8:
            $service = new $class($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4], $arguments[5], $arguments[6], $arguments[7]);
            break;
          case 9:
            $service = new $class($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4], $arguments[5], $arguments[6], $arguments[7], $arguments[8]);
            break;
          case 10:
            $service = new $class($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4], $arguments[5], $arguments[6], $arguments[7], $arguments[8], $arguments[9]);
            break;
          default:
            $r = new ReflectionClass($class);
            $service = $r->newInstanceArgs($arguments);
            break;
        }
        // @codeCoverageIgnoreEnd
      }
    }
    catch (\Exception $e) {
      unset($this->loading[$name]);
      throw $e;
    }
    $this->services[$name] = $service;
    unset($this->loading[$name]);

    foreach ($definition['calls'] as $call) {
      $method = $call[0];
      $arguments = array();
      if (!empty($call[1])) {
        $arguments = $this->expandArguments($call[1], $invalidBehavior);
      }
      call_user_func_array(array($service, $method), $arguments);
    }
    foreach ($definition['properties'] as $key => $value) {
      $service->{$key} = $value;
    }

    return $this->services[$name];
  }

  /**
   * {@inheritdoc}
   */
  public function set($id, $service, $scope = self::SCOPE_CONTAINER) {
    if (isset($service)) {
      $service->_serviceId = $id;
    }
    $this->services[$id] = $service;
  }

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
