<?php

namespace True9\Textmarketer\Requests;

use SimpleXMLElement;
use True9\Textmarketer\Exception\ConfigValidationFailureException;
use True9\Textmarketer\Exception\MissingConfigException;
use True9\Textmarketer\Exception\MissingHttpRequestMethodException;

abstract class AbstractRequest
{
    /**
     * Determines HTTP/HTTPS
     * @var $protocol
     */
    protected $protocol = 'https';

    /**
     * The base url that all request paths will be relative to
     * @var $baseUrl
     */
    protected $baseUrl = 'api.textmarketer.co.uk';

    /**
     * The endpoint to make the request to
     * @var $endpoint
     */
    protected $endpoint;

    /**
     * User's Textmarketer API username
     * @var $username
     */
    protected $username = null;

    /**
     * User's Textmarketer API password
     * @var $password
     */
    protected $password = null;

    /**
     * Determines whether the response should be in XML or JSON format, defaults to JSON
     * @var $responseType
     */
    protected $responseType = 'json';

    /**
     * The constructed URL made up of the various parts
     * @var $url
     */
    protected $url;

    /**
     * Additional parameters for the request
     * @var $params
     */
    protected $params = [];

    /**
     * AbstractRequest constructor.
     *
     * @param array|null $config
     * @param array $params
     * @throws ConfigValidationFailureException
     * @throws MissingConfigException
     */
    public function __construct($config = null, $params = [])
    {
        if($config == null)
        {
            throw new MissingConfigException("A config array must be provided when instantiating a request class");
        }

        $validatedKeys = $this->validateKeys($config);
        if(!$validatedKeys['is_valid'])
        {
            throw new ConfigValidationFailureException($validatedKeys['missing_keys'], $validatedKeys['invalid_keys']);
        }

        $responseType = (array_key_exists('response_type', $config) ? $config['response_type'] : 'json');

        $this->setUsername($config['username']);
        $this->setPassword($config['password']);
        $this->setResponseType($responseType);
        $this->setParams($params);

        $this->constructUrl($config);
    }

    /**
     * Assemble the various pieces into a full URL
     *
     * @param array $config - Config options to build this request with
     * @return string
     * @throws ConfigValidationFailureException
     */
    public function constructUrl()
    {
        $constructedUrl = $this->getProtocol() . '://';
        $constructedUrl .= $this->getBaseUrl();
        $constructedUrl .= $this->getEndpoint() . '/';

        foreach($this->getParams() as $k => $v)
        {
            $constructedUrl .= "&{$k}={$v}";
        }

        $this->setUrl($constructedUrl);
        return $constructedUrl;
    }

    /**
     * @param string|null $method
     * @param array $data
     * @return SimpleXMLElement|void
     * @throws MissingHttpRequestMethodException
     */
    public function sendRequest($method = null, array $data)
    {
        if($method == null)
        {
            throw new MissingHttpRequestMethodException();
        }

        switch(strtolower($method))
        {
            case 'get':
                return $this->sendGetRequest();
            case 'post':
                return $this->sendPostRequest($data);
        }
    }

    /**
     * Make a request to the constructed URL via HTTP GET
     *
     * @return SimpleXMLElement
     */
    private function sendGetRequest()
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->getUrl(),
            CURLOPT_RETURNTRANSFER => true
        ]);

        return simplexml_load_string(curl_exec($ch));
    }

    /**
     * Make a request to the constructed URL via HTTP POST
     *
     * @param array $data
     * @return SimpleXMLElement
     */
    private function sendPostRequest(array $data)
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->getUrl(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => count($data),
            CURLOPT_POSTFIELDS => $this->preparePostFields($data),
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded']
        ]);

        $result = curl_exec($ch);

        curl_close($ch);
        return simplexml_load_string($result);
    }

    private function preparePostFields(array $data)
    {
        $fields = null;

        $fields .= "username={$this->getUsername()}&";
        $fields .= "password={$this->getPassword()}&";

        foreach($data as $k => $v)
        {
            $fields .= $k.'='.$v.'&';
        }

        rtrim($fields, '&');
        return $fields;
    }

    private function validateKeys(array $config)
    {
        $valid = true;
        $requiredKeys = ['username', 'password'];
        $missingKeys = [];
        $invalidKeys = [];

        foreach($requiredKeys as $key)
        {
            if(!array_key_exists($key, $config))
            {
                $valid = false;
                $missingKeys[] = $key;
            }

            if(array_key_exists($key, $config) && (strlen($config[$key]) == 0 || is_null($config[$key])))
            {
                $valid = false;
                $invalidKeys[] = $key;
            }
        }

        return [
            'is_valid' => $valid,
            'missing_keys' => $missingKeys,
            'invalid_keys' => $invalidKeys
        ];
    }

    /**
     * @param string $protocol
     * @return $this
     */
    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;
        return $this;
    }

    /**
     * @return string
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * @param string $baseUrl
     * @return $this
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @param string $endpoint
     * @return $this
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
        return $this;
    }

    /**
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * @param string $username
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $responseType
     * @return $this
     */
    public function setResponseType($responseType)
    {
        $this->responseType = $responseType;
        return $this;
    }

    /**
     * @return string
     */
    public function getResponseType()
    {
        return $this->responseType;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    public function setParams(array $params)
    {
        $this->params = $params;
        return $this;
    }

    public function getParams()
    {
        return $this->params;
    }
}