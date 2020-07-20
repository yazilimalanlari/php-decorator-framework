<?php

function clear_terminal() {
    echo shell_exec('clear');
}

array_shift($argv);


$arg = implode(' ', $argv);
clear_terminal();


if ($arg === 'server start') {
    shell_exec('php -S localhost:5000');
} else if ($arg === 'build') {
    define('BUILD_MODE', true);
    require __DIR__ . '/index.php';
}

echo PHP_EOL;