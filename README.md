
# Discount codes that charge the user's wallet

If a discount code is defined in the discounts table, whose type is equal to by_finance, The wallets of users who use this discount code will be charged according to the discount_value.
## Installation

Connect to MySQL and run this command

```bash
  source db.sql
```

In root directory  install dependencies with the composer

```bash
  composer install
```

then

```bash
  php artisan serve
```


## Tech Stack
**Server:** Laravel 9, php 8.1.3, MySQL +5.7

