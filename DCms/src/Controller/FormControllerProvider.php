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
            ->bind('content_edit');
        $controllers->post('/ck_upload', array($this, 'ckUpload'))
            ->bind('ck_upload');
        $controllers->get('/imagelist/delete/{content}/{id}', array($this, 'deleteImagelistJson'));
        $controllers->get('/delete/{content}/{id}', array($this, 'deleteContent'));

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
        $contentName = $request->request->get('structure');
        if ($request->request->get('id')) {
            $content = \R::load($contentName, $request->request->get('id'));
        } else {
            $content = \R::dispense($contentName);
            $date = new \DateTime();
            $content->created = $date->format('Y-m-d H:i:s');
        }
        /**
         * 基本字段
         */
        foreach ($app['structureConfig'][$contentName]['fields'] as $fieldName => $field) {
            switch ($field['type']) {
                case 'image':
                    if ($request->files->get($fieldName)) {
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
                    }
                    break;
                case 'imagelist':
                    if ($request->files->get($fieldName)) {
                        $file = new \Data\Image();

                        $fileNames = $file->uploadFiles($request->files->get($fieldName));
                        $imgTableName = 'own' . ucwords($fieldName);
                        foreach ($fileNames as $filename) {
                            $img = \R::dispense($fieldName);
                            $img->filename = $filename;
                            array_push($content->$imgTableName, $img);
                            $filePath = $file->getPath($filename);
                            foreach ($field['size'] as $s) {
                                $img = Image::make($filePath);
                                $img->resize($s[0], $s[1], true);
                                $imgName = $s[0] . '_' . $s[1] . '_' . $filename;
                                $img->save($file->getPath($imgName));
                            }
                        }
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
        if ($app['structureConfig'][$contentName]['category']) {
            foreach ($app['structureConfig'][$contentName]['category'] as $catName => $cat) {
                if ($request->request->get($catName)) {
                    $arr = array();
                    foreach ($request->request->get($catName) as $id) {
                        $s = explode('_', $id);
                        $arr[] = $s[count($s) - 1];
                    }
                    $cats = \R::find($catName, 'id in (' . \R::genSlots($arr) . ')',
                        $arr);
                    $p = 'shared' . ucwords($catName);
                    $content->$p = $cats;
                }
            }
        }

        \R::store($content);
        return self::redirect($request->server->get('HTTP_REFERER'));
    }

    /**
     * 删除内容项
     *
     * @param Application $app
     * @param Request $request
     * @param $contentName string 内容结构名称
     * @param $id
     */
    public function deleteContent(Application $app, Request $request, $content, $id)
    {
        $bean = \R::load($content, $id);
        \R::trash( $bean );
        return self::redirect($request->server->get('HTTP_REFERER'));
    }

    /**
     * 删除imagelist中的某一个图片
     *
     * @param Application $app
     * @param Request $request
     * @param $content
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteImagelistJson(Application $app, Request $request, $content, $id)
    {
        $success = false;
        $errorMessages = array();

        $bean = \R::load($content, $id);
        \R::trash($bean);

        return $app->json(array(
            'success' => true,
            'error' => $errorMessages
        ));
    }

    /**
     * 处理从 ckEditor 编辑器上传的图片
     *
     * @param Request $request
     * @param Application $app
     * @return string
     */
    public function ckUpload(Request $request, Application $app)
    {
        $file = new \Data\Image();
        $url = $file->getUrl($file->uploadFile($request->files->get('upload')));
        $funcNum = $_GET['CKEditorFuncNum'];

        return "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($funcNum, '$url', '');</script>";
    }

    public static function redirect($url, $info = '操作成功！')
    {
        $out = '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
            <script>alert("' . $info . '");window.location.href = "' . $url . '";</script>';
        return $out;
    }
} 