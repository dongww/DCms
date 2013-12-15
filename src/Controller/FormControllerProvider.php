<?php
/**
 * 表单处理
 *
 * Created by dongww.
 * User: dongww
 * Date: 13-12-8
 * Time: 下午4:20
 */

namespace Controller;

use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Silex\ControllerProviderInterface;

class FormControllerProvider implements ControllerProviderInterface
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

        $controllers->post('/edit', array($this, 'edit'))
            ->method('GET|POST')
            ->bind('content_edit');

        return $controllers;
    }


    /**
     * 新建或编辑内容
     *
     * @param Application $app
     * @param Request $request
     */
    public function edit(Application $app, Request $request)
    {
        if ($request->request->get('id')) {
            //todo:id存在，为修改模式
        } else {
            //id不存在，为新建模式

            $contentName = $request->request->get('content_type');
            $content = \R::dispense($contentName);

            foreach ($app['contentTypesConfig'][$contentName]['fields'] as $fieldName => $field) {
                $content->$fieldName = $request->request->get($fieldName);
            }

            foreach ($app['contentTypesConfig'][$contentName]['relations'] as $relName => $rel) {
                if ($request->request->get($relName)) {
                    if ($rel['type'] == 'm2o') {
                        $obj = \R::load($relName, $request->request->get($relName));
                        $content->$relName = $obj;
                    }
                }
            }

            \R::store($content);
        }
    }
} 