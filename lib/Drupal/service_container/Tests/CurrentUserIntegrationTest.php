<?php

/**
 * @file
 * Contains \Drupal\service_container\Tests\CurrentUserIntegrationTest.
 */

namespace Drupal\service_container\Tests;

use Drupal\service_container\Session\Account;

class CurrentUserIntegrationTest extends ServiceContainerIntegrationTestBase {

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => 'CurrentUser',
      'description' => 'Tests the current user integration',
      'group' => 'service_container',
    );
  }

  public function testCurrentUser() {
    /** @var \Drupal\service_container\Session\Account $account */

    $account = $this->container->get('current_user');
    $this->assertTrue($account instanceof Account);

    $admin_user = $this->drupalCreateUser(array('access content'));
    $this->drupalLogin($admin_user);
    drupal_save_session(FALSE);
    $GLOBALS['user'] = $admin_user;

    $this->assertEqual(array_keys($admin_user->roles), $account->getRoles());
    $this->assertEqual(1, count($account->getRoles(TRUE)));
    $this->assertEqual($admin_user->name, $account->getUsername());
  }

}

