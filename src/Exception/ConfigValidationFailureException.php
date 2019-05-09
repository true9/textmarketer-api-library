<?php

namespace True9\Textmarketer\Exception;

use Exception;

class ConfigValidationFailureException extends Exception
{
    public function __construct(array $missingKeys, array $invalidKeys)
    {
        $message = "Provided config failed validation! ";

        if(count($missingKeys))
        {
            $message .= "The following keys were missing: " . implode(', ', $missingKeys);
        }

        if(count($invalidKeys))
        {
            $message .= "The following keys were found to be empty or null: " . implode(', ', $invalidKeys);
        }

        parent::__construct($message);
    }
}