<?php

/**
 * @file
 * Contains \Drupal\service_container\StringTranslation\StringTranslation.
 */

namespace Drupal\service_container\StringTranslation;

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

  /**
   * {@inheritdoc}
   */
  public function formatPluralTranslated($count, $translation, array $args = array(), array $options = array()) {
    throw new \BadMethodCallException('StringTranslation::formatPluralTranslated is not implemented.');
  }

  /**
   * {@inheritdoc}
   */
  public function getNumberOfPlurals($langCode = NULL) {
    throw new \BadMethodCallException('StringTranslation::getNumberOfPlurals is not implemented.');
  }

}
