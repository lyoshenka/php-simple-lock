# php-simple-lock

## Install

```JSON
"require": {
  "lyoshenka/php-simple-lock": "1.0"
}
```

or from the command-line:

```
composer require lyoshenka/php-simple-lock
```

## Usage

```PHP
use SimpleLock\Lock;

$lock = Lock::acquire('lockname');
if ($lock)
{
  // lock acquired. protected code goes here.

  Lock::release($lock);
}
```

If you want to change the location where lockfiles are stored:

```PHP
Lock::setLockDir('/path/to/lock/dir');
```

## Bugs/Fixes

File issues here, submit pull requests, or email me: alex@grin.io
