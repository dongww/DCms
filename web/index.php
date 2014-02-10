<?php
/**
 * 基于 Silex 的简单 CMS 系统
 */
require_once __DIR__ . '/../DCms/vendor/autoload.php';

$app = new Core\Application();

$app->get('/', function () use ($app) {
    return $app['twig']->render('index.twig');
});

$app->run();