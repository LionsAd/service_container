<?php

/**
 * @file
 * Contains \Drupal\service_container\UrlGenerator.
 */

namespace Drupal\service_container;

use Drupal\service_container\Legacy\Drupal7;

/**
 * Generates a URL out of a path and options.
 *
 * Wraps url().
 *
 * @codeCoverageIgnore
 */
class UrlGenerator {

  /**
   * The Drupal7 service.
   *
   * @var \Drupal\service_container\Legacy\Drupal7
   */
  protected $drupal7;

  /**
   * Constructs a new UrlGenerator instance.
   *
   * @param \Drupal\service_container\Legacy\Drupal7 $drupal7
   *   The Drupal7 service.
   */
  public function __construct(Drupal7 $drupal7) {
    $this->drupal7 = $drupal7;
  }

  /**
   * Generates an internal or external URL.
   *
   * When creating links in modules, consider whether l() could be a better
   * alternative than url().
   *
   * @param $path
   *   (optional) The internal path or external URL being linked to, such as
   *   "node/34" or "http://example.com/foo". The default value is equivalent to
   *   passing in '<front>'. A few notes:
   *   - If you provide a full URL, it will be considered an external URL.
   *   - If you provide only the path (e.g. "node/34"), it will be
   *     considered an internal link. In this case, it should be a system URL,
   *     and it will be replaced with the alias, if one exists. Additional query
   *     arguments for internal paths must be supplied in $options['query'], not
   *     included in $path.
   *   - If you provide an internal path and $options['alias'] is set to TRUE, the
   *     path is assumed already to be the correct path alias, and the alias is
   *     not looked up.
   *   - The special string '<front>' generates a link to the site's base URL.
   *   - If your external URL contains a query (e.g. http://example.com/foo?a=b),
   *     then you can either URL encode the query keys and values yourself and
   *     include them in $path, or use $options['query'] to let this function
   *     URL encode them.
   * @param $options
   *   (optional) An associative array of additional options, with the following
   *   elements:
   *   - 'query': An array of query key/value-pairs (without any URL-encoding) to
   *     append to the URL.
   *   - 'fragment': A fragment identifier (named anchor) to append to the URL.
   *     Do not include the leading '#' character.
   *   - 'absolute': Defaults to FALSE. Whether to force the output to be an
   *     absolute link (beginning with http:). Useful for links that will be
   *     displayed outside the site, such as in an RSS feed.
   *   - 'alias': Defaults to FALSE. Whether the given path is a URL alias
   *     already.
   *   - 'external': Whether the given path is an external URL.
   *   - 'language': An optional language object. If the path being linked to is
   *     internal to the site, $options['language'] is used to look up the alias
   *     for the URL. If $options['language'] is omitted, the global $language_url
   *     will be used.
   *   - 'https': Whether this URL should point to a secure location. If not
   *     defined, the current scheme is used, so the user stays on HTTP or HTTPS
   *     respectively. TRUE enforces HTTPS and FALSE enforces HTTP, but HTTPS can
   *     only be enforced when the variable 'https' is set to TRUE.
   *   - 'base_url': Only used internally, to modify the base URL when a language
   *     dependent URL requires so.
   *   - 'prefix': Only used internally, to modify the path when a language
   *     dependent URL requires so.
   *   - 'script': The script filename in Drupal's root directory to use when
   *     clean URLs are disabled, such as 'index.php'. Defaults to an empty
   *     string, as most modern web servers automatically find 'index.php'. If
   *     clean URLs are disabled, the value of $path is appended as query
   *     parameter 'q' to $options['script'] in the returned URL. When deploying
   *     Drupal on a web server that cannot be configured to automatically find
   *     index.php, then hook_url_outbound_alter() can be implemented to force
   *     this value to 'index.php'.
   *   - 'entity_type': The entity type of the object that called url(). Only
   *     set if url() is invoked by entity_uri().
   *   - 'entity': The entity object (such as a node) for which the URL is being
   *     generated. Only set if url() is invoked by entity_uri().
   *
   * @return string
   *   A string containing a URL to the given path.
   */
  public function url($path = NULL, array $options = array()) {
    return $this->drupal7->url($path, $options);
  }

}

