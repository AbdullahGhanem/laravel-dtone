# Laravel Dtone

[![Latest Stable Version](https://poser.pugx.org/ghanem/dtone/v/stable)](https://packagist.org/packages/ghanem/dtone) [![Total Downloads](https://poser.pugx.org/ghanem/dtone/downloads)](https://packagist.org/packages/ghanem/dtone) [![Latest Unstable Version](https://poser.pugx.org/ghanem/dtone/v/unstable)](https://packagist.org/packages/ghanem/dtone) [![License](https://poser.pugx.org/ghanem/dtone/license)](https://packagist.org/packages/ghanem/dtone)

A package that provides an interface between [Laravel](https://laravel.com/docs/8.x) and [Dtone API](https://dvs-api-doc.dtone.com/#section/Overview), includes Gifs.

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