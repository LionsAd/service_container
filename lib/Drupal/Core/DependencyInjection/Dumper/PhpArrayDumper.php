<?php
/**
 * @file
 * Contains \Drupal\Core\DependencyInjection\Dumper\PhpArrayDumper
 */

namespace Drupal\Core\DependencyInjection\Dumper;

use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Dumper\Dumper;

/**
 * PhpArrayDumper dumps a service container as a serialized PHP array.
 */
class PhpArrayDumper extends Dumper
{
  /**
   * {@inheritdoc}
   */
  public function dump(array $options = array())
  {
    return serialize($this->getArray());
  }

  /**
   * Returns the service container as a PHP array.
   *
   * @return array
   *  A PHP array represention of the service container
   */
  public function getArray()
  {
    $definition = array();
    $definition['parameters'] = $this->getParameters();
    $definition['services'] = $this->getServiceDefinitions();
    return $definition;
  }


  /**
   * Returns parameters of the container as a PHP Array.
   *
   * @return array
   *   The escaped and prepared parameters of the container.
   */
  protected function getParameters()
  {
    if (!$this->container->getParameterBag()->all()) {
      return array();
    }

    $parameters = $this->container->getParameterBag()->all();
    $is_frozen = $this->container->isFrozen();
    return $this->prepareParameters($parameters, $is_frozen);
  }

  /**
   * Returns services of the container as a PHP Array.
   *
   * @return array
   *   The service definitions.
   */
  protected function getServiceDefinitions()
  {
    if (!$this->container->getDefinitions()) {
      return array();
    }

    $services = array();
    foreach ($this->container->getDefinitions() as $id => $definition) {
      $services[$id] = $this->getServiceDefinition($definition);
    }

    $aliases = $this->container->getAliases();
    foreach ($aliases as $alias => $id) {
      while (isset($aliases[(string) $id])) {
        $id = $aliases[(string) $id];
      }
      $services[$alias] = $this->getServiceAliasDefinition($id);
    }

    return $services;
  }

  /**
   * Prepares parameters.
   *
   * @param array $parameters
   * @param bool  $escape
   *
   * @return array
   */
  protected function prepareParameters($parameters, $escape = true)
  {
    $filtered = array();
    foreach ($parameters as $key => $value) {
      if (is_array($value)) {
        $value = $this->prepareParameters($value, $escape);
      }
      elseif ($value instanceof Reference) {
        $value = '@'.$value;
      }

      $filtered[$key] = $value;
    }

    return $escape ? $this->escape($filtered) : $filtered;
  }

  /**
   * Escapes arguments.
   *
   * @param array $arguments
   *   The arguments to escape.
   *
   * @return array
   *   The escaped arguments.
   */
  protected function escape($arguments)
  {
    $args = array();
    foreach ($arguments as $k => $v) {
      if (is_array($v)) {
        $args[$k] = $this->escape($v);
      }
      elseif (is_string($v)) {
        $args[$k] = str_replace('%', '%%', $v);
      }
      else {
        $args[$k] = $v;
      }
    }

    return $args;
  }

  /**
   * Gets a service definition as PHP array.
   *
   * @param \Symfony\Component\DependencyInjection\Definition $definition
   *   The definition to process.
   *
   * @return array
   *   The service definition as PHP array.
   */
  protected function getServiceDefinition($definition)
  {
    $service = array();
    if ($definition->getClass()) {
      $service['class'] = $definition->getClass();
    }

    if (!$definition->isPublic()) {
      $service['public'] = FALSE;
    }

    /*
        $tagsCode = '';
        foreach ($definition->getTags() as $name => $tags) {
          foreach ($tags as $attributes) {
            $att = array();
            foreach ($attributes as $key => $value) {
              $att[] = sprintf('%s: %s', $this->dumper->dump($key), $this->dumper->dump($value));
            }
            $att = $att ? ', '.implode(', ', $att) : '';

            $tagsCode .= sprintf("      - { name: %s%s }\n", $this->dumper->dump($name), $att);
          }
        }
        if ($tagsCode) {
          $code .= "    tags:\n".$tagsCode;
        }
    */

    if ($definition->getFile()) {
      $service['file'] = $definition->getFile();
    }

    if ($definition->isSynthetic()) {
      $service['synthetic'] = TRUE;
    }

    if ($definition->isLazy()) {
      $service['lazy'] = TRUE;
    }

    if ($definition->getArguments()) {
      $service['arguments'] = $this->dumpValue($definition->getArguments());
    }

    if ($definition->getProperties()) {
      $service['properties'] = $this->dumpValue($definition->getProperties());
    }

    if ($definition->getMethodCalls()) {
      $service['calls'] = $this->dumpValue($definition->getMethodCalls());
    }

    if (($scope = $definition->getScope()) !== ContainerInterface::SCOPE_CONTAINER) {
      $service['scope'] = $scope;
    }

    if (($decorated = $definition->getDecoratedService()) !== NULL) {
      $service['decorates'] = $decorated;
    }

    if ($callable = $definition->getFactory()) {
      $service['factory'] = $this->dumpCallable($callable);
    }

    if ($callable = $definition->getConfigurator()) {
      $service['configurator'] = $this->dumpCallable($callable);
    }

    return $service;
  }

