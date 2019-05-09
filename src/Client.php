<?php

namespace True9\Textmarketer;

use True9\Textmarketer\Requests\SendRequest;

class Client
{
    public function __construct()
    {
        $request = new SendRequest(['username' => null]);
        die(dump('True9 Textmarketer Client Constructor Called'));
    }
}