<?php
define('BASE_PATH', __DIR__.'/');
define('BASE_URL', '');

define('CONFIGS_PATH', \BASE_PATH.'config/');
define('LIBS_PATH', \BASE_PATH.'libs/');

define('COMPONENTS_PATH', \BASE_PATH.'components/');
define('COMPONENTS_URL', \BASE_URL.'components/');

define('PLUGINS_PATH', \BASE_PATH.'plugins/');
define('PLUGINS_URL', \BASE_URL.'plugins/');

require \LIBS_PATH.'flat-core/autoload.inc';
require 'autoload.inc';

// --- Plugins --- //
//\Flat::loadPlugin('ExamplePlugin');
\Flat::loadPlugin('DataGrid');