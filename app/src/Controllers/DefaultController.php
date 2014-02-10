<?php
/**
 * User: dongww
 * Date: 14-2-10
 * Time: 上午10:47
 */

namespace Controllers;

use Core\Application;

class DefaultController
{
    public function indexAction(Application $app, $template)
    {
        return $app['twig']->render($template);
    }
} 