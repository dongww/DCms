<?php
/**
 * 定制的 Aplication。
 * 注册核心组件
 *
 * User: dongweiwei
 * Date: 13-12-1
 * Time: 下午2:18
 */

namespace Core;

use Silex\Application as baseApp;

/**
 * 定制的主程序类，继承自 Silex\Application
 *
 * Class Application
 * @package Core
 */
class Application extends baseApp
{
    public function __construct($appPath = '../app')
    {
        parent::__construct();

        //error_reporting(E_ALL ^ E_NOTICE);

        $this['appPath'] = $appPath;
        $this['dataPath'] = $this['appPath'] . '/data';

        $this->regProviders();

        if ($this['debug']) {
            if ($this['mainConfig']['display_notice']) {
                error_reporting(E_ALL ^ E_NOTICE);
            } else {
                error_reporting(E_ERROR);
            }
        } else {
            error_reporting(0);
        }
    }

    /**
     * 设置是否开启调试模式
     *
     * @param bool $type
     */
    public function setDebug($type = true)
    {
        $this['debug'] = $type;
    }

    /**
     * 注册核心组件
     */
    public function regProviders()
    {
        $this->initConfig();
        $this->setDebug($this['mainConfig']['debug']);
        $this->initDatabase();
        $this->initForm();
        $this->initTemplate();
        $this->initController();
    }

    /**
     * 配置文件服务注册
     */
    public function initConfig()
    {
        $this->register(new \Provider\ConfigProvider(), array(
            'config.path' => $this['appPath'] . '/config'
        ));

        /**
         * 读取主配置和内容类型配置
         */
        $this['mainConfig'] = $this['config']->getMainConfig();
        $this['structureConfig'] = $this['config']->getStructureConfig();
    }

    /**
     * RedBeanPHP 配置
     */
    public function initDatabase()
    {
        \R::setup($this['mainConfig']['db']['dsn'],
            $this['mainConfig']['db']['user'],
            $this['mainConfig']['db']['password']);
    }

    /**
     * Form 服务注册
     */
    public function initForm()
    {
    }

    /**
     * 模板服务注册
     */
    public function  initTemplate()
    {
        $this->register(new \Silex\Provider\TwigServiceProvider(), array(
            'twig.path' => $this['appPath'] . '/views',
            'twig.options' => array(
                'cache' => $this['dataPath'] . '/cache',
                'strict_variables' => false,
                'debug' => $this['debug']
            )
        ));

        $this['twig']->addExtension(new \Template\Extension($this));
    }

    /**
     * Url生成器服务注册
     */
    public function initUrlGenerator()
    {
        $this->register(new \Silex\Provider\UrlGeneratorServiceProvider());
    }

    /**
     * 注册核心控制器
     */
    public function initController()
    {
        $this->mount('/form', new \Controller\FormControllerProvider());
        $this->mount('/admin', new \Controller\Demo\AdminControllerProvider());
    }
} 