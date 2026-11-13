# Aurora Shoes — Project Context

## Description
Aurora Shoes is a single-page application for an online shoe shop.
Customers browse products and reviews, place orders and manage profiles.
Admins manage shop data.

## Technology stack
- Frontend: HTML, CSS, JavaScript, jQuery SPApp and Bootstrap.
- Backend: PHP, FlightPHP and PDO.
- Database: MySQL/MariaDB.
- Documentation: swagger-php and Swagger UI.
- External API: Frankfurter exchange rates.
- Dependencies: Composer.

## Project structure
- frontend/assets/ — stylesheets, JavaScript and images.
- frontend/views/ — HTML views.
- backend/index.php — service registration and route loading.
- backend/.htaccess — Apache URL rewriting.
- backend/Database.php — shared PDO connection.
- backend/config/ — local configuration and example.
- backend/rest/dao/ — BaseDAO and entity DAO classes.
- backend/rest/services/ — validation and business logic.
- backend/rest/routes/ — FlightPHP routes and OpenAPI annotations.
- backend/public/v1/docs/ — OpenAPI generation and Swagger UI.
- backend/middleware/ — reserved for later middleware.
- backend/logs/ — runtime logs, excluded from Git.
- backend/vendor/ — Composer dependencies, excluded from Git.
- backend/test_connection.php — connection check.
- backend/test_daos.php — DAO read checks.
- backend/test_crud.php — transactional DAO CRUD checks.
- database/ — ER diagram, schema and SQL export.
- docs/AI_LOG.md — AI usage documentation.

## Architecture rules
- Keep frontend and backend code separate.
- Routes call services; services call DAO classes.
- Keep SQL in DAO classes and use prepared statements.
- Validate request data in services.
- Return JSON and appropriate HTTP status codes from routes.
- Do not expose SQL errors, stack traces or password hashes.
- Keep shared CRUD methods in BaseDAO and BaseService.
- Do not modify files inside vendor.

## Configuration
- Store local database credentials in backend/config/config.php.
- Never commit config.php, secrets, logs or vendor.
- Keep config.example.php free of actual passwords.
- Commit composer.json and composer.lock.
- Store application passwords as hashes.

## Database and business rules
The database is aurora_shoes.
Tables: users, categories, products, orders, order_items and reviews.
Application roles: customer and admin.

- Category names and user emails must be unique.
- Products must reference existing categories.
- Orders must reference existing users.
- New orders start as pending with a zero total.
- Order items use the product price from the database.
- Existing order items allow quantity changes only.
- Item changes are restricted to pending orders.
- Item changes and total recalculation use one transaction.
- Review ratings must be integers between 1 and 5.
- Reviews must reference existing users and products.

## External API
CurrencyService calls Frankfurter for EUR, USD and GBP conversion.
No API key is required.
Keep timeouts, response validation and error logging.
External failures return a friendly JSON response with status 503.
Other application endpoints must remain usable during an outage.

## Naming conventions
- Use lowercase names for HTML views.
- Use snake_case for database tables and columns.
- Follow existing PHP class and method names.
- Use camelCase for JavaScript functions.
- Write code messages and comments in English.

## Current milestone
Milestone 3 includes services, CRUD routes, OpenAPI documentation
and the external currency API endpoint.

Manual CRUD checks passed for all six entities.
Order total calculation, selected validation errors and external API
failure handling were tested.

The frontend remains static.
Login, JWT and authorization middleware are not implemented yet.

The current working copy is C:\xampp\htdocs\web-project3.

## Rules for AI assistance
- Keep changes within the current milestone.
- Follow the existing project structure and coding conventions.
- Explain adaptations so I can understand and defend the code.
- Keep the implementation simple and avoid unnecessary features.
- Record actual assistance and corrections in docs/AI_LOG.md.
- Preserve relevant source attribution.
- Do not claim successful checks without evidence.
- Keep the project local; do not deploy or publish without my instruction.