<?php
/**
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
            new \Twig_SimpleFunction('simple_form', array($this, 'displaySimpleForm'), array(
                'is_safe' => array('html')
            )),
            new \Twig_SimpleFunction('symfony_form', array($this, 'displaySymfonyForm'), array(
                'is_safe' => array('html')
            )),
        );
    }

    public function displaySimpleForm($name, $data = array())
    {
        $contentTypes = $this->app['config']->getContentTypesConfig();

        return $this->app['twig']->render('form/simple_form.twig', array(
            'form' => $contentTypes[$name],
            'data' => $data
        ));

    }

    public function displaySymfonyForm($name, $data = array())
    {
        $contentTypes = $this->app['config']->getContentTypesConfig();

        $formBuilder = $this->app['form.factory']->createBuilder('form', $data);

        foreach ($contentTypes[$name]['fields'] as $fieldName => $field) {
            switch ($field['type']) {
                case 'text':
                    $formBuilder->add($fieldName, 'text', array(
                        'label' => $field['label']
                    ));
                    break;
                case 'textarea':
                    $formBuilder->add($fieldName, 'textarea', array(
                        'label' => $field['label']
                    ));
                    break;
                case 'image':
                    $formBuilder->add($fieldName, 'file', array(
                        'label' => $field['label']
                    ));
                    break;
            }
        }

        $form = $formBuilder->getForm();
        return $this->app['twig']->render('form/symfony_form.twig', array(
            'form' => $form->createView(),
        ));

    }

} 