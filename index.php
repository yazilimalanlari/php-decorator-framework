<?php
define('DIST_FOLDER', __DIR__ . '/dist');
define('SRC_FOLDER', __DIR__ . '/src');

if (!defined('BUILD_MODE')) {
    define('BUILD_MODE', false);
}

require __DIR__ . '/framework/kernel.php';

$init();