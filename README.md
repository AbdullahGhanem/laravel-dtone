# Laravel Dtone

[![Latest Stable Version](https://poser.pugx.org/ghanem/dtone/v/stable.svg)](https://packagist.org/packages/ghanem/dtone) [![License](https://poser.pugx.org/ghanem/dtone/license.svg)](https://packagist.org/packages/ghanem/dtone) [![Total Downloads](https://poser.pugx.org/ghanem/dtone/downloads.svg)](https://packagist.org/packages/ghanem/dtone)

A package that provides an interface between [Laravel](https://laravel.com) and [DT One DVS API](https://dvs-api-doc.dtone.com/#section/Overview).

## Installation
- [Dtone on Packagist](https://packagist.org/packages/ghanem/dtone)
- [Dtone on GitHub](https://github.com/abdullahghanem/dtone)


You can install the package via composer:

```bash
composer require ghanem/dtone
```

now you need to publish the config file with:
```bash
php artisan vendor:publish --provider="Ghanem\Dtone\DtoneServiceProvider" --tag="config"
```