  /**
   * Returns a service alias definiton.
   *
   * @param string $alias
   * @param Alias  $id
   *
   * @return string
   */
  protected function getServiceAliasDefinition($id)
  {
    if ($id->isPublic()) {
      return array(
        'alias' => (string) $id,
      );
    } else {
      return array(
        'alias' => (string) $id,
        'public' => FALSE,
      );
    }
  }
  /**
   * Dumps callable to YAML format
   *
   * @param callable $callable
   *
   * @return callable
   */
  protected function dumpCallable($callable)
  {
    if (is_array($callable)) {
      if ($callable[0] instanceof Reference) {
        $callable = array($this->getServiceCall((string) $callable[0], $callable[0]), $callable[1]);
      }
      elseif ($callable[0] instanceof Definition) {
        $callable[0] = $this->getPrivateService($callable[0]);
        $callable = array($callable[0], $callable[1]);
      }
      else {
        $callable = array($callable[0], $callable[1]);
      }
    }

    return $callable;
  }

  /**
   * Returns a private service definition in a suitable format.
   *
   * @param \Symfony\Component\DependencyInjection\Definition $definition
   *   The definition to process.
   *
   * @return \stdClass
   *   A very lightweight private service value object.
   */
  protected function getPrivateService(Definition $definition) {
    $service_definition = $this->getServiceDefinition($definition);
    $hash = sha1(serialize($service_definition));
    return (object) array(
      'type' => 'service',
      'id' => 'private__' . $hash,
      'value' => $service_definition,
    );
  }

  /**
   * Dumps the value to YAML format.
   *
   * @param mixed $value
   *
   * @return mixed
   *
   * @throws RuntimeException When trying to dump object or resource
   */
  protected function dumpValue($value)
  {
    if (is_array($value)) {
      $code = array();
      foreach ($value as $k => $v) {
        $code[$k] = $this->dumpValue($v);
      }

      return $code;
    } elseif ($value instanceof Reference) {
      return $this->getServiceCall((string) $value, $value);
    } elseif ($value instanceof Definition) {
      return $this->getPrivateService($value);
    } elseif ($value instanceof Parameter) {
      return $this->getParameterCall((string) $value);
    } elseif ($value instanceof Expression) {
      return $this->getExpressionCall((string) $value);
    } elseif (is_object($value)) {
      if (isset($value->_serviceId)) {
        return '@' . $value->_serviceId;
      }
      throw new RuntimeException('Unable to dump a service container if a parameter is an object without _serviceId.');
    } elseif (is_resource($value)) {
      throw new RuntimeException('Unable to dump a service container if a parameter is a resource.');
    }

    return $value;
  }

  /**
   * Gets the service call.
   *
   * @param string  $id
   * @param Reference $reference
   *
   * @return string
   */
  protected function getServiceCall($id, Reference $reference = null)
  {
    if (null !== $reference && ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE !== $reference->getInvalidBehavior()) {
      return sprintf('@?%s', $id);
    }

    return sprintf('@%s', $id);
  }

  /**
   * Gets parameter call.
   *
   * @param string $id
   *
   * @return string
   */
  protected function getParameterCall($id)
  {
    return sprintf('%%%s%%', $id);
  }

  protected function getExpressionCall($expression)
  {
    return sprintf('@=%s', $expression);
  }
}
