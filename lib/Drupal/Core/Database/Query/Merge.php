<?php

/**
 * @file
 * Contains \Drupal\Core\Database\Query\Merge.
 */

namespace Drupal\Core\Database\Query;

class Merge {

  /**
   * Returned by execute() if an INSERT query has been executed.
   */
  const STATUS_INSERT = 1;

  /**
   * Returned by execute() if an UPDATE query has been executed.
   */
  const STATUS_UPDATE = 2;

}

