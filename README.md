# service\_container

Service Container is an API module based on ctools to enable a Drupal 7 quick and easy lightweight service container with 100% unit test coverage.

It is very similar in syntax to a Symfony container, but was written from scratch as a symfony dependency was not wanted - using some of Drupal 8 Core and Component directly. They will likely depend on a drupal8core project in the future - but for now the copy is fine.

This allows to use an extensible service container (like in Drupal 8) and write modules in Drupal 7 as if they were using Drupal 8.

The main benefit is being able to use unit testing.

service\_container uses PHP Unit and travis.yml, but the tests and a composer.json are isolated in the tests/ directory, so no vendor or composer multi map is needed by default.

It was originally written for the render\_cache module, but since then others have expressed interest in using it, so it was split it out and made a dependency of render\_cache instead.

You need:

- registry\_autoload

or any other PSR-4 autoloader.

### Registering CTools plugins

By default the service\_container supports ctools discovery, to register your plugins all you need to do is:

````php
    // Plugin Managers - filled out by alterDefinition() of service_container
    // module.
    // This needs to exist in an empty state.
    $services['render_cache.controller'] = array();

    // Syntax is: <service_name> => <plugin_manager_definition>
    $parameters['service_container.plugin_managers']['ctools'] = array(
      'render_cache.controller' => array(
        'owner' => 'render_cache',
        'type' => 'Controller',
      ),
    );
````

And you can then get a plugin via:

````php
    $rcc = \Drupal::service('render_cache.controller')->createInstance('block');
````

Because the plugin managers implement the whole discovery interface, you can get all definitions with ease.

````php
  $plugins = \Drupal::service('render_cache.controller')->getDefinitions();
````

Your plugin itself looks like:

cat modules/render_cache_block/src/RenderCache/Controller/block.inc

````php
$plugin = array(
  'class' => "\\Drupal\\render_cache_block\\RenderCache\\Controller\\BlockController",
  'arguments' => array('@render_stack', '@render_cache.cache'),
);
````

So you can use normal container parameter syntax.

### Provides the following services:

* module handler ('module_handler') and module installer ('module_installer')
* service container ('service_container')
* database ('database')
* key value store ('keyvalue', 'keyvalue.database')
* a wrapper for variable_get()  / variable_set() ('variable')
* A lock ('lock')
* A wrapper for url() / l() ('url_generator', 'link_generator')
* TODO

### Testing

- service\_container is tested via PHPUnit for code correctness.
- service\_container is tested via simpletest for integration with Drupal.

### Status

[![Build Status](https://travis-ci.org/LionsAd/service_container.svg?branch=7.x-1.x)](https://travis-ci.org/LionsAd/service_container)
[![Coverage Status](https://coveralls.io/repos/LionsAd/service_container/badge.png?branch=7.x-1.x)](https://coveralls.io/r/LionsAd/service_container?branch=7.x-1.x)
[![Gitter](https://badges.gitter.im/Join Chat.svg)](https://gitter.im/LionsAd/service_container?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
