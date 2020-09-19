[![build status](https://api.travis-ci.org/paneidos/laravel-tagged-cache.svg?branch=master)](https://travis-ci.org/paneidos/laravel-tagged-cache)
# Laravel Tagged Cache

Force a certain tag on your cache.

## Why would I need this?

If you use the same Memcache server for multiple projects/tenants, it might be difficult to flush the cache for only one of them. The only options is to flush the entire cache. Using Laravel's built in `TaggableStore`, it's possible to flush the cache for only one project/tenant.

## How does it work?

The tagged cache driver is a simple wrapper for another cache driver. All regular access to cache keys, such as `get`, `put` and `remember`, are forced to use the `tags` method and include the specified tag. Other special methods, such as `lock` are passed through without modification.

# Installation

To get started with Laravel Tagged Cache, use Composer to add the package to your project's dependencies:

```
composer require paneidos/laravel-tagged-cache
```

## Config

Specify a tagged store in your `config/cache.php`:

```php
return [
    'stores' => [
        'memcache-tagged' => [
            'driver' => 'tagged',
            'backend' => 'memcache',
            'tag' => 'project_name',
        ],
        'memcache' => [
            // ...
        ],
        // ...
    ],
];
```

## Example: replace your default store

```php
return [
    'default' => 'tagged',
    
    'stores' => [
        'tagged' => [
            'driver' => 'tagged',
            'backend' => env('CACHE_DRIVER', 'array'),
            // Use the database name as the forced tag
            'tag' => env('DB_DATABASE', 'none'),
        ],
        // ...
    ],
];
```

# Compatibility

Currently works with Laravel 5.6 and higher.
The backend can be any store with support for tags. Note: the file store does *not* support tags.

# Development

```
# Install dependencies
composer install
# Run tests
composer test
# Run tests & report coverage
composer test -- --coverage-text
```

# Contributing

Send a pull request, ensure you've got test coverage for the new code.

# License

Laravel Tagged Cache is licensed under the MIT License.
