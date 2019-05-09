<?php

namespace True9\Textmarketer\Requests;

interface RequestInterface
{
    public function setEndpoint($endpoint);
    public function getEndpoint();
}