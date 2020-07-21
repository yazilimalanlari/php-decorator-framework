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
} else if($arg === 'clean dist') {
    $path = sprintf("%s/dist/", __DIR__);
    if (is_dir($path)) {
        shell_exec(sprintf("rm -r %s", $path));
    }
}



echo PHP_EOL;