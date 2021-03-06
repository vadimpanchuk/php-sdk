<?php
// ensure we get report on all possible php errors
error_reporting(-1);
// require composer autoloader if available
$composerAutoload = __DIR__ . '/../vendor/autoload.php';
if (is_file($composerAutoload)) {
    require_once($composerAutoload);
} else {
    require_once(__DIR__ . '/../autoloader.php');
}
