# Phalcon User Auth 

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]


This library contains functions that manages the entire process of user creation, authentication, status update and password management.

## Install

Via Composer

``` bash
$ composer require cottacush/phalcon
```

```bash
$ PHINX_DBHOST=<host> PHINX_DBNAME=<database_name> PHINX_DBUSER=<username> PHINX_DBPASS=<password> ./vendor/bin/phinx migrate -e production -c ./vendor/cottacush/phalcon-user-auth/phinx.yml
```


## Features
- User registration  
- User authentication  
- Automatic Password Generation
- Password reset
- Authentication history

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email <developers@cottacush.com> instead of using the issue tracker.

## Credits

- Tega Oghenekohwo <tega@cottacush.com>
- Adeyemi Olaoye <yemi@cottacush.com>
- Adegoke Obasa <goke@cottacush.com>
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/cottacush/phalcon-user-auth.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/cottacush/phalcon-user-auth/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/cottacush/phalcon-user-auth.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/cottacush/phalcon-user-auth.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/cottacush/phalcon-user-auth.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/cottacush/phalcon-user-auth
[link-travis]: https://travis-ci.org/cottacush/phalcon-user-auth
[link-scrutinizer]: https://scrutinizer-ci.com/g/cottacush/phalcon-user-auth/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/cottacush/phalcon-user-auth
[link-downloads]: https://packagist.org/packages/cottacush/phalcon-user-auth
[link-author]: https://github.com/:author_username
[link-contributors]: ../../contributors
