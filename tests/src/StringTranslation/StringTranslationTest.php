<?php

/**
 * @file
 * Contains \Drupal\Tests\service_container\StringTranslation\StringTranslationTest
 */

namespace Drupal\Tests\service_container\StringTranslation;

use Drupal\service_container\StringTranslation\StringTranslation;

/**
 * @coversDefaultClass \Drupal\service_container\StringTranslation\StringTranslation
 */
class StringTranslationTest extends \PHPUnit_Framework_TestCase {

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $this->string_translation = new StringTranslation();
  }

  /**
   * @covers ::translate()
   */
  public function test_translate() {}

  /**
   * @covers ::formatPlural()
   */
  public function test_formatPlural() {}

}
