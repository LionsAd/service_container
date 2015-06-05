<?php

namespace Drupal\service_container_test_ctools\ServiceContainerTestCtoolsPlugin;

$plugin = array(
  'class' => '\\Drupal\\service_container_test_ctools\\ServiceContainerTestCtoolsPlugin\\ServiceContainerTestCtoolsPluginTest1'
);

class ServiceContainerTestCtoolsPluginTest1 {
  function beep() {
    return 'beep!';
  }
}
