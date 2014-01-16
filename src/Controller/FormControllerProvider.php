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
use Intervention\Image\Image;

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
            //->method('GET|POST')
            ->bind('content_edit');
        $controllers->post('/ck_upload', array($this, 'ckUpload'))
            ->bind('ck_upload');

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

            $contentName = $request->request->get('structure');
            $content = \R::dispense($contentName);

            foreach ($app['structureConfig'][$contentName]['fields'] as $fieldName => $field) {
                switch ($field['type']) {
                    case 'image':
                        $file = new \Data\Image();
                        $fileName = $file->uploadFile($request->files->get($fieldName));
                        $content->$fieldName = $fileName;

                        $filePath = $file->getPath($fileName);
                        foreach ($field['size'] as $s) {
                            $img = Image::make($filePath);
                            $img->resize($s[0], $s[1], true);
                            $imgName = $s[0] . '_' . $s[1] . '_' . $fileName;
                            $img->save($file->getPath($imgName));
                        }
                        break;
                    default:
                        $content->$fieldName = $request->request->get($fieldName);
                }

            }

            /**
             * 关联
             */
            if ($app['structureConfig'][$contentName]['relations']) {
                foreach ($app['structureConfig'][$contentName]['relations'] as $relName => $rel) {
                    if ($request->request->get($relName)) {
                        switch ($rel['type']) {
                            case 'm2o':
                                $obj = \R::load($relName, $request->request->get($relName));
                                $content->$relName = $obj;
                                break;
                            case 'm2m':
                                $rels = \R::find($relName, 'id in (' . \R::genSlots($request->request->get($relName)) . ')',
                                    $request->request->get($relName));
                                $p = 'shared' . ucwords($relName);
                                $content->$p = $rels;
                                break;
                        }
                    }
                }
            }

            /**
             * 多级分类
             */
            if ($app['structureConfig'][$contentName]['relations']) {

            }

            \R::store($content);
        }
    }

    public function ckUpload(Request $request, Application $app)
    {
        $file = new \Data\Image();
        $url = $file->getUrl($file->uploadFile($request->files->get('upload')));
        $funcNum = $_GET['CKEditorFuncNum'];

        return "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($funcNum, '$url', '');</script>";
    }
} 