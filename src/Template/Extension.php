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
            new \Twig_SimpleFunction('d_content', array($this, 'getContent')),
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
            'data' => $data
        ));
    }

    /**
     * 获取指定类型的数据
     * $options 的用法：\n
     * {'id': 123} 获取某一条id的数据\n
     * {'last': 5, 'by': 'public_time'} 获取最后5条，以public_time排序。默认按 id\n
     * {'per_page': 5, 'page': 2, 'by': 'public_time', 'order': 'desc'} 每页5条，第2页，以public_time倒序排列\n
     * {'where': "category = ? and ...", 'values': [3,...]} 设置查询条件\n
     *
     * @param $name 类型
     * @param $options 选项
     * @return array
     */
    public function getContent($name, $options = array())
    {
        /**
         * 如果 id 有值
         */
        if ($options['id'] > 0) {
            return \R::load($name, $options['id']);
        }

        if ($options['last']) {
            if ($options['by']) {

            }
        }

        return \R::findAll($name);
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