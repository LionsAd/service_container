# service_container

Service Container is an API module based on ctools to enable a Drupal 7 quick and easy lightweight service container with 100% unit test coverage.

It is very similar in syntax to a Symfony container, but was written from scratch as a symfony dependency was not wanted - using some of Drupal 8 Core and Component directly. They will likely depend on a drupal8core project in the future - but for now the copy is fine.

This allows to use an extensible service container (like in Drupal 8) and write modules in Drupal 7 as if they were using Drupal 8.

The main benefit is being able to use unit testing.

service_container uses PHP Unit and travis.yml, but the tests and a composer.json are isolated in the tests/ directory, so no vendor or composer multi map is needed by default.

It was originally written for the render_cache module, but since then others have expressed interest in using it, so this will split it out.

You need:

- registry_autoload

or any other PSR-4 autoloader.

### Status

[![Build Status](https://travis-ci.org/LionsAd/service_container.svg?branch=7.x-1.x)](https://travis-ci.org/LionsAd/service_container)
[![Coverage Status](https://coveralls.io/repos/LionsAd/service_container/badge.png?branch=7.x-1.x)](https://coveralls.io/r/LionsAd/service_container?branch=7.x-1.x)
[![Gitter](https://badges.gitter.im/Join Chat.svg)](https://gitter.im/LionsAd/service_container?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
