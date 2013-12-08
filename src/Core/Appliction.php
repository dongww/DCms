<?php
/**
 * User: dongweiwei
 * Date: 13-12-1
 * Time: 下午2:18
 */

namespace Core;

use Silex\Application as baseApp;

class Appliction extends baseApp
{
    public function __construct()
    {
        parent::__construct();

        //error_reporting(E_ALL ^ E_NOTICE);

        $this['appPath'] = __DIR__ . '/../../app';
        $this['dataPath'] = __DIR__ . '/../../data';

        $this->regProviders();

        $this->setDebug($this['mainConfig']['debug']);

        if ($this['debug']) {
            if ($this['mainConfig']['display_notice']) {
                error_reporting(E_ALL);
            } else {
                error_reporting(E_ALL ^ E_NOTICE);
            }
        } else {
            error_reporting(0);
        }

        if ($this['debug']) {
            $this->mount('/demo/admin', new \Controller\Demo\AdminControllerProvider());
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
     * 注册核心服务
     */
    public function regProviders()
    {
        $this->initConfig();
        $this->initDatabase();
        $this->initForm();
        $this->initTemplate();
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
         * 读取主配置文件
         */
        $this['mainConfig'] = $this['config']->getMainConfig();
    }

    /**
     * RedBeanPHP 配置
     */
    public function initDatabase()
    {
        \R::setup($this['mainConfig']['db']['dsn'], $this['mainConfig']['db']['user'], $this['mainConfig']['db']['password']);
    }

    /**
     * Form 服务注册
     */
    public function initForm()
    {

        $this->register(new \Silex\Provider\FormServiceProvider());
        $this->register(new \Silex\Provider\ValidatorServiceProvider());
        $this->register(new \Silex\Provider\TranslationServiceProvider(), array(
            'translator.messages' => array(),
        ));
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
                'strict_variables' => false
            )
        ));

        $this['twig']->addExtension(new \Template\Extension($this));
    }

    /**
     * Url生成器服务注册
     */
    public function initUrlGenerator()
    {

        $this->register(new Silex\Provider\UrlGeneratorServiceProvider());
    }
} 