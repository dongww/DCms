<?php
/**
 * 基于 Silex 的简单 CMS 系统
 */
$loader = require_once __DIR__ . '/../DCms/vendor/autoload.php';
$loader->add('', '../app/src');

$app = new Core\Application();
$app->run();