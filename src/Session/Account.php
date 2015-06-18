<?php

/**
 * @file
 * Contains \Drupal\service_container\Session\Account.
 */

namespace Drupal\service_container\Session;

use Drupal\Core\Session\AccountInterface;
use Drupal\service_container\Legacy\Drupal7;
use Drupal\service_container\Variable;

/**
 * Wraps the global user to provide the account interface.
 *
 * @codeCoverageIgnore
 */
class Account implements AccountInterface {

  /**
   * The Drupal7 service.
   *
   * @var \Drupal\service_container\Legacy\Drupal7
   */
  protected $drupal7;

  /**
   * The variable storage.
   *
   * @var \Drupal\service_container\Variable
   */
  protected $variable;

  /**
   * Constructs a new Account instance.
   *
   * @param \Drupal\service_container\Legacy\Drupal7 $drupal7
   *   The Drupal7 service.
   * @param \Drupal\service_container\Variable $variable
   *   The variable storage.
   */
  public function __construct(Drupal7 $drupal7, Variable $variable) {
    $this->drupal7 = $drupal7;
    $this->variable = $variable;
  }

  /**
   * {@inheritdoc}
   */
  public function id() {
    return $GLOBALS['user']->uid;
  }

  /**
   * {@inheritdoc}
   */
  public function getRoles($exclude_locked_roles = FALSE) {
    $rids = array_keys($GLOBALS['user']->roles);
    if ($exclude_locked_roles) {
      $rids = array_filter($rids, function($value) {
        return $value != DRUPAL_ANONYMOUS_RID && $value != DRUPAL_AUTHENTICATED_RID;
      });
    }
    return $rids;
  }

  /**
   * {@inheritdoc}
   */
  public function hasPermission($permission) {
    return $this->drupal7->user_access($permission, $GLOBALS['user']);
  }

  /**
   * {@inheritdoc}
   */
  public function getSessionId() {
    throw new \BadMethodCallException(sprintf('%s is not implemented', __FUNCTION__));
  }

  /**
   * {@inheritdoc}
   */
  public function getSecureSessionId() {
    throw new \BadMethodCallException(sprintf('%s is not implemented', __FUNCTION__));
  }

  /**
   * {@inheritdoc}
   */
  public function getSessionData() {
    return $_SESSION;
  }

  /**
   * {@inheritdoc}
   */
  public function isAuthenticated() {
    return $this->drupal7->user_is_logged_in();
  }

  /**
   * {@inheritdoc}
   */
  public function isAnonymous() {
    return $this->drupal7->user_is_anonymous();
  }

  /**
   * {@inheritdoc}
   */
  public function getPreferredLangcode($fallback_to_default = TRUE) {
    throw new \BadMethodCallException(sprintf('%s is not implemented', __FUNCTION__));
  }

  /**
   * {@inheritdoc}
   */
  public function getPreferredAdminLangcode($fallback_to_default = TRUE) {
    throw new \BadMethodCallException(sprintf('%s is not implemented', __FUNCTION__));
  }

  /**
   * {@inheritdoc}
   */
  public function getUsername() {
    return $GLOBALS['user']->name;
  }

  /**
   * {@inheritdoc}
   */
  public function getEmail() {
    return $GLOBALS['user']->mail;
  }

  /**
   * {@inheritdoc}
   */
  public function getTimeZone() {
    return $this->drupal7->drupal_get_user_timezone();
  }

  /**
   * {@inheritdoc}
   */
  public function getLastAccessedTime() {
    return $GLOBALS['user']->access;
  }

  /**
   * {@inheritdoc}
   */
  public function getHostname() {
    return $GLOBALS['user']->hostname;
  }

}
