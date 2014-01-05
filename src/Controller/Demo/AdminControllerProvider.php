<?php
/**
 * Created by dongww.
 * User: dongww
 * Date: 13-12-8
 * Time: 下午1:34
 */

namespace Controller\Demo;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Silex\ControllerProviderInterface;

/**
 * 示范性的后台功能
 *
 * Class AdminControllerProvider
 * @package Controller\Demo
 */
class AdminControllerProvider implements ControllerProviderInterface
{
    /**
     * Returns routes to connect to the given application.
     *
     * @param Application $app An Application instance
     *
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->get('', array($this, 'index'));
        $controllers->get('/news/edit', array($this, 'editNews'));
        $controllers->get('/news/edit/{id}', array($this, 'editNews'));
        $controllers->get('/category/{name}', array($this, 'category'));
        $controllers->post('/category/add', array($this, 'addCategory'));

        return $controllers;
    }

    public function index(Application $app, Request $request)
    {
        //return 'hello admin ' . $request->get('name');
        return $app['twig']->render('demo/admin/index.twig');
    }

    public function editNews(Application $app, Request $request)
    {
        return $app['twig']->render('demo/admin/news/edit.twig');
    }

    public function category(Application $app, Request $request, $name)
    {
        return $app['twig']->render('demo/admin/category.twig', array(
            'name' => $name
        ));
    }

    public function addCategory(Application $app, Request $request)
    {
        return $app->json(array(
            'success' => false
        ));
    }

} 