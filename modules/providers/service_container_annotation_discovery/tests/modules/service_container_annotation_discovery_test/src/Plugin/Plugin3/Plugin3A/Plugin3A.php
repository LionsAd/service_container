<?php

namespace Drupal\service_container_annotation_discovery_test\Plugin\Plugin3\Plugin3A;

use Drupal\Component\Annotation\Plugin;
use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Plugin3A
 *
 * @Plugin(
 *   id = "Plugin3A",
 *   label = "Label Plugin3A"
 * )
 *
 * @package Drupal\service_container_annotation_discovery_test\Plugin\Plugin3\Plugin3A
 */
class Plugin3A extends PluginBase implements ContainerFactoryPluginInterface {

  protected $data;

  /**
   * @inheritdoc
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition, 'Hello world!');
  }

  public function __construct($configuration, $plugin_id, $plugin_definition, $data) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->data = $data;
  }

  public function getData() {
    return $this->data;
  }
}
