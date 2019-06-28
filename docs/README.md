# Textmarketer API Client

The True9 Textmarketer API Client (t9tmc) is a drop in, (almost) entirely dependency free client for interacting with the Textmarketer API.

You can view this documentation via [docsify](https://docsify.js.org/#/quickstart) for a much nicer experience;
```bash
npm i -g docsify-cli && docsify serve ./docs
```

## Prerequisites

- PHP 7.0+
- Curl extension for PHP
- Json extension for PHP
- XML extension for PHP

## Installation

This package isn't available publicly on Packagist, but can still be installed via Composer by adding the following to your `composer.json`

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/true9/textmarketer-api-library"
    }
]
```

After this has been added, simply run;

```bash
composer require true9/textmarketer-client
``` 

Autoloading is already configured internally on this package, no additional configuration required!

# Configuration

Included in the t9tcm is a powerful configuration engine designed to give you as much flexibility as you can possibly need to get your configuration into it.

Doesn't matter if your preferred flavour's configuration or convention - this library can support it.

## via Direct input

You can pass your configuration directly to the `ConfigStrategy` by passing `array` as the first argument, and an array as the second like so;

```php
$smsConfig = new ConfigRetrievalStrategy('array', [
        'username' => 'abc123',
        'password' => 'def456',
        'originator' => 'True9'
    ]
));
```

## via Configuration file

This method attempts to locate a `textmarketer.config.php` file located inside a `config` directory at the root of your project.

You may need to run a `chdir` early on in your bootstrapping process (like `public/index.php` kind of early) so t9tmc can work on the correct path.

The config file is very simple and looks like this;
```php
<?php

return [
    'username' => 'your-textmarketer-api-username',
    'password' => 'your-textmarketer-api-password',
    'originator' => 'short-string-to-display-as-sender-of-sms'
];
```

## via Environment settings

Lastly, you can set your configuration via the environment through the use of `putenv` like so;

```php
putenv('TRUE9_TEXTMARKETER_CLIENT_CONFIG=username=test&password=test);
```

# Usage

## Sending Messages

After you've configured your client, sending messages is very simple. All you need to do is instantiate a `ConfigStrategy` (or pull one out of a DI container if you're so inclined) and pass it to an `SmsSendRequest` object like so;

```php
/*
* Some additional operations are available via GET on the textmarketer API,
* so you can specify your request method if you need to access them.
*/ 
$request = new SendSmsRequest('post', [
    'to' => '01234567890',
    'message' => 'Hello, world!',
    'originator' => 'True9'
]);
```

# Feature Roadmap

- Get account balance
- Get message queue