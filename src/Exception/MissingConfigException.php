<?php

namespace True9\Textmarketer\Exception;

use Exception;

class MissingConfigException extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}