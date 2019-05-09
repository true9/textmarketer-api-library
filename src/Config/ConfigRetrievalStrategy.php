<?php

namespace True9\Textmarketer\Config;

use True9\Textmarketer\Config\Readers;

class ConfigRetrievalStrategy
{
    /**
     * The method that should be used to retrieve the config
     * @var $method
     */
    protected $method;

    /**
     * The loaded config
     * @var $config
     */
    protected $config;

    /**
     * The map of classes to methods for retrieving configs
     * @var $methodMap
     */
    protected $methodMap;

    public function __construct($method = null, array $value = null)
    {
        $this->setMethod($method);
        $this->setConfig($value);
        $this->setMethodMap([
            'file'  => Readers\ConfigFileReader::class,
            'env'   => Readers\ConfigEnvironmentReader::class
        ]);
    }

    public function __invoke()
    {
        if(!$this->getConfig())
        {
            $this->execute();
        }

        return $this->getConfig();
    }

    private function execute()
    {
        $classReference = null;
        $instance = null;

        if($this->getMethod())
        {
            $classReference = $this->getMethodMap()[$this->getMethod()];
        }

        $environmentSetting = getenv('TRUE9_TEXTMARKETER_CLIENT_CONFIG');
        if($environmentSetting)
        {
            $this->setMethod('env');
            $classReference = $this->getMethodMap()['env'];
        }

        $fileSetting = file_exists(getcwd() . '/config/textmarketer.config.php');
        if($fileSetting)
        {
            $this->setMethod('file');
            $classReference = $this->getMethodMap()['file'];
        }

        $instance = new $classReference();
        $config = $instance();
        $this->setConfig($config);
    }

    /**
     * @param string|null $method
     * @return $this
     */
    public function setMethod($method = null)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param array|null $config
     * @return $this
     */
    public function setConfig(array $config = null)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array $methodMap
     * @return $this
     */
    public function setMethodMap(array $methodMap)
    {
        $this->methodMap = $methodMap;
        return $this;
    }

    /**
     * @return array
     */
    public function getMethodMap()
    {
        return $this->methodMap;
    }
}