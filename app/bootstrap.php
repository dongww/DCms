<?php

error_reporting(E_ALL ^ E_NOTICE);

/**
 * 配置文件服务注册
 */
$app->register(new Provider\ConfigProvider(), array(
    'config.path' => __DIR__ . '/config'
));

/**
 * 读取主配置文件
 */
$mainConfig = $app['config']->getMainConfig();

$app['debug'] = $mainConfig['debug'];

/**
 * 数据库服务注册
 */
//$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
//    'db.options' => array(
//        'driver' => 'pdo_mysql',
//        'host' => $mainConfig['db']['host'],
//        'dbname' => $mainConfig['db']['dbname'],
//        'user' => $mainConfig['db']['username'],
//        'password' => $mainConfig['db']['password'],
//        'charset' => 'utf8',
//    ),
//));

/**
 * RedBeanPHP 配置
 */
R::setup($mainConfig['db']['dsn'], $mainConfig['db']['user'], $mainConfig['db']['password']);
//print_r($mainConfig['db']);
/**
 * 模板服务注册
 */
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/views',
    'twig.options' => array(
        'cache' => __DIR__ . '/../data/cache',
        'strict_variables' => false
    )
));

$app['twig']->addExtension(new Template\Extension($app));

/**
 * Url生成器服务注册
 */
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());