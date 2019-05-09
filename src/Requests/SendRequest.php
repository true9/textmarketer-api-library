<?php

namespace True9\Textmarketer\Requests;

class SendRequest extends AbstractRequest
{
    protected $endpoint = 'sms';

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