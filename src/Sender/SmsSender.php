<?php

namespace True9\Textmarketer\Sender;

class SmsSender
{
    protected $request;

    /**
     * @param AbstractRequest $request
     * @return $this
     */
    public function setRequest(AbstractRequest $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @return AbstractRequest
     */
    public function getRequest()
    {
        return $this->request;
    }


}