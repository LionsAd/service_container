<?php

/**
 * @file
 * Contains \Drupal\Tests\service_container\StringTranslation\StringTranslationTest
 */

namespace Drupal\Tests\service_container\StringTranslation;

use Drupal\service_container\StringTranslation\StringTranslation;
use Mockery;

/**
 * @coversDefaultClass \Drupal\service_container\StringTranslation\StringTranslation
 */
class StringTranslationTest extends \PHPUnit_Framework_TestCase {

  /**
   * The Drupal7 service.
   *
   * @var \Drupal\service_container\Legacy\Drupal7
   */
  protected $drupal7;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $this->drupal7 = Mockery::mock('\Drupal\service_container\Legacy\Drupal7');
    $this->string_translation = new StringTranslation($this->drupal7);
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
