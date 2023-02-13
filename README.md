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
