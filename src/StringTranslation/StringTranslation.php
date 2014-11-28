<?php

/**
 * @file
 * Contains \Drupal\service_provider\StringTranslation\StringTranslation.
 */

namespace Drupal\service_provider\StringTranslation;

use Drupal\Core\StringTranslation\TranslationInterface;

/**
 * Provides a t() based string translation.
 *
 * @codeCoverageIgnore
 */
class StringTranslation implements TranslationInterface {

  /**
   * {@inheritdoc}
   */
  public function translate($string, array $args = array(), array $options = array()) {
    return t($string, $args, $options);
  }

  /**
   * {@inheritdoc}
   */
  public function formatPlural($count, $singular, $plural, array $args = array(), array $options = array()) {
    return format_plural($count, $singular, $plural, $args, $options);
  }

}
