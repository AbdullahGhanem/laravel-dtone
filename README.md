# Laravel Dtone

[![Latest Stable Version](https://poser.pugx.org/ghanem/dtone/v/stable.svg)](https://packagist.org/packages/ghanem/dtone) [![License](https://poser.pugx.org/ghanem/dtone/license.svg)](https://packagist.org/packages/ghanem/dtone) [![Total Downloads](https://poser.pugx.org/ghanem/dtone/downloads.svg)](https://packagist.org/packages/ghanem/dtone)

A package that provides an interface between [Laravel](https://laravel.com) and [DT One DVS API](https://dvs-api-doc.dtone.com/#section/Overview).

## Requirements

- PHP ^7.2.5 | ^8.0
- Laravel ^7.0 | ^8.0 | ^9.0 | ^10.0 | ^11.0 | ^12.0

## Installation

- [Dtone on Packagist](https://packagist.org/packages/ghanem/dtone)
- [Dtone on GitHub](https://github.com/abdullahghanem/dtone)

You can install the package via composer:

```bash
composer require ghanem/dtone
```

Publish the config file:

```bash
php artisan vendor:publish --provider="Ghanem\Dtone\DtoneServiceProvider" --tag="config"
```

## Configuration

Add the following to your `.env` file:

```env
DTONE_KEY=your-production-api-key
DTONE_SECRET=your-production-api-secret
DTONE_TEST_KEY=your-sandbox-api-key
DTONE_TEST_SECRET=your-sandbox-api-secret
DTONE_IS_PRODUCTION=false
DTONE_RETRIES=0
DTONE_RETRY_DELAY=100
```

Set `DTONE_IS_PRODUCTION=true` when you are ready to use the production API.

### Retry Configuration

You can enable automatic retries for failed requests:

```env
DTONE_RETRIES=3
DTONE_RETRY_DELAY=100
```

This will retry failed requests up to 3 times with a 100ms delay between attempts.

## Usage

You can use the `Dtone` facade or resolve it from the container.

```php
use Ghanem\Dtone\Facades\Dtone;
```

### Services

```php
// List all services (with pagination)
$services = Dtone::services($page, $per_page);

// Get a service by ID
$service = Dtone::serviceById(1);
```

### Countries

```php
// List all countries
$countries = Dtone::countries($page, $per_page);

// Get a country by ISO code
$country = Dtone::countryByIsoCode('US');
```

### Operators

```php
// List all operators (optionally filter by country)
$operators = Dtone::operators('US', $page, $per_page);

// Get an operator by ID
$operator = Dtone::operatorById(5);

// Lookup operators by mobile number
$operators = Dtone::lookupOperatorsByMobileNumber('+1234567890');
```

### Products

```php
// List products with filters
$products = Dtone::products(
    $type,              // e.g. 'FIXED_VALUE_RECHARGE'
    $service_id,        // e.g. 1
    $country_iso_code,  // e.g. 'US'
    $benefit_types,     // e.g. ['Airtime']
    $page,
    $per_page
);

// Get a product by ID
$product = Dtone::productById(99);
```

### Campaigns

```php
// List active campaigns
$campaigns = Dtone::campaigns($page, $per_page);

// Get a campaign by ID
$campaign = Dtone::campaignById(7);
```

### Promotions

```php
// List promotions
$promotions = Dtone::promotions($page, $per_page);

// Get a promotion by ID
$promotion = Dtone::promotionById(3);
```

### Benefit Types

```php
// List all benefit types
$benefitTypes = Dtone::benefitTypes($page, $per_page);
```

### Balances

```php
$balances = Dtone::balances();
```

### Transactions

```php
// List transactions
$transactions = Dtone::transactions($page, $per_page);

// Get a transaction by ID
$transaction = Dtone::transactionById(456);

// Create a transaction (async)
$transaction = Dtone::createTransaction(
    'external-id-123',                      // external_id
    99,                                     // product_id
    ['mobile_number' => '+1234567890'],     // credit_party_identifier
    false                                   // auto_confirm (default: false)
);

// Create a transaction (sync - waits for completion)
$transaction = Dtone::createTransactionSync(
    'external-id-123',
    99,
    ['mobile_number' => '+1234567890'],
    true                                    // auto_confirm
);

// Confirm a transaction (async)
$confirmed = Dtone::confirmTransaction($transaction_id);

// Confirm a transaction (sync - waits for completion)
$confirmed = Dtone::confirmTransactionSync($transaction_id);

// Cancel a transaction
$cancelled = Dtone::cancelTransaction($transaction_id);
```

### Lookups

```php
// Lookup operators by mobile number
$operators = Dtone::lookupOperatorsByMobileNumber('+1234567890');

// Statement inquiry
$statement = Dtone::statementInquiry(
    99,                                     // product_id
    ['account_number' => '123456']          // credit_party_identifier
);

// Get remaining benefits for a credit party
$benefits = Dtone::creditPartyBenefits(
    99,                                     // product_id
    ['mobile_number' => '+1234567890']      // credit_party_identifier
);

// Get status for a credit party
$status = Dtone::creditPartyStatus(
    99,                                     // product_id
    ['mobile_number' => '+1234567890']      // credit_party_identifier
);
```

### Paginated Responses

List endpoints return a paginated response with `data` and `meta`:

```php
$response = Dtone::services(1, 10);

$response['data'];  // array of items
$response['meta'];  // pagination info
```

The `meta` array contains:

| Key | Description |
|-----|-------------|
| `total` | Total number of items |
| `total_pages` | Total number of pages |
| `per_page` | Items per page |
| `page` | Current page |
| `next_page` | Next page number |
| `prev_page` | Previous page number |

## Done

- [x] Services (list, get by ID)
- [x] Countries (list, get by ISO code)
- [x] Operators (list, get by ID)
- [x] Products (list with filters, get by ID)
- [x] Campaigns (list, get by ID)
- [x] Promotions (list, get by ID)
- [x] Benefit types (list)
- [x] Balances
- [x] Transactions (list, get by ID, create async/sync, confirm async/sync, cancel)
- [x] Lookup operators by mobile number
- [x] Statement inquiry lookup
- [x] Credit party lookup (remaining benefits, status)
- [x] Pagination support with meta data
- [x] Production / Sandbox environment switching
- [x] Retry mechanism for failed requests
- [x] Test suite
- [x] Support for Laravel 7 - 12

## Roadmap

- [ ] Response DTOs (typed objects instead of arrays)
- [ ] Webhook / Callback support
- [ ] Laravel notifications channel integration

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
