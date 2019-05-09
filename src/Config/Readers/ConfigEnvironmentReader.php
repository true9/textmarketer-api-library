<?php

namespace True9\Textmarketer\Config\Readers;

class ConfigEnvironmentReader implements ConfigReaderInterface
{
    public function __invoke()
    {
        $parts = explode('=', getenv('TRUE9_TEXTMARKETER_CLIENT_CONFIG'));
        parse_str(implode('=', $parts), $queryStr);

        return $queryStr;
    }
}