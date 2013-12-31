<?php
/**
 * 基于 Silex 的简单 CMS 系统
 */
require_once __DIR__ . '/../vendor/autoload.php';

$app = new Core\Application();

$app->get('/', function () use ($app) {
//    $root = \R::load('cane',1);
//    $cs = $root->searchIn('ownCane');
//    print_r($cs);
    return $app['twig']->render('index.twig');
});

$app->run();