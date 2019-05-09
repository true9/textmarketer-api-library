<?php

namespace True9\Textmarketer;

use True9\Textmarketer\Config\ConfigRetrievalStrategy;
use True9\Textmarketer\Requests\SendRequest;

class Client
{
    public function __construct($configRetrievalMethod = null, array $config = null)
    {
        $config = new ConfigRetrievalStrategy($configRetrievalMethod, $config);
        $request = new SendRequest($config());
    }
}