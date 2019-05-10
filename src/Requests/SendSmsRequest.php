<?php

namespace True9\Textmarketer\Requests;

class SendSmsRequest extends AbstractRequest
{
    protected $endpoint = '/gateway';

    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
        return $this;
    }

    public function getEndpoint()
    {
        return $this->endpoint;
    }
}