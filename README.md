# CareHQ PHP API client

CareHQ API Client for PHP.


## Composer installation

You can install the bindings via [Composer](https://getcomposer.org/). Run the
following command:

```
composer require CareHQ/carehq-php
```

To use the bindings, use Composer's
[autoload](https://getcomposer.org/doc/01-basic-usage.md#autoloading):

```
require_once('vendor/autoload.php');
```

## Manual installation

If you do not wish to use Composer, you can download the
[latest release](https://github.com/CareHQ/carehq-php/releases). Then, to use
the bindings, include the `init.php` file.

```
require_once('/path/to/carehq-php/init.php');
```

## Requirements

- PHP 5.6.0+
- [rmccue/requests aka WpOrg\Requests](https://github.com/WordPress/Requests)

# Usage

```PHP

require_once('vendor/autoload.php');


$api_client = new CareHQ\APIClient(
    'MY_ACCOUNT_ID',
    'MY_API_KEY',
    'MY_API_SECRET'
);

$users = $api_client->request(
    'get',
    'users',
    [
        'attributes' => [
            'first_name',
            'last_name'
        ],
        'filters-q' => 'ant'
    ]
);

```

# Changelog

- 2025-01-07: Updated to use the new 'CareHQ-Signature-Version 2.0' hashing mechanism (SHA256 with a nonce), and bring more in line with the [Python version](https://github.com/CareHQ/carehq-python).