<?php
/**
 * User: dongweiwei
 * Date: 13-12-1
 * Time: 下午2:30
 */

namespace Core;

use Symfony\Component\Yaml\Yaml;

class Config
{
    /**
     * 配置文件路径
     *
     * @var string
     */
    protected $configPath;

    /**
     * 从配置文件中读取的，以数组形式呈现的配置内容
     *
     * @var array
     */
    protected $values;

    const MAIN_CONFIG = 1;

    public function __construct($configPath)
    {
        $this->configPath = $configPath;
        $this->values = array(
            'main' => null,
            'structure' => null,
        );
    }

    public function getMainConfig()
    {
        if (!$this->values['main']) {
            $this->values['main'] = Yaml::parse($this->configPath . '/main.yml');
        }
        return $this->values['main'];
    }

    /**
     * @return array
     */
    public function getStructureConfig()
    {
        if (!$this->values['structure']) {
            $this->values['structure'] = Yaml::parse($this->configPath . '/structure.yml');
        }
        return $this->values['structure'];
    }
} 