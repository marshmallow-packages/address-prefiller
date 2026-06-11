![alt text](https://marshmallow.dev/cdn/media/logo-red-237x46.png "marshmallow.")

# Laravel Address Prefiller

[![Latest Version on Packagist](https://img.shields.io/packagist/v/marshmallow/address-prefiller.svg?style=flat-square)](https://packagist.org/packages/marshmallow/address-prefiller)
[![Tests](https://img.shields.io/github/actions/workflow/status/marshmallow-packages/address-prefiller/php-syntax-checker.yml?branch=main&label=tests&style=flat-square)](https://github.com/marshmallow-packages/address-prefiller/actions/workflows/php-syntax-checker.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/marshmallow/address-prefiller.svg?style=flat-square)](https://packagist.org/packages/marshmallow/address-prefiller)
[![Issues](https://img.shields.io/github/issues/marshmallow-packages/address-prefiller)](https://github.com/marshmallow-packages/address-prefiller/issues)
[![License](https://img.shields.io/github/license/marshmallow-packages/address-prefiller)](https://github.com/marshmallow-packages/address-prefiller/blob/main/LICENSE.md)

This package prefills address fields based on a provided zipcode and house number. It currently only supports Dutch addresses. You can use it in your custom applications, or use the ready-made field with Laravel Nova.

The address data is retrieved from the official open API provided by the Dutch government (PDOK Locatieserver), which should contain the latest information.

## Installation

Install the package via Composer:

```bash
composer require marshmallow/address-prefiller
```

The service provider (`Marshmallow\Zipcode\FieldServiceProvider`) is auto-discovered by Laravel, so there is nothing else to register.

## Usage in Nova

A custom Laravel Nova field is available which you can use. It works very similarly to the Laravel Place field. The difference is that this field does not talk to Algolia, but to the official open API provided by the Dutch government, which should contain the latest information.

```php
use Marshmallow\Zipcode\Nova\Zipcode;

public function fields(Request $request)
{
    return [
        ID::make()->sortable(),
        $this->addressFields(),
    ];
}

protected function addressFields()
{
    return $this->merge([
        Zipcode::make(__('Zipcode prefiller'), __('Zipcode'), __('Housenumber'))
            /**
             * Let the package know which columns are connected to
             * the fields. The default values are commented after each
             * function call. If your column names match these defaults,
             * you don't need to call all these functions.
             */
            ->zipcode('zipcode')         // default: zipcode
            ->housenumber('housenumber') // default: housenumber
            ->street('street')           // default: street
            ->city('city')               // default: city
            ->province('province')       // default: province
            ->country('country')         // default: country
            ->latitude('latitude')       // default: latitude
            ->longitude('longitude'),    // default: longitude

        /**
         * The fields below will all be prefilled with the collected
         * data if we find a match on the submitted zipcode and house number.
         */
        Hidden::make(__('Zipcode'), 'zipcode')->hideFromIndex(),
        Hidden::make(__('Housenumber'), 'address_2')->hideFromIndex(),
        Text::make(__('Street'), 'address_1')->hideFromIndex(),
        Text::make(__('City'), 'city')->hideFromIndex(),
        Text::make(__('Province'), 'province')->hideFromIndex(),
        Country::make(__('Country'), 'country')->hideFromIndex(),
        Text::make(__('Latitude'), 'latitude')->hideFromIndex(),
        Text::make(__('Longitude'), 'longitude')->hideFromIndex(),
    ]);
}
```

The field exposes a fluent setter for each address attribute so you can map it to the column names used in your resource: `zipcode()`, `housenumber()`, `street()`, `city()`, `province()`, `country()`, `latitude()` and `longitude()`.

## Usage manually

We have provided an example of how you can use this functionality in your own application. We've currently only used this in the Nova setting, so if you're missing anything, please let us know!

```php
use Marshmallow\Zipcode\Facades\Zipcode;

return Zipcode::get(
    $request->zipcode,
    $request->housenumber
);
```

You can map the returned address fields to your own keys by chaining the setter methods before calling `get()`:

```php
use Marshmallow\Zipcode\Facades\Zipcode;

return Zipcode::street('address_1')
    ->city('city')
    ->province('province')
    ->country('country')
    ->latitude('latitude')
    ->longitude('longitude')
    ->get($request->zipcode, $request->housenumber);
```

## Testing

```bash
composer test
```

## Security Vulnerabilities

If you discover any security related issues, please email stef@marshmallow.dev instead of using the issue tracker.

## Credits

- [All Contributors](https://github.com/marshmallow-packages/address-prefiller/contributors)

## License

The MIT License (MIT). Please see the [License File](LICENSE.md) for more information.
