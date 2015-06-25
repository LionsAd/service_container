<?php

namespace Drupal\service_container_annotation_discovery_subtest\Plugin\Plugin8\Plugin8A;

use Drupal\Component\Annotation\PluginID;
use Drupal\Component\Plugin\PluginBase;
use Drupal\service_container\Messenger\MessengerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Plugin8A
 *
 * @Plugin(
 *   id = "Plugin8A",
 *   label = "Label Plugin8A",
 *   arguments = {
 *    "@messenger"
 *   }
 * )
 *
 * @package Drupal\service_container_annotation_discovery_subtest\Plugin\Plugin8\Plugin8A
 */
class Plugin8A extends PluginBase {

  /**
   * @var \Drupal\service_container\Messenger\MessengerInterface
   */
  protected $messenger;

  public function __construct($configuration, $plugin_id, $plugin_definition, MessengerInterface $messenger) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->messenger = $messenger;
  }

  public function getMessenger() {
    return $this->messenger;
  }
}
