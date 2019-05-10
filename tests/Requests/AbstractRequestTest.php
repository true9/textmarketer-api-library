<?php

namespace True9\TextMarketerTest;

use PHPUnit\Framework\TestCase;
use True9\Textmarketer\Exception\ConfigValidationFailureException;
use True9\Textmarketer\Exception\MissingConfigException;
use True9\Textmarketer\Exception\MissingHttpRequestMethodException;
use True9\Textmarketer\Requests\SendSmsRequest;

class AbstractRequestTest extends TestCase
{
    public function testExceptionIsThrownIfConfigNotProvided()
    {
        $this->expectException(MissingConfigException::class);
        $request = new SendSmsRequest();
    }

    public function testExceptionMessageIsCorrectForMissingConfig()
    {
        try {
            $request = new SendSmsRequest();
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
        $request = new SendSmsRequest($config);
    }

    public function testMissingArrayKeysArePrintedInErrorMessage()
    {
        try {
            $request = new SendSmsRequest(['key' => 'val']);
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
            $request = new SendSmsRequest($config);
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
        $request = new SendSmsRequest($config);

        $this->assertEquals($request->getProtocol(), 'https');
        $this->assertEquals($request->getBaseUrl(), 'api.textmarketer.co.uk');
    }

    /**
     * @dataProvider validConfigArrayProvider
     * @param $config
     * @throws ConfigValidationFailureException
     * @throws MissingConfigException
     */
    public function testDefaultValuesCanBeOverridden($config)
    {
        $request = new SendSmsRequest($config);

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
    public function testUrlConstructionGeneratesCorrectUrl($config)
    {
        $request = new SendSmsRequest($config);
        $request->setEndpoint($config['endpoint']);

        $url = $request->constructUrl();
        $expectedUrl = "https://api.textmarketer.co.uk";
        $expectedUrl .= $config['endpoint'] . '/';
        $expectedUrl .= "?username={$config['username']}";
        $expectedUrl .= "&password={$config['password']}";
        $expectedUrl .= "&option={$config['response_type']}";

        $this->assertEquals($expectedUrl, $url);
    }

    /**
     * @dataProvider requestMethodProvider
     * @param $method
     * @throws ConfigValidationFailureException
     * @throws MissingConfigException
     * @throws MissingHttpRequestMethodException
     */
    public function testSendRequestThrowsExceptionIfMethodNotProvided($method)
    {
        $this->expectException(MissingHttpRequestMethodException::class);
        $sms = new SendSmsRequest(['username' => 'test', 'password' => 'test', 'endpoint' => '/xml-test', 'response_type' => 'xml']);
        $sms->sendRequest(null, []);
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
            [['username' => 'test', 'password' => 'test', 'endpoint' => '/json-test', 'response_type' => 'json']],
            [['username' => 'test', 'password' => 'test', 'endpoint' => '/xml-test', 'response_type' => 'xml']]
        ];
    }

    public static function requestMethodProvider()
    {
        return [
            ['GET'],
            ['POST'],
            ['gEt'],
            ['pOsT'],
            ['get'],
            ['post']
        ];
    }
}