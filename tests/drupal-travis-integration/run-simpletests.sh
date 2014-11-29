#!/bin/bash
# @file
# Simple script to run the tests via travis-ci.

set -e

cd "$TRAVIS_BUILD_DIR/../drupal_travis/drupal/"
php scripts/run-tests.sh "$@" | tee /tmp/simpletest-result.txt

# Simpletest does not exit with code 0 on success, so we will need to analyze
# the output to ascertain whether the tests passed.
TEST_EXIT=${PIPESTATUS[0]}
if [ $TEST_EXIT -ne 0 ]
then
  exit 1
fi

egrep -i -v -q "([0-9]+ fails)|(PHP Fatal error)|([0-9]+ exceptions)" /tmp/simpletest-result.txt
