<?php
/**
 * User: dongweiwei
 * Date: 13-12-1
 * Time: 下午2:23
 */

namespace Provider;

use Silex\ServiceProviderInterface;
use Silex\Application;
use Core\Config;

/**
 * 处理配置的 Silex Provider
 *
 * Class ConfigProvider
 * @package Provider
 */
class ConfigProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['config'] = $app->share(function ($app) {
            return new Config($app['config.path']);
        });
    }

    public function boot(Application $app)
    {

    }


} 