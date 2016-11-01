# Unoconv Laravel API

A Laravel wrapper for unoconv as a webservice. See [Convert](https://github.com/EBOOST/Convert) for more details.

## Installation

Install this package through [Composer](https://getcomposer.org/). 

Add this to your `composer.json` dependencies:

### Using Laravel 5.2+

```js
"require": {
   "eboost/unoconv": "dev-master"
}
```

Run `composer install` to download the required files.

Next you need to add the service provider to `config/app.php`

```php
'providers' => [
    ...
    Eboost\Unoconv\UnoconvServiceProvider::class
]
```

Set up the [facade](http://laravel.com/docs/facades). Add the reference in `config/app.php` to your aliases array.

```php
'aliases'  => [
    ...
    'Unoconv' => Eboost\Unoconv\Facades\Unoconv::class,
]
```

Publish the config
```sh
php artisan vendor:publish --provider="Eboost\Unoconv\UnoconvServiceProvider" --tag="config"
```

## Usage

### File conversion
```php
# Convert the file to /file.pdf

Unoconv::file('/file.pptx')->to('pdf');
```

```php
# Convert the file and save it in a different location /new/location/file.pdf

Unoconv::file('/file.pptx')->to('/new/location/file.pdf');
```

#### Chaining multiple conversions
```php
# Convert the file to /file.pdf and /file.jpg

Unoconv::file('/file.pptx')->to(['pdf', 'jpg]);
```

```php
# Convert the file to /file.pdf and /preview/file.jpg

Unoconv::file('/file.pptx')->to(['pdf', '/preview/file.jpg]);
```

### Non-blocking conversion using a queue
To use queues you will need have set-up the default laravel queue listener.

```php
Unoconv::file('/file.pptx')->queue('pdf');
```

```php
# You can also specify the queue.

Unoconv::file('/file.pptx')->onQueue('image-converter', 'pdf');
```

### Dispatch new job after conversion is done
```php
Unoconv::file('/file.pptx')->after((new AfterConversionJob()))->to('pdf');
```
