# Encryptable

It is a simple trait that allows you to encrypt and decrypt on the fly multiple fields in your Eloquent models.

And no, it's not just another encryption package for Eloquent. This is designed to solve what others can not do: decrypt the answers by API, especially in Lumen.

## Install on Laravel/Lumen 5.5+

```
composer require attakinsky/encryptable
```

## Usage

1. Import on any model with `use Attakinsky\Encryptable\Encryptable`
2. Iclude the trait with `use Encryptable;`
3. Define an array named `$encryptable` with the fields that must be encrypted/decrypted.

Example:

```
<?php

namespace App;

use Attakinsky\Encryptable\Encryptable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, Encryptable;

    /**
     * The attributes that should be encrypted on database.
     *
     * @var array
     */
    protected $encryptable = [
        'name', 'email',
    ];
```

## Voila!

Now, your data will be encrypted on save, and decrypted on load. 

## Demo

Let's start an Artisan tinker session:

```
php artisan tinker
Psy Shell v0.9.9 (PHP 7.3.5-1+ubuntu16.04.1+deb.sury.org+1 — cli) by Justin Hileman
```

### Encrypt mutator

Create an user:

```
>>> $x = new User();
[!] Aliasing 'User' to 'App\User' for this Tinker session.
=> App\User {#3195}
>>> $x->name = "John Doe";
=> "John Doe"
>>> $x->email = "johndoe_encrypted@gmail.com";
=> "johndoe_encrypted@gmail.com"
>>> $x->password = Hash::make('123456');
=> "$2y$10$CM2qTNmUhbmmLFqiwWfvbuzTQO9sz4hmDo4COWK2HEQdWMhkLp7YO"
>>> $x->save();
=> true
```

### Decrypt accessor

Name and email fields are encrypted. You can verify in MySQL witn any tool like console, PHPMyAdmin or Adminer or something.

```
>>> $x = User::all();
=> Illuminate\Database\Eloquent\Collection {#3196
     all: [
       App\User {#3197
         id: 1,
         name: "eyJpdiI6InlvRWxQZ2ZcL1VBM0lqNW5pVkZxZ2d3PT0iLCJ2YWx1ZSI6ImtjUDNGRmVLNTF1clM0NllUb2FWWFI1WlMrandIaTVcL2lZTXkzSmVoWU5ZPSIsIm1hYyI6ImJmMjg3MzM3ZDE5OWJlMTkwNjg3MjBmNWQ0ZTcxYmY3MWY1ZjY0NmI4ZjhjMzg1NzAyZjY0NDE2MjE1NDNjNDIifQ==",
         email: "eyJpdiI6IklDRjJIUlBqaHp4dkJ3Z3ZTcWdmdVE9PSIsInZhbHVlIjoiaW5GUXVGcjJudE1GczRBYnFhN1V2RTRZUlRtT1czUFNWUDVnQ21qSEx1Zz0iLCJtYWMiOiIzZThmY2IyNzM3MTcxMjUwNmVmODQ1NDliNzNjN2MxZjFkMWY0MDA1MDZjZTAzNjI3YmRkZTcxM2FjNjA5ZWMwIn0=",
         email_verified_at: null,
         created_at: "2019-06-04 21:28:39",
         updated_at: "2019-06-04 21:28:39",
       },
     ],
   }
>>>
>>> $x = User::find(1);
=> App\User {#3198
     id: 1,
     name: "eyJpdiI6InlvRWxQZ2ZcL1VBM0lqNW5pVkZxZ2d3PT0iLCJ2YWx1ZSI6ImtjUDNGRmVLNTF1clM0NllUb2FWWFI1WlMrandIaTVcL2lZTXkzSmVoWU5ZPSIsIm1hYyI6ImJmMjg3MzM3ZDE5OWJlMTkwNjg3MjBmNWQ0ZTcxYmY3MWY1ZjY0NmI4ZjhjMzg1NzAyZjY0NDE2MjE1NDNjNDIifQ==",
     email: "eyJpdiI6IklDRjJIUlBqaHp4dkJ3Z3ZTcWdmdVE9PSIsInZhbHVlIjoiaW5GUXVGcjJudE1GczRBYnFhN1V2RTRZUlRtT1czUFNWUDVnQ21qSEx1Zz0iLCJtYWMiOiIzZThmY2IyNzM3MTcxMjUwNmVmODQ1NDliNzNjN2MxZjFkMWY0MDA1MDZjZTAzNjI3YmRkZTcxM2FjNjA5ZWMwIn0=",
     email_verified_at: null,
     created_at: "2019-06-04 21:28:39",
     updated_at: "2019-06-04 21:28:39",
   }

```

At this point, Attakinsky/Encryptable accessor's grants decrypt when you need it. For example in blade: `{{ $user->name }}` will print `John Doe`.

### Lumen and API support.

However, if you need a response with raw data, this would not be decrypted by the standard accessor. Attakinsky/Encryptable includes an additional bulk accesor that allows decypt data that will not be invoked but sent as answer in JSON format.


```
>>> $x->toArray();
=> [
     "id" => 1,
     "name" => "John Doe",
     "email" => "johndoe_encrypted@gmail.com",
     "email_verified_at" => null,
     "created_at" => "2019-06-04 21:28:39",
     "updated_at" => "2019-06-04 21:28:39",
   ]
>>> 
>>> $x->toJson();
=> "{"id":1,"name":"John Doe","email":"johndoe_encrypted@gmail.com","email_verified_at":null,"created_at":"2019-06-04 21:28:39","updated_at":"2019-06-04 21:28:39"}"
```
