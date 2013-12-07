<?php
/**
 * Created by PhpStorm.
 * User: dongweiwei
 * Date: 13-12-4
 * Time: 下午6:37
 */

namespace Template;

use Core\Appliction;
use Silex\Application;

class Extension extends \Twig_Extension
{
    protected $app;

    public function __construct(Appliction $app)
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
        return 'silex_cms_extension';
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('simple_form', array($this, 'displayForm')),
        );
    }

    public function displayForm($name, $data = array())
    {
        //return 'haha:' . $name;
        $contentTypes = $this->app['config']->getContentTypesConfig();
        print_r($contentTypes);

        return $this->app['twig']->render('form/simple_form.twig', array(
            'form' => $contentTypes[$name],
            'data'  =>  $data
        ));

    }

} 