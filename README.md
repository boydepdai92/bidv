# BIDV Library
This is the library that supports the connection to the BIDV banking system.

**Note:** This library is only usable with **Laravel** and **Lumen**

# Installation
```bash
composer require 9pay/bidv
```

## Laravel
For Laravel version <= **5.4**

### For Wallet

Add to section providers of `config/app.php`:
```php
'providers' => [
    ...
    NinePay\Bidv\Providers\BankServiceProvider::class,
];
```
And add to aliases section:
```php
'aliases' => [
    ...
    'Bidv' => NinePay\Bidv\Facades\BidvFacade::class,
];
```

The library will use the data in the config file, so we need to publish config to use:
```bash
php artisan vendor:publish --provider="NinePay\Bidv\Providers\BankServiceProvider" --tag=config
```

### For Gate

Add to section providers of `config/app.php`:
```php
'providers' => [
    ...
    NinePay\Bidv\Providers\GateServiceProvider::class,
];
```
And add to aliases section:
```php
'aliases' => [
    ...
    'BidvGate' => NinePay\Bidv\Facades\GateFacade::class,
];
```

The library will use the data in the config file, so we need to publish config to use:
```bash
php artisan vendor:publish --provider="NinePay\Bidv\Providers\GateServiceProvider" --tag=config
```

## Lumen

### For Wallet

Open `bootstrap/app.php` and register the required service provider:
```php
$app->register(NinePay\Bidv\Providers\BankServiceProvider::class);
```
And register class alias:
```php
class_alias(NinePay\Bidv\Facades\BidvFacade::class, 'Bidv');
```

*Facades must be enabled.*

In Lumen, we can not create config file by Artisan CLI. So, you will create a config file with name `bidv_wallet.php` with content:
```php
return [
	'url'              => env('BIDV_WALLET_URL', ''),
	'service_id'       => env('BIDV_WALLET_SERVICE_ID', ''),
	'merchant_id'      => env('BIDV_WALLET_MERCHANT_ID', ''),
	'private_key_bidv' => env('BIDV_WALLET_SECRET_KEY', ''),
	'public_key_bidv'  => '/test/example_public_key.pem',
	'private_key_9pay' => '/test/example_private_key.pem',
];
```
And add it to `bootstrap/app.php`:
```php
$app->configure('bidv_wallet');
```

### For Gate

Open `bootstrap/app.php` and register the required service provider:
```php
$app->register(NinePay\Bidv\Providers\GateServiceProvider::class);
```
And register class alias:
```php
class_alias(NinePay\Bidv\Facades\GateFacade::class, 'BidvGate');
```

*Facades must be enabled.*

In Lumen, we can not create config file by Artisan CLI. So, you will create a config file with name `bidv_gate.php` with content:
```php
return [
	'url'              => env('BIDV_GATE_URL', ''),
	'service_id'       => env('BIDV_GATE_SERVICE_ID', ''),
	'merchant_id'      => env('BIDV_GATE_MERCHANT_ID', ''),
	'private_key_bidv' => env('BIDV_GATE_SECRET_KEY', ''),
	'public_key_bidv'  => '/test/example_public_key.pem',
	'private_key_9pay' => '/test/example_private_key.pem',
];
```
And add it to `bootstrap/app.php`:
```php
$app->configure('bidv_gate');
```

# Certificate
Library will use RSA to encrypt and decrypt so there should be 2 files `public_key` and `private_key`

# Methods

## Wallet
| **Name**  | **Method** |
| --------------------------- | ------------- |
| Connect Wallet with BIDV    | *\Bidv::link($param);*                 |
| Disconnet wallet with BIDV  | *\Bidv::unlink($param);*               |
| Withdraw wallet             | *\Bidv::wallet2Bank($param);*          |
| Deposit wallet              | *\Bidv::bank2Wallet($param);*          |
| Check account connect BIDV  | *\Bidv::checkLink($param);*            |
| Check OTP                   | *\Bidv::checkOtp($param);*             |
| Check Provider Balance      | *\Bidv::checkProviderBalance($param);* |
| Inquiry                     | *\Bidv::inquiry($param);*              |

## Gate
| **Name**  | **Method** |
| ----------| ------------- |
| Inquiry   | *\BidvGate::inquiry($param);*   |
| Verify    | *\BidvGate::verify($param);*    |
| Inittrans | *\BidvGate::inittrans($param);* |
| Refund    | *\BidvGate::refund($param);*    |

# Param
Input data for the above methods will not be included the following variables: `Service_Id`, `Merchant_Id`, `Secure_Code` because the library will be generated it.

# Response
The returned data will look like this:
```php
    array(
        'RESPONSE_CODE'   => '000',
        'MESSAGE'         => '',
        'IS_CORRECT_SIGN' => false,
        //...
    )
```
`RESPONSE_CODE` backs to **000** is successful. If not, it fail.<br/>
`MESSAGE` is description of result data.<br/>
`IS_CORRECT_SIGN` is a sign shows that returned data is valid.

# License
[MIT](https://choosealicense.com/licenses/mit/)






