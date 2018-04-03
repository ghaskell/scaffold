# Scaffold

[![Build Status](https://travis-ci.org/ghaskell/scaffold.svg?branch=master)](https://travis-ci.org/ghaskell/scaffold)
[![styleci](https://styleci.io/repos/CHANGEME/shield)](https://styleci.io/repos/CHANGEME)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ghaskell/scaffold/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ghaskell/scaffold/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/CHANGEME/mini.png)](https://insight.sensiolabs.com/projects/CHANGEME)
[![Coverage Status](https://coveralls.io/repos/github/ghaskell/scaffold/badge.svg?branch=master)](https://coveralls.io/github/ghaskell/scaffold?branch=master)

[![Packagist](https://img.shields.io/packagist/v/ghaskell/scaffold.svg)](https://packagist.org/packages/ghaskell/scaffold)
[![Packagist](https://poser.pugx.org/ghaskell/scaffold/d/total.svg)](https://packagist.org/packages/ghaskell/scaffold)
[![Packagist](https://img.shields.io/packagist/l/ghaskell/scaffold.svg)](https://packagist.org/packages/ghaskell/scaffold)

Package description: CHANGE ME

## Installation

Install via composer
```bash
composer require ghaskell/scaffold
```

### Register Service Provider

**Note! This and next step are optional if you use laravel>=5.5 with package
auto discovery feature.**

Add service provider to `config/app.php` in `providers` section
```php
Ghaskell\Scaffold\ServiceProvider::class,
```

### Register Facade

Register package facade in `config/app.php` in `aliases` section
```php
Ghaskell\Scaffold\Facades\Scaffold::class,
```

### Publish Configuration File

```bash
php artisan vendor:publish --provider="Ghaskell\Scaffold\ServiceProvider" --tag="config"
```

## Usage

CHANGE ME

## Security

If you discover any security related issues, please email 
instead of using the issue tracker.

## Credits

- [](https://github.com/ghaskell/scaffold)
- [All contributors](https://github.com/ghaskell/scaffold/graphs/contributors)

This package is bootstrapped with the help of
[melihovv/laravel-package-generator](https://github.com/melihovv/laravel-package-generator).
