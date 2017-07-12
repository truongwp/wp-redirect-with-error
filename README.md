## WordPress redirect with error

This class handles the error when redirecting to other URL in WordPress.
Use simple URL parameter and nonce, don't use SESSION or COOKIE which not advised in WordPress.

### How to use?

Just include file `class-truongwp-redirect-with-error.php` in your theme or plugin or use Composer to install.

```
composer require "truongwp/wp-redirect-with-error >= 0.1.1"
```

Then create a function to store class instance globally.

```php
<?php
function prefix_redirect_error() {
    static $errors = null;

    if ( ! $errors ) {
        $errors = new Truongwp_Redirect_With_Error();
    }

    return $errors;
}
```

You need to register all errors you use. This can be done by `register_error()` method.

```php
<?php
function prefix_redirect_error() {
    static $errors = null;

    if ( ! $errors ) {
        $errors = new Truongwp_Redirect_With_Error();
    }

    // $errors->register_error( 'error-code', 'error-message' );
    $errors->register_error( 'error-1', 'This is error 1' );
    $errors->register_error( 'error-2', 'This is error 2' );
    $errors->register_error( 'error-3', 'This is error 3' );

    return $errors;
}
```

To pass error to redirect URL. Instead of:
```php
<?php
wp_redirect( $url );
```

You need:

```php
<?php
// This will add error code and nonce to URL parameters.
$new_url = prefix_redirect_error()->add_error( $url, 'error-2' );
wp_redirect( $new_url );
```

To display error, use this code:

```php
<?php
// This check error code and nonce via URL parameters to get error and display.
prefix_redirect_error()->show_error();
```

To display only specific error, pass error code to `show_error()` method:

```php
<?php
prefix_redirect_error()->show_error( 'error-1' );
```

To change markup of error when displaying, use `set_template()` method:

```php
<?php
function prefix_redirect_error() {
    static $errors = null;

    if ( ! $errors ) {
        $errors = new Truongwp_Redirect_With_Error();
    }

    $errors->register_error( 'error-1', 'This is error 1' );
    $errors->register_error( 'error-2', 'This is error 2' );
    $errors->register_error( 'error-3', 'This is error 3' );

    // Set new error markup.
    $errors->set_template( '<p class="error error-%1$s">%2$s</p>' );

    return $errors;
}
```
You can also change some other value, read the code and comments in `class-truongwp-redirect-with-error.php` file.


### Contributing
Contributor: [@truongwp](https://truongwp.com)

Bug reports or Pull requests are welcome.
