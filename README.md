# service\_container

[![Build Status](https://travis-ci.org/LionsAd/service_container.svg?branch=7.x-1.x)](https://travis-ci.org/LionsAd/service_container)
[![Coverage Status](https://coveralls.io/repos/LionsAd/service_container/badge.png?branch=7.x-1.x)](https://coveralls.io/r/LionsAd/service_container?branch=7.x-1.x)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/LionsAd/service_container/badges/quality-score.png?b=7.x-1.x)](https://scrutinizer-ci.com/g/LionsAd/service_container/?branch=7.x-1.x)
[![Gitter](https://badges.gitter.im/Join Chat.svg)](https://gitter.im/LionsAd/service_container?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

# Versions

[![Latest Stable Version](https://poser.pugx.org/lionsad/service_container/v/stable)](https://packagist.org/packages/lionsad/service_container) [![Total Downloads](https://poser.pugx.org/lionsad/service_container/downloads)](https://packagist.org/packages/lionsad/service_container) [![Latest Unstable Version](https://poser.pugx.org/lionsad/service_container/v/unstable)](https://packagist.org/packages/lionsad/service_container) [![License](https://poser.pugx.org/lionsad/service_container/license)](https://packagist.org/packages/lionsad/service_container)

Service Container is an API module based on [ctools](https://www.drupal.org/project/ctools) to enable a Drupal 7 quick and easy lightweight service container with 100% unit test coverage.

It is very similar in syntax to a Symfony container, but was written from scratch as a symfony dependency was not wanted - using some of Drupal 8 Core and Component directly. They will likely depend on a drupal8core project in the future - but for now the copy is fine.

This allows to use an extensible service container (like in Drupal 8) and write modules in Drupal 7 as if they were using Drupal 8.

The main benefit is being able to use unit testing but also to write Drupal 7 module with Drupal 8 style of coding in mind.

The module uses PHP Unit and travis.yml, but the tests and a composer.json are isolated in the tests/ directory, so no vendor or composer multi map is needed by default.

It was originally written for the render\_cache module, but since then others have expressed interest in using it, so it was split it out and made a dependency of render\_cache instead.

You need:

- [registry\_autoload](https://www.drupal.org/project/registry_autoload)

or any other PSR-4 autoloader.

### Registering CTools plugins

By default the service\_container supports CTools discovery, to register your plugins all you need to do is:

````php
    $parameters['ctools_plugins_auto_discovery']['render_cache'] = TRUE
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
* variable, a wrapper for variable_get() / variable_set()
* A lock ('lock')
* A wrapper for url() / l() ('url_generator', 'link_generator')
* Flood, a wrapper for the flood mechanisms
* Messenger, a wrapper for displaying messages
* Drupal 7 Legacy, a wrapper for accessing the Drupal 7 legacy functions.
* More to come...

### Testing

- service\_container is tested via PHPUnit for code correctness. (run ./tests/run-tests.sh)
- service\_container is tested via simpletest for integration with Drupal. (run ./tests/run-simpletests.sh)
- service\_container is tested via PHPUnit for code coverage. (run ./tests/run-coverage.sh)

### List of Drupal 8 core services that we've altered

See the file HACK.md for more details.

### Examples of modules that use this module
* [render_cache 7.x-2.x](https://www.drupal.org/project/render_cache)
* [openlayers 7.x-3.x](https://www.drupal.org/project/openlayers)
* [vardumper 7.x-1.x](https://www.drupal.org/project/vardumper)
