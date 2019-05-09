<?php

namespace True9\Textmarketer\Requests;

use True9\Textmarketer\Exception\ConfigValidationFailureException;
use True9\Textmarketer\Exception\MissingConfigException;

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
    protected $baseUrl = 'api.textmarketer.co.uk/gateway/';

    /**
     * The endpoint the request will be made to
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
     * AbstractRequest constructor.
     *
     * @param array|null $config
     * @throws ConfigValidationFailureException
     * @throws MissingConfigException
     */
    public function __construct($config = null)
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
        $this->setEndpoint($config['endpoint']);
        $this->setResponseType($responseType);

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
        $constructedUrl .= "?username=" . $this->getUsername();
        $constructedUrl .= "&password=" . $this->getPassword();
        $constructedUrl .= "&option=" . $this->getResponseType();

        $this->setUrl($constructedUrl);
        return $constructedUrl;
    }

    private function validateKeys(array $config)
    {
        $valid = true;
        $requiredKeys = ['endpoint', 'username', 'password'];
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
}