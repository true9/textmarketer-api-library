<?php

namespace True9\TextMarketerTest;

use PHPUnit\Framework\TestCase;
use True9\Textmarketer\Exception\ConfigValidationFailureException;
use True9\Textmarketer\Exception\MissingConfigException;
use True9\Textmarketer\Requests\SendRequest;

class AbstractRequestTest extends TestCase
{
    public function testExceptionIsThrownIfConfigNotProvided()
    {
        $this->expectException(MissingConfigException::class);
        $request = new SendRequest();
    }

    public function testExceptionMessageIsCorrectForMissingConfig()
    {
        try {
            $request = new SendRequest();
        } catch (MissingConfigException $e) {
            $this->assertEquals("A config array must be provided when instantiating a request class", $e->getMessage());
        }
    }

    /**
     * @dataProvider invalidConfigArrayProvider
     * @param $config
     * @throws ConfigValidationFailureException
     * @throws MissingConfigException
     */
    public function testExceptionIsThrownIfInvalidConfigIsProvided($config)
    {
        $this->expectException(ConfigValidationFailureException::class);
        $request = new SendRequest($config);
    }

    public function testMissingArrayKeysArePrintedInErrorMessage()
    {
        try {
            $request = new SendRequest(['key' => 'val']);
        } catch (ConfigValidationFailureException $e) {
            $message = "Provided config failed validation! ";
            $message .= "The following keys were missing: ";
            $message .= "username, password";

            $this->assertEquals($message, $e->getMessage());
        }
    }

    /**
     * @dataProvider invalidConfigArrayProvider
     * @param $config
     * @throws MissingConfigException
     */
    public function testInvalidArrayKeysArePrintedInErrorMessage($config)
    {
        try {
            $request = new SendRequest($config);
        } catch (ConfigValidationFailureException $e) {
            $message = "Provided config failed validation! ";
            $message .= "The following keys were found to be empty or null: ";
            $message .= "username, password";

            $this->assertEquals($message, $e->getMessage());
        }
    }

    /**
     * @dataProvider validConfigArrayProvider
     * @param $config
     * @throws ConfigValidationFailureException
     * @throws MissingConfigException
     */
    public function testDefaultValuesAreSet($config)
    {
        $request = new SendRequest($config);

        $this->assertEquals($request->getProtocol(), 'https');
        $this->assertEquals($request->getBaseUrl(), 'api.textmarketer.co.uk/gateway/');
    }

    /**
     * @dataProvider validConfigArrayProvider
     * @param $config
     * @throws ConfigValidationFailureException
     * @throws MissingConfigException
     */
    public function testDefaultValuesCanBeOverridden($config)
    {
        $request = new SendRequest($config);

        $request->setProtocol('http');
        $request->setBaseUrl('google.com');

        $this->assertEquals($request->getProtocol(), 'http');
        $this->assertEquals($request->getBaseUrl(), 'google.com');
        $this->assertEquals($request->getResponseType(), $config['response_type']);
    }

    /**
     * @dataProvider validConfigArrayProvider
     * @param $config
     * @throws ConfigValidationFailureException
     * @throws MissingConfigException
     */
    public function testUrlConstruction($config)
    {
        $request = new SendRequest($config);
        $request->setEndpoint($config['endpoint']);

        $url = $request->constructUrl();
        $expectedUrl = "https://api.textmarketer.co.uk/gateway/";
        $expectedUrl .= $config['endpoint'] . '/';
        $expectedUrl .= "?username={$config['username']}";
        $expectedUrl .= "&password={$config['password']}";
        $expectedUrl .= "&option={$config['response_type']}";

        $this->assertEquals($expectedUrl, $url);
    }

    public static function invalidConfigArrayProvider()
    {
        return [
            [['username' => '', 'password' => '', 'endpoint' => '', 'response_type' => '']],
            [['username' => null, 'password' => null, 'endpoint' => null, 'response_type' => null]]
        ];
    }

    public static function validConfigArrayProvider()
    {
        return [
            [['username' => 'test', 'password' => 'test', 'endpoint' => 'json-test', 'response_type' => 'json']],
            [['username' => 'test', 'password' => 'test', 'endpoint' => 'xml-test', 'response_type' => 'xml']]
        ];
    }
}