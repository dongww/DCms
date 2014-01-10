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
use Data\Category;

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
        $controllers->post('/category/add', array($this, 'addCategoryJson'));
        $controllers->post('/category/rename', array($this, 'renameJson'));

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

    public function addCategoryJson(Application $app, Request $request)
    {
        $success = false;
        $errorMessages = array();

        if ($request->get('name')) {
            $title = trim($request->get('title'));
            $category = new Category($request->get('name'));
            $s = explode('_', $request->get('selected'));
            $selectedId = $s[count($s) - 1];
            switch ($request->get('position')) {
                case 'child':
                    if ($category->addChildNode($selectedId, $title)) {
                        $success = true;
                    }
                    break;
                case 'pre':
                    if ($category->addPreNode($selectedId, $title)) {
                        $success = true;
                    }
                    break;
                case 'next':
                    if ($category->addNextNode($selectedId, $title)) {
                        $success = true;
                    }
                    break;
            }
        } else {
            $errorMessages[] = '数据结构名称为空';
        }

        return $app->json(array(
            'success' => $success,
            'error' => $errorMessages
        ));
    }

    public function renameJson(Application $app, Request $request)
    {
        $success = false;
        $errorMessages = array();

        if ($request->get('name')) {
            $title = trim($request->get('title'));
            $category = new Category($request->get('name'));
            $s = explode('_', $request->get('selected'));
            $selectedId = $s[count($s) - 1];

            if ($category->rename($selectedId, $title)) {
                $success = true;
            }
        } else {
            $errorMessages[] = '数据结构名称为空';
        }

        return $app->json(array(
            'success' => $success,
            'error' => $errorMessages
        ));
    }

} 