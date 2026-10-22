# Aurora Shoes

Aurora Shoes is a single-page web application for an online shoe shop.

The application is designed for customers to browse shoes, view categories
and reviews, place orders, and manage their profiles. Admins will manage
shop data.

## Milestone 1

Milestone 1 includes:

- Static frontend pages and components.
- SPA navigation using jQuery SPApp.
- Responsive styling using Bootstrap and custom CSS.
- Separate frontend and backend directories.
- Backend folder structure for future development.
- A draft ER diagram with six entities.
- Project documentation and AI usage documentation.

Products, orders, profiles and dashboard statistics are sample data.
Forms are static previews and are not connected to a backend.

## Technologies

### Frontend

- HTML
- CSS
- JavaScript
- jQuery
- jQuery SPApp
- Bootstrap

### Planned backend

- PHP with FlightPHP
- MySQL
- PDO
- JWT authentication

## Pages

- Home
- Products
- Categories
- Reviews
- Orders
- Contact
- Login
- Register
- Profile
- Dashboard

Navigation between views uses URL hashes and loads HTML templates
without a full page reload.

## Project structure

```text
web-project3/
  frontend/
    index.html
    assets/
      css/
      js/
      images/
    views/
  backend/
    routes/
    services/
    dao/
    middleware/
    config/
    logs/
  database/
    database.png
  docs/
    AI_LOG.md
  .gitignore
  AGENTS.md
  README.md
```

The backend directories are placeholders for later milestones.
The .gitignore rules exclude secrets, runtime logs and dependencies.
Empty tracked backend directories contain .gitkeep files.

## Database plan

The planned database contains six entities:

- Users — customer and administrator accounts.
- Categories — product categories.
- Products — shoes available in the shop.
- Orders — customer orders.
- OrderItems — products and quantities within each order.
- Reviews — customer ratings and comments about products.

### Relationships

- One user can have many orders.
- One user can write many reviews.
- One category can contain many products.
- One order can contain many order items.
- One product can appear in many order items.
- One product can have many reviews.
- Each order item belongs to one order and references one product.
- Each review references one product and one user.

### ER diagram

![Database ER diagram](database/database.png)

Database planning is included in Milestone 1.
MySQL database creation and the SQL export are planned for Milestone 2.

## Planned roles

- Customer — browse products, place orders, write reviews and view a profile.
- Admin — manage users, categories, products, orders and reviews.

Authentication and role-based access will be implemented in later milestones.
Test accounts are not available in the static frontend.

## Planned external API feature

A currency conversion API will provide exchange rates so product prices
can be displayed in different currencies.

The planned feature appears in the Currency Converter section
on the Products page. The current currency selector is a static preview.

The API provider will be selected before implementation.

## Running the frontend locally

1. Open the project folder in Visual Studio Code.
2. Install the Live Server extension if it is not already installed.
3. Right-click frontend/index.html.
4. Select Open with Live Server.
5. Use the navigation menu to switch between views.

Use a local HTTP server because SPA views are loaded through AJAX.
Internet access is required for libraries loaded from external CDNs.

PHP and MySQL are not required to preview Milestone 1.

## AI documentation

- AGENTS.md describes the project architecture and rules for AI assistance.
- docs/AI_LOG.md records AI assistance, prompts, mistakes and corrections.

## Later milestones

Later milestones will add:

- MySQL database creation and sample data.
- DAO classes using PDO prepared statements.
- Services and FlightPHP REST routes.
- OpenAPI documentation and Swagger UI.
- External API integration.
- JWT authentication and role-based authorization.
- Request logging and error handling.
- Frontend integration with the backend.
- Form validation and deployment.

The Swagger link, deployed application URL, configuration instructions
and test accounts will be added when those features are implemented.