# Aurora Shoes — Project Context

## Description
Aurora Shoes is a single-page application for an online shoe shop.
Customers can browse products and reviews, place orders, and view
their profiles. Admins manage shop data.

## Technology stack
- Frontend: HTML, CSS, JavaScript, jQuery SPApp and Bootstrap.
- Planned backend: PHP with FlightPHP.
- Planned database: MySQL, accessed through PDO.
- Planned authentication: JWT.

## Project structure
- frontend/assets/css/ — stylesheets.
- frontend/assets/js/ — JavaScript and SPA navigation.
- frontend/assets/images/ — static images.
- frontend/views/ — HTML views.
- backend/routes/ — REST API routes.
- backend/services/ — business logic.
- backend/dao/ — database access.
- backend/middleware/ — authentication, authorization and logging.
- backend/config/ — configuration.
- backend/logs/ — runtime logs, excluded from Git.
- database/ — database diagram and future SQL export.
- docs/AI_LOG.md — AI usage documentation.

## Architecture rules
- Keep frontend and backend code separate.
- Use FlightPHP for backend routes and PDO prepared statements
  for database queries.
- Routes call services; services call DAO classes.
- Keep credentials, tokens and API keys out of Git.
- Use customer and admin roles.

## Naming conventions
- Use lowercase names for HTML views.
- Use snake_case for database tables and columns.
- Use PascalCase for PHP classes and camelCase for JavaScript functions.

## Current milestone
Milestone 1 includes static frontend views, SPA navigation,
project structure and database planning.
Backend connections are not required at this stage.

## Rules for AI assistance
- Keep changes within the current milestone.
- Explain changes so I can understand and defend the code.
- Do not add unnecessary libraries or features.
- Record actual AI assistance and corrections in docs/AI_LOG.md.
- Identify reused code and preserve relevant source attribution.