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
```

Set `DTONE_IS_PRODUCTION=true` when you are ready to use the production API.

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

### Balances

```php
$balances = Dtone::balances();
```

### Transactions

```php
// List transactions
$transactions = Dtone::transactions($page, $per_page);

// Create a transaction
$transaction = Dtone::createTransaction(
    'external-id-123',                      // external_id
    99,                                     // product_id
    ['mobile_number' => '+1234567890'],     // credit_party_identifier
    false                                   // auto_confirm (default: false)
);

// Confirm a transaction
$confirmed = Dtone::confirmTransaction($transaction_id);
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
- [x] Balances
- [x] Transactions (list, create async, confirm)
- [x] Lookup operators by mobile number
- [x] Pagination support with meta data
- [x] Production / Sandbox environment switching
- [x] Test suite (29 tests)
- [x] Support for Laravel 7 - 12

## Roadmap

- [ ] Campaigns (list, get by ID)
- [ ] Promotions (list, get by ID)
- [ ] Benefits types (list)
- [ ] Create transaction synchronously
- [ ] Confirm transaction synchronously
- [ ] Cancel transaction
- [ ] Get transaction by ID
- [ ] Statement inquiry lookup
- [ ] Credit party lookup (remaining benefits, status)
- [ ] Webhook / Callback support
- [ ] Rate limiting handling
- [ ] Retry mechanism for failed requests
- [ ] Response DTOs (typed objects instead of arrays)
- [ ] Laravel notifications channel integration

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
