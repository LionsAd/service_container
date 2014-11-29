#!/bin/bash
# @file
# Simple script to run the tests via travis-ci.

set -e
set -x

# Return if we should ignore this call.
[ -n "$IGNORE_DRUPAL_TRAVIS_INTEGRATION" ] && exit 0

cd "$TRAVIS_BUILD_DIR/../drupal_travis/drupal/"
{ php scripts/run-tests.sh "$@" || echo "1 fails"; } | tee /tmp/simpletest-result.txt

egrep -i -v -q "([0-9]+ fails)|(PHP Fatal error)|([0-9]+ exceptions)" /tmp/simpletest-result.txt
