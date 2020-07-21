<?php
define('DIST_FOLDER', __DIR__ . '/dist');
define('SRC_FOLDER', __DIR__ . '/src');
define('DEVELOPMENT_MODE', true);

if (!defined('BUILD_MODE')) {
    define('BUILD_MODE', false);
}

require __DIR__ . '/framework/kernel.php';

$init();