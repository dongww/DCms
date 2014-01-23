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
        $controllers->get('/{structure}/list/{page}', array($this, 'listContent'))
            ->value('page', 1);
        $controllers->get('/{structure}/edit', array($this, 'editContent'));
        $controllers->get('/{structure}/edit/{id}', array($this, 'editContent'));
        $controllers->get('/category/{name}', array($this, 'category'));
        $controllers->post('/category/add', array($this, 'addCategoryJson'));
        $controllers->post('/category/rename', array($this, 'renameCategoryJson'));
        $controllers->post('/category/move', array($this, 'moveCategoryJson'));

        return $controllers;
    }

    public function index(Application $app, Request $request)
    {
        return $app['twig']->render('demo/admin/index.twig');
    }

    public function listContent(Application $app, Request $request, $structure, $page)
    {
        return $app['twig']->render('demo/admin/content/list.twig', array(
            'structure' =>  $structure,
            'page'  =>  $page
        ));
    }

    public function editContent(Application $app, Request $request, $structure)
    {
        return $app['twig']->render('demo/admin/content/edit.twig', array(
            'id' => $request->get('id'),
            'structure' => $structure
        ));
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

    public function renameCategoryJson(Application $app, Request $request)
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

    public function moveCategoryJson(Application $app, Request $request)
    {
        $success = false;
        $errorMessages = array();

        if ($request->get('name')) {
            $category = new Category($request->get('name'));
            $s = explode('_', $request->get('selected'));
            $selectedId = $s[count($s) - 1];
            $p = explode('_', $request->get('parent'));
            $parentId = $p[count($p) - 1] == '#' ? null : $p[count($p) - 1];

            if ($category->move($selectedId, $parentId, $request->get('position') + 1)) {
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