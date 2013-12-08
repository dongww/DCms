<?php
/**
 * 基于 Silex 的简单 CMS 系统
 */
require_once __DIR__ . '/../vendor/autoload.php';

$app = new Core\Appliction();

$app->get('/', function () use ($app) {
    return $app['twig']->render('index.twig');
});

$app->run();