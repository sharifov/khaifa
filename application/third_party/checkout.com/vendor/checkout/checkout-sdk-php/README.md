<p align="center"><img src="https://www.checkout.com/static/img/checkout-logo/logo.svg" width="380"></p>


The **Checkout SDK for PHP** enables developers to easily work with Checkout.com APIs.
It requires PHP 5.6.

## Getting Help

If you encounter a bug with Checkout SDK for PHP please search the existing issues and try to make sure your problem doesn’t already exist before opening a new issue.
The GitHub issues are intended for bug reports and feature requests. For help and questions with using Checkout SDK for PHP please contact our integration support team.

For full usage details, see the [Wiki](https://github.com/checkout/checkout-sdk-php/wiki).


## Installation

### Installation with Composer (Recommended)
Either run the following command in the root directory of your project:
```bash
composer require checkout/checkout-sdk-php
```

Or require the Checkout.com package inside the composer.json file of your project:
```php
"require": { "php": ">=5.6", "checkout/checkout-sdk-php": "1.0.0"}.
```

### Clone repository
Alternatively you can clone the repository from GitHub with git clone
```bash
git clone git@github.com:checkout/checkout-sdk-php.git
```

## Quickstart

Include a `checkout-sdk-php/checkout.php` to access the operations for each API:

```php
use Checkout\CheckoutApi;
use Checkout\Models\Tokens\Card;
use Checkout\Models\Payments\TokenSource;
use Checkout\Models\Payments\Payment;

// Set the secret key
$secretKey = 'sk_test_key';

// Initialize the Checkout API
$checkout = new CheckoutApi($secretKey);


// Create a Card token
$card = new Card('4242424242424242', 12, 2020);
$card->cvv = 100;
$token = $checkout->tokens()->request($card);


// Create a payment method instance with card details
$method = new TokenSource($token->getId());

// Prepare the payment parameters
$payment = new Payment($method, 'GBP');
$payment->amount = 1000; // = 10.00

// Send the request and retrieve the response
$response = $checkout->payments()->request($payment);
```


## Tests
Install PHPUnit by running `composer require --dev phpunit/phpunit` and execute the tests with `./vendor/bin/phpunit`.
