<?php

namespace True9\Textmarketer\Exception;

use Exception;

class MissingHttpRequestMethodException extends Exception
{
    public function __construct()
    {
        parent::__construct("The request type must be specified! Valid options are POST and GET");
    }
}