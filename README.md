# MTN MoMo Laravel Package

A Laravel package for MTN Mobile Money API integration that supports Collections, Disbursements, and Remittances.

## Features

- Collections API for receiving payments
- Disbursements API for sending payments
- Remittances API for international transfers
- Token management and caching
- Account balance checking
- Account holder verification
- Exchange rate queries (for Remittances)

## Requirements

- PHP ^8.1
- Laravel ^10.0
- Guzzle ^7.8

## Installation

You can install the package via composer:

```bash
composer require angstrom/mtn-momo
```

The package will automatically register its service provider.

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Angstrom\MoMo\MoMoServiceProvider"
```

This will create a `config/momo.php` file. Update it with your MTN MoMo API credentials:

```php
return [
    'api_key' => env('MOMO_API_KEY'),
    'api_user' => env('MOMO_API_USER'),
    'api_base_url' => env('MOMO_API_BASE_URL', 'https://sandbox.momodeveloper.mtn.com'),
    'environment' => env('MOMO_ENVIRONMENT', 'sandbox'),
];
```

Add these variables to your `.env` file:

```env
MOMO_API_KEY=your-api-key
MOMO_API_USER=your-api-user
MOMO_API_BASE_URL=https://sandbox.momodeveloper.mtn.com
MOMO_ENVIRONMENT=sandbox
```

## Usage

### Collections

```php
use Angstrom\MoMo\Collections;

$collections = new Collections();

// Request to pay
$result = $collections->requestToPay(
    amount: 100,
    currency: 'EUR',
    externalId: 'unique-transaction-id',
    partyId: '256772123456', // Phone number
    partyIdType: 'MSISDN',
    payerMessage: 'Payment for order #123',
    payeeNote: 'Order #123'
);

// Check transaction status
$status = $collections->getTransactionStatus($result['referenceId']);

// Get account balance
$balance = $collections->getAccountBalance();

// Check if account holder is active
$status = $collections->checkAccountHolderStatus('256772123456');
```

### Disbursements

```php
use Angstrom\MoMo\Disbursements;

$disbursements = new Disbursements();

// Transfer money
$result = $disbursements->transfer(
    amount: 100,
    currency: 'EUR',
    externalId: 'unique-transaction-id',
    payeeId: '256772123456',
    payeeIdType: 'MSISDN',
    payerMessage: 'Salary payment',
    payeeNote: 'Salary for January'
);

// Check transfer status
$status = $disbursements->getTransferStatus($result['referenceId']);

// Get account balance
$balance = $disbursements->getAccountBalance();
```

### Remittances

```php
use Angstrom\MoMo\Remittances;

$remittances = new Remittances();

// International transfer
$result = $remittances->transfer(
    amount: 100,
    currency: 'EUR',
    externalId: 'unique-transaction-id',
    payeeId: '256772123456',
    payeeIdType: 'MSISDN',
    payerMessage: 'International transfer',
    payeeNote: 'Family support'
);

// Check transfer status
$status = $remittances->getTransferStatus($result['referenceId']);

// Get exchange rate
$rate = $remittances->getExchangeRate('EUR', 'UGX');
```

### Using the Facade

You can also use the provided facade:

```php
use Angstrom\MoMo\Facades\MoMo;

// Collections
$result = MoMo::collections()->requestToPay(...);

// Disbursements
$result = MoMo::disbursements()->transfer(...);

// Remittances
$result = MoMo::remittances()->transfer(...);
```

## Testing

```bash
composer test
```

## Security

If you discover any security-related issues, please email info@angstrom.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
