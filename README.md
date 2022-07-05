
# Discount codes that charge the user's wallet

If a discount code is defined in the discounts table, whose type is equal to by_finance, The wallets of users who use this discount code will be charged according to the discount_value.
## Installation
#1. manual
In root directory  install dependencies with the composer

```bash
  composer install
```

create database and run migrate
```bash
  php artisan migrate --seed
```

then

```bash
  php artisan serve
```

#2. docker

In root directory
```bash
$ docker-compose up -d --build
```

then
```bash
$  docker ps -a
```

open your browser and check 127.0.0.1:8000


