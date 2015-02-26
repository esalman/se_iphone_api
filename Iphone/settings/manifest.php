<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'iphone',
    'version' => '4.0.0',
    'path' => 'application/modules/Iphone',
    'meta' => 
    array (
      'title' => 'iPhone API',
      'description' => '',
      'author' => 'Booyamedia',
    ),
    'callback' => 
    array (
      'class' => 'Engine_Package_Installer_Module',
    ),
    'actions' => 
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'enable',
      4 => 'disable',
    ),
    'directories' => 
    array (
      0 => 'application/modules/Iphone',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/iphone.csv',
    ),
  ),
); ?>