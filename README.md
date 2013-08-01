## Guardian

[![Build Status](https://travis-ci.org/theelphie/guardian.png?branch=master)](https://travis-ci.org/theelphie/guardian)

Laravel 4 user management package. Adding additional features to the Auth modules.

## Installation

Add the following into your `composer.json` file:

```json
{
	"require": {
		"elphie\guardian": "dev-master"
	}
}
```

Replace the Auth service provider in the `app/config/app.php` file

``` php
//'Illuminate\Auth\AuthServiceProvider',
Elphie\Guardian\GuardianServiceProvider,
```

Run `php artisan migrate --package="elphie\guardian". This will create a new user table. You are encourage to install this with a new fresh copy of Laravel.