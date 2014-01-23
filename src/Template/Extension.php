<?php
/**
 * User: dongweiwei
 * Date: 13-12-4
 * Time: 下午6:37
 */

namespace Template;

use Core\Application;
use Data\Category;

class Extension extends \Twig_Extension
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'd_cms_extension';
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('d_form', array($this, 'getForm'), array(
                'is_safe' => array('html')
            )),
            new \Twig_SimpleFunction('d_paging', array($this, 'getPaging'), array(
                'is_safe' => array('html')
            )),
            new \Twig_SimpleFunction('d_content', array($this, 'getContent')),
            new \Twig_SimpleFunction('d_parent', array($this, 'getParent')),
            new \Twig_SimpleFunction('d_shared', array($this, 'getShared')),
            new \Twig_SimpleFunction('d_own', array($this, 'getOwn')),
            new \Twig_SimpleFunction('d_imagelist', array($this, 'getImageList')),
            new \Twig_SimpleFunction('d_list', array($this, 'getList')),
            new \Twig_SimpleFunction('d_category', array($this, 'getCategory')),
        );
    }

    /**
     * 表单扩展函数
     *
     * @param string $name 指定生成表单的内容类型
     * @param string $tplFile 指定表单模板文件
     * @param array $data
     * @return string
     */
    public function getForm($name, $tplFile = 'form/simple_form.twig', $data = array())
    {
        return $this->app['twig']->render($tplFile, array(
            'form' => $this->app['structureConfig'][$name],
            'data' => $data,
            'path'   => $_SERVER["PHP_SELF"]
        ));
    }

    public function getPaging($path, $name, $page = 1, $limit = 10, $tplFile = 'demo/admin/content/paging.twig')
    {
        $count = \R::count($name);

        return $this->app['twig']->render($tplFile, array(
            'page'  =>  $page,
            'pages' =>  ceil($count / $limit),
            'path'  =>  $path
        ));
    }

    /**
     * 获取指定类型的数据
     * $options 的用法：\n
     * {'limit': 5, 'by': 'title'} 获取最后5条，以title倒序排。默认按 created倒序\n
     * {'limit': 5, 'page': 2, 'by': 'title', 'order': 'asc'} 每页5条，第2页，以public_time倒序排列\n
     * {'where': "category = ? and ...", 'values': [3,...]} 设置查询条件\n
     *
     * @param $name 类型
     * @param $options 选项
     * @return array
     */
    public function getList($name, $options = array())
    {
        $select = sprintf('select * from %s', $name);
        $o = $options;
        if ($o['limit']) {
            if ($o['page']) {
                $limit = sprintf(' limit %u, %u',
                    ($o['page'] - 1) * $o['limit'],
                    $o['limit']);
            } else {
                $limit = sprintf(' limit %u', $o['limit']);
            }
        }

        if ($o['by']) {
            $by = sprintf(' order by %s', $o['by']);
        } else {
            $by = ' order by created';
        }

        if ($o['order']) {
            $order = ' ' . $o['order'];
        } else {
            $order = ' desc';
        }

        $sql = $select . $by . $order . $limit;
        $rows = \R::getAll($sql);
        return \R::convertToBeans('author', $rows);
    }

    /**
     * 根据id获得一个内容
     *
     * @param $name
     * @param $id
     * @return \RedBean_OODBBean
     */
    public function getContent($name, $id)
    {
        return $data = \R::load($name, $id);
    }

    /**
     * 获得某个内容所属的关联内容，例如某个商品的分类
     *
     * @param $content
     * @param $parentName
     */
    public function getParent($content, $parentName)
    {
        return $content->$parentName;
    }

    public function getShared($content, $sharedName)
    {
        $name = 'shared' . ucwords($sharedName);
        return $content->$name;
    }

    public function getOwn($content, $ownName)
    {
        $name = 'own' . ucwords($ownName);
        return $content->$name;
    }

    public function getImageList($content, $imageList, $size = null)
    {
        $list = $this->getOwn($content, $imageList);
        $images = array();
        $file = new \Data\Image();

        $imgSize = $this->app['structureConfig'][$content->getMeta('type')]['fields'][$imageList]['size'][$size];
        foreach ($list as $i) {
            $images[] = array(
                'id' => $i->id,
                'filename' => $i->filename,
                'url' => $file->getUrl($i->filename, $imgSize[0] . '_' . $imgSize[1] . '_'),
            );
        }
        return $images;
    }

    /**
     * 获取分类视图
     *
     * @param $name
     * @param array $options
     */
    public function getCategory($name, $options = array())
    {
        $cate = new Category($name);
        echo $cate->getTreeView();
    }
} 