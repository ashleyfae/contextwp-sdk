# ContextWP SDK

A plug-and-play library to integrate with [ContextWP](https://contextwp.com)

## Usage

For tailored usage, see your Integration instructions within [ContextWP](https://contextwp.com).

More generic usage is as follows:

Include this package within your plugin, using Composer:

```
composer require ashleyfae/contextwp-sdk
```

Make sure your WordPress plugin is including the autoload file, if not already:

```php
require_once __DIR__.'/vendor/autoload.php';
```

Then register your product:

```php
add_action('contextwp_sdk_loaded', function (\ContextWP\SDK $sdk) {
    $sdk->register(
        (new \ContextWP\ValueObjects\Product('PUBLIC_KEY_HERE', 'PRODUCT_UUID_HERE'))
    );
});
```

Your public key and product UUID can be obtained from your ContextWP product dashboard.

### Collect your plugin's version number

By default, the SDK will include non-plugin-specific environment information, such as PHP version, WordPress version, and more. You can also include your plugin's version number in the data, by using the `setVersion()` method like so:

```php
add_action('contextwp_sdk_loaded', function (\ContextWP\SDK $sdk) {
    $sdk->register(
        (new \ContextWP\ValueObjects\Product('PUBLIC_KEY_HERE', 'PRODUCT_UUID_HERE'))
            ->setVersion($yourVersionHere)
    );
});
```

### Testing

If you want to test that the SDK is set up correctly and definitely running, you can run this WP-CLI command:

```
wp contextwp checkin
```

This is the output you should see if it's working correctly (UUIDs will differ):

```
Sending check-ins for PK: af272e18-bea7-42fd-b531-f898fbd55b25
Response code: 202
Response body: {"accepted":["ca9d46ca-d5b7-4a85-8411-aeec690a6d26"],"rejected":[]}
```
