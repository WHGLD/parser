<?php

unset($argv[0]);

spl_autoload_register(function (string $className) {
    require_once __DIR__ .'/../'. str_replace("\\", "/", $className) . '.php';
});

$className = '\\Cli\\Commands\\' . array_shift($argv) . 'Command';

$params = [];

foreach ($argv as $argument) {
    preg_match('/^-(.+)=(.+)$/', $argument, $matches);
    if (!empty($matches)) {
        $paramName = $matches[1];
        $paramValue = $matches[2];

        $params[$paramName] = $paramValue;
    }
}

$class = new $className($params);
$class->execute();