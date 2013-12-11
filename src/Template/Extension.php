<?php
/**
 * User: dongweiwei
 * Date: 13-12-4
 * Time: 下午6:37
 */

namespace Template;

use Core\Application;

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
            new \Twig_SimpleFunction('d_form', array($this, 'displayForm'), array(
                'is_safe' => array('html')
            )),
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
    public function displayForm($name, $tplFile = 'form/simple_form.twig', $data = array())
    {
        return $this->app['twig']->render($tplFile, array(
            'form' => $this->app['contentTypesConfig'][$name],
            'data' => $data
        ));
    }
} 