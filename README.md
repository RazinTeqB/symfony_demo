## Floraqueen Backend Developer Test

### Prerequisites

- PHP >=8.1
- Composer
- [Symfony cli](https://symfony.com/download) to start server.
  - Alternatively can also use php in-built server.

### Installation Steps

1. Install dependencies

   ```bash
   composer install
   ```

2. Set database credentials in .env

- DATABASE_URL="mysql://<USERNAME>:<PASSWORD>@127.0.0.1:3306/<DB_NAME>?serverVersion=8&charset=utf8mb4"

  ```bash
  DATABASE_URL="mysql://root:1234@127.0.0.1:3306/floraqueen_demo?serverVersion=8&charset=utf8mb4"
  ```

3. Create tables in db

   ```bash
   php bin/console d:s:u --force
   ```

4. Run fixture to insert products and voucher in db.

   ```bash
   php bin/console doctrine:fixtures:load
   ```

- If running fixture second time then to reset auto increment column run below command

  ```bash
  php bin/console doctrine:fixtures:load --purge-with-truncate
  ```

5. Start server

   ```
   symfony server:start
   ```

- If symfony cli is not avaliable than php in-built server can be used

  ```bash
  php -S localhost:8000 public/index.php
  ```

6. Visit url
- [localhost:8000](http://localhost:8000)

Example Outputs:

1. Example 1

    ![Alt text](public/images/Floraqueen-Backend-Developer-Test-1.png)

2. Example 2

    ![Alt text](public/images/Floraqueen-Backend-Developer-Test-2.png)


## Initial Requirement

1. Shopping cart (or Basket) is a concept used in e-commerce to assist people making
purchases online. The software allows online shopping customers to accumulate a
list of items for purchase, described metaphorically as “placing items in the shopping
cart” or “adding to cart”. Upon checkout, the software typically calculates a total for
the order, including shipping and handling (i.e. postage and packing) charges and the
associated taxes, as applicable. Vouchers are discount codes that can be entered
when shopping online. As a result of using a voucher code in a shopping cart, online
customers can get a wide different range of discounts.
Imagine an application where we can add items in the Shopping cart, those items can
be both:
products and vouchers. The application has 3 kind of Product and 3 types of Voucher:
- Product A (10€)
- Product B (8€)
- Product C (12€)
- Voucher V (10% off discount voucher for the second unit applying only to Product A)
- Voucher R (5€ off discount on product type B)
- Voucher S (5% discount on a cart value over 40€)

Example 1:
Product A added + Product C added + Voucher S added + Product A added +
Voucher V added + Product B added.
- Total cart value: 39€

Example 2:
Product A added + Voucher S added + Product A added + Voucher V added +
Product B added + Voucher R added + Product C added + Product C added + Product C added.
- Total cart value: 55,10€