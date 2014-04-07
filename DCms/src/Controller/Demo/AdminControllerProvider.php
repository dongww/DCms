<?php
/**
 * Created by dongww.
 * User: dongww
 * Date: 13-12-8
 * Time: 下午1:34
 */

namespace Controller\Demo;

use Silex\Application;
use Silex\ControllerCollection;
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
        /** @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers->get('', array($this, 'index'));
        $controllers->get('/{structure}/list/{page}', array($this, 'listContent'))
            ->value('page', 1);
        $controllers->get('/{structure}/content/{id}', array($this, 'getContent'));
        $controllers->get('/{structure}/edit', array($this, 'editContent'));
        $controllers->get('/{structure}/edit/{id}', array($this, 'editContent'));
        $controllers->get('/category/{name}', array($this, 'category'));
        $controllers->post('/category/add', array($this, 'addCategoryJson'));
        $controllers->post('/category/rename', array($this, 'renameCategoryJson'));
        $controllers->post('/category/move', array($this, 'moveCategoryJson'));

        return $controllers;
    }

    /**
     * 管理后台首页
     *
     * @param Application $app
     * @return mixed
     */
    public function index(Application $app)
    {
        return $app['twig']->render('demo/admin/index.twig');
    }

    /**
     * 内容列表
     *
     * @param Application $app
     * @param $structure string 内容结构名称
     * @param $page integer 页码
     * @return mixed
     */
    public function listContent(Application $app, $structure, $page)
    {
        return $app['twig']->render('demo/admin/content/list.twig', array(
            'structure' =>  $structure,
            'page'  =>  $page
        ));
    }

    /**
     * 单项内容页面
     *
     * @param Application $app
     * @param $structure string 内容结构名称
     * @param $id integer 内容id
     * @return mixed
     */
    public function getContent(Application $app, $structure, $id)
    {
        return $app['twig']->render('demo/admin/content/content.twig', array(
            'structure' =>  $app['structureConfig'][$structure],
            'structure_name' =>  $structure,
            'id'  =>  $id
        ));
    }

    /**
     * 编辑内容页面
     *
     * @param Application $app
     * @param Request $request
     * @param $structure string 内容结构名称
     * @return mixed
     */
    public function editContent(Application $app, Request $request, $structure)
    {
        return $app['twig']->render('demo/admin/content/edit.twig', array(
            'id' => $request->get('id'),
            'structure' => $structure
        ));
    }

    /**
     * 多级分类管理页面
     *
     * @param Application $app
     * @param $name
     * @return mixed
     */
    public function category(Application $app, Request $name)
    {
        return $app['twig']->render('demo/admin/category.twig', array(
            'name' => $name
        ));
    }

    /**
     * 通过 ajax 添加分类项
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
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

    /**
     * 重命名分类项
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
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

    /**
     * 移动分类项
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
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