<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = new Core\Appliction();

require_once __DIR__ . '/../app/bootstrap.php';

$app->get('/', function () use ($app) {
    return $app['twig']->render('index.twig');
});

$app->get('/setup', function() use($app){
    $book = R::dispense('book');
    $book->title = 'Gifted Programmers';
    $book->author = 'Charles Xavier';

    $id = R::store($book);
});

$app->run();