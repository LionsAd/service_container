<?php

/**
 * @file
 * Contains \Drupal\service_container\StringTranslation\StringTranslation.
 */

namespace Drupal\service_container\StringTranslation;

use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\service_container\Legacy\Drupal7;

/**
 * Provides a t() based string translation.
 *
 * @codeCoverageIgnore
 */
class StringTranslation implements TranslationInterface {

  /**
   * The Drupal7 service.
   *
   * @var \Drupal\service_container\Legacy\Drupal7
   */
  protected $drupal7;

  /**
   * Constructs a new StringTranslation instance.
   *
   * @param \Drupal\service_container\Legacy\Drupal7 $drupal7
   *   The Drupal7 service.
   */
  public function __construct(Drupal7 $drupal7) {
    $this->drupal7 = $drupal7;
  }

  /**
   * {@inheritdoc}
   */
  public function translate($string, array $args = array(), array $options = array()) {
    return $this->drupal7->t($string, $args, $options);
  }

  /**
   * {@inheritdoc}
   */
  public function formatPlural($count, $singular, $plural, array $args = array(), array $options = array()) {
    return $this->drupal7->format_plural($count, $singular, $plural, $args, $options);
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
