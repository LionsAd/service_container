<?php

/**
 * @file
 * Contains \Drupal\service_container_block\Plugin\Block\ServiceContainerBlock.
 */

namespace Drupal\service_container_block\Plugin\Block;
use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Drupal Block.
 *
 * @Block(
 *   id = "ServiceContainerBlock",
 *   admin_label = "Service Container admin label",
 *   label = "Service Container Block",
 *   category = "Utility"
 * )
 */
class ServiceContainerBlock {
  /**
   * {@inheritdoc}
   */
  public function build() {
    return 'Hello World !';
  }
}
