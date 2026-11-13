# Aurora Shoes

Aurora Shoes is a single-page web application for an online shoe shop.
It is designed for browsing shoes, categories and reviews,
placing orders and managing customer profiles.

## Milestone 1

- Static frontend pages.
- SPA navigation using jQuery SPApp.
- Responsive design using Bootstrap and custom CSS.
- Separate frontend and backend folders.
- ER diagram with six entities.
- Project and AI usage documentation.

The frontend uses sample data. Forms are not connected to the backend.

## Milestone 2

The local MySQL/MariaDB database contains six tables:
users, categories, products, orders, order_items and reviews.

The schema includes primary keys, foreign keys and constraints.
Sample data for all six tables is included in database/aurora_shoes.sql.
A database export is available in database/aurora_shoes_export.sql.

The PHP backend uses PDO prepared statements, a shared BaseDAO
and a DAO class for each table. 

Read checks passed using test_daos.php.
Create, read, update and delete checks passed for all six entities
using test_crud.php. The CRUD test rolls back its changes.

The SQL export was successfully imported into a separate test database.

## Relationships and roles

A user can have many orders and reviews.
A category contains products.
Orders contain order items, each linked to a product.
Each review belongs to a user and a product.

Application roles are customer and admin.
Demo users for both roles are included in the database.
Application login is not implemented yet.

## Local setup

1. Start MySQL in XAMPP.
2. Create an empty database named aurora_shoes, select it,
   and import database/aurora_shoes_export.sql.
3. Copy backend/config/config.example.php to backend/config/config.php.
4. Enter your local database credentials in config.php.
   This file is excluded from Git.
5. Open a terminal in backend and run composer install.
6. Start Apache in XAMPP.
7. Open http://localhost/web-project3/frontend/ for the static frontend
   or the Swagger URL below to test the API.

The project is served from C:\xampp\htdocs\web-project3.
The frontend is not yet connected to the API.

Import the export only into a fresh database.
Use an account with permission to create tables and import data.
The account used by PHP needs SELECT, INSERT, UPDATE and DELETE
permissions on aurora_shoes.

Run the checks from the project root:

```powershell
& "C:\xampp\php\php.exe" backend/test_connection.php
& "C:\xampp\php\php.exe" backend/test_daos.php
& "C:\xampp\php\php.exe" backend/test_crud.php
```

## Database diagram

![Database ER diagram](database/database.png)

## AI documentation

- AGENTS.md — project architecture and rules.
- docs/AI_LOG.md — AI assistance, prompts and corrections.

## Milestone 3

Added services with validation and FlightPHP CRUD routes
for all six entities.

Routes call services, services apply business rules,
and DAO classes access the database using PDO.

Implemented:
- Category name validation and duplicate-name checks.
- Product price, stock and category checks.
- User validation and password hashing.
- Order and order item validation.
- Automatic order total calculation in a transaction.
- Review ratings between 1 and 5.

User responses do not include password hashes.

### API documentation

OpenAPI documentation is generated from annotations in route files.
Swagger UI documents 32 operations, including the API status
and currency conversion endpoints.

With Apache and MySQL running, open:
http://localhost/web-project3/backend/public/v1/docs/index.html

### External API

CurrencyService calls the Frankfurter API to obtain exchange rates.
It supports EUR, USD and GBP and does not require an API key.

Example:
http://localhost/web-project3/backend/currency/convert?amount=99.90&from=EUR&to=USD

The service uses connection and request timeouts.
If the external API fails, the endpoint returns a JSON message
with status 503 and writes the failure to backend/logs/error.log.

### Verification

- CRUD requests were tested for all six entities.
- Order totals were checked after adding, updating and deleting items.
- Invalid input returned status 400 in the tested cases.
- Deleting a missing category returned status 404.
- Swagger successfully sent requests to the local API.
- Currency conversion worked with the external API.
- Disconnecting the internet produced an error response and log entry.
- Conversion worked again after restoring the connection.