# Aurora Shoes — Project Context

## Description
Aurora Shoes is a single-page application for an online shoe shop.
It is designed for customers to browse products and reviews,
place orders and manage their profiles. Admins manage shop data.

## Technology stack
- Frontend: HTML, CSS, JavaScript, jQuery SPApp and Bootstrap.
- Backend: PHP with PDO.
- Database: MySQL/MariaDB.
- FlightPHP and JWT are planned for later milestones.

## Project structure
- frontend/assets/ — stylesheets, JavaScript and images.
- frontend/views/ — HTML views.
- backend/Database.php — shared PDO connection.
- backend/config/ — local configuration and configuration example.
- backend/rest/dao/ — BaseDAO and six entity DAO classes.
- backend/rest/routes/ — placeholder for REST routes.
- backend/rest/services/ — placeholder for business logic.
- backend/middleware/ — placeholder for middleware.
- backend/logs/ — runtime logs, excluded from Git.
- backend/test_connection.php — connection check.
- backend/test_daos.php — DAO read checks.
- backend/test_crud.php — transactional CRUD checks.
- database/ — ER diagram and SQL schema with sample data.
- docs/AI_LOG.md — AI usage documentation.

## Architecture rules
- Keep frontend and backend code separate.
- Use PDO prepared statements for database queries.
- Keep shared CRUD methods in BaseDAO.
- Entity DAO classes define their table, primary key and writable fields.
- Use FlightPHP for future REST routes.
- Routes call services; services call DAO classes.
- Store local credentials in backend/config/config.php.
- Never commit config.php, secrets or runtime logs.
- Keep config.example.php free of actual passwords.
- Store application passwords as hashes.
- Do not expose password hashes in API responses.

## Database
The database is aurora_shoes.
Tables: users, categories, products, orders, order_items and reviews.
Primary keys and foreign keys follow database/aurora_shoes.sql.
Application roles are customer and admin.

## Naming conventions
- Use lowercase names for HTML views.
- Use snake_case for database tables and columns.
- Follow existing PHP names, such as BaseDAO and UserDAO.
- Use camelCase for JavaScript functions.
- Write code messages and comments in English.

## Current milestone
Milestone 2 covers the local database and DAO layer.
All six tables contain sample data.
Connection, read and transactional CRUD checks passed.
The frontend remains static and is not connected to the backend.

## Rules for AI assistance
- Keep changes within the current milestone.
- Follow the supplied shoe-shop reference project where appropriate.
- Explain adaptations so I can understand and defend the code.
- Do not add unnecessary libraries or features.
- Record actual assistance and corrections in docs/AI_LOG.md.
- Preserve relevant source attribution.
- Do not claim successful checks without running them.
- Keep the project local; do not deploy or publish it.