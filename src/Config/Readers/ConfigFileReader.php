<?php

namespace True9\Textmarketer\Config\Readers;

use True9\Textmarketer\Exception\MissingConfigException;

class ConfigFileReader implements ConfigReaderInterface
{
    public function __invoke()
    {
        $path = getcwd() . '/config/textmarketer.config.php';

        if(file_exists($path))
        {
            $config = require_once $path;
            return $config;
        }

        throw new MissingConfigException();
    }
}