# JWT Middleware

A simple PSR-15 compatible JWT authentication middleware.

## Install

Via Composer 

```bash
$ composer require ideationnet/jwt-middleware
```

## Usage

Use with your favourite PSR-15 middleware dispatcher, 
like [Stack Runner](https://github.com/ideationnet/stack-runner).
Example configuration (using PHP-DI) where the options
are changed to add `/token` as a public path:

```php
return [
    JwtMiddleware::class => object()
        ->constructorParameter('options', ['public' => ['/token']]),
];
```

Options:

 * `key` is the JWT secret key, defaults to `JWT_SECRET` environment variable
 * `algorithms` is the array of supported algorithms, defaults to `['HS512']`
 * `public` is an array of paths that bypass authentication
 * `attribute` is the name of the attribute into which the decoded token is stored,
 defaults to `jwt-token`
 
The token is available in the `$request` object in middleware further
down the stack:

    $token = $request->getAttribute('jwt-token');

## Security

If you discover any security related issues, please email
darren@darrenmothersele.com instead of using the issue tracker.


## Credits

- [Darren Mothersele](http://www.darrenmothersele.com)
- [All Contributors](../../contributors)

## License

The MIT License. Please see [License File](License.md) for more information.

