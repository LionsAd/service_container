<?php

namespace Drupal\service_container_annotation_discovery_test\Plugin\Plugin5\Plugin5A;

use Drupal\Component\Annotation\PluginID;
use Drupal\Component\Plugin\PluginBase;
use Drupal\service_container\Messenger\MessengerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Plugin5A
 *
 * @Plugin(
 *   id = "Plugin5A",
 *   label = "Label Plugin5A",
 * )
 *
 * @package Drupal\service_container_annotation_discovery_test\Plugin\Plugin5\Plugin5A
 */
class Plugin5A extends PluginBase {

}
