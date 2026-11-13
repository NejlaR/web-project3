# AI Usage Log

## Milestone 1

### Tools and assistance
ChatGPT — checking milestone requirements and helping with
project structure and documentation.

### Main prompts
- Review the project for Milestone 1.
- Explain the missing setup steps.

### Mistakes and corrections
1. ChatGPT misunderstood my question about documenting AI use.
   I clarified the question, and it corrected its explanation.
2. ChatGPT suggested a draft log with an unfinished example.
   I reviewed it, and the unfinished entry was removed.

## Milestone 2

### Tools and assistance
ChatGPT/Codex helped implement database configuration and DAO
classes, explain the code, prepare sample data and tests,
troubleshoot local database issues, and update documentation.

### Main prompts
- Help implement DAO classes for all six entities.
- Explain which code belongs in each file.
- Help diagnose database connection error 1045.
- Review my work against the Milestone 2 requirements.
- Keep the project local without deployment.

### Mistakes and corrections
1. The assistant initially suggested a configuration approach
   that did not match my application structure. I noticed the
   mismatch, and the configuration was changed to use the
   Config class and Database::connect().

2. A PHP diagnostic command supplied by the assistant failed because
   of quotation handling in PowerShell. I noticed the PHP parse error.
   The diagnostic code was moved into a PHP file and ran successfully.

### Verification completed
- The local PHP database connection succeeded.
- Read checks passed for all six DAO classes.
- Create, read, update and delete checks passed for all six entities.
- The CRUD test rolled back its temporary changes.
- Sample data was added and read successfully from all six tables.
- PHP syntax checks passed during the project review.
- The SQL export was imported into a separate test database.
  All six tables contained the expected sample data.

### Local database troubleshooting
Connection troubleshooting revealed corruption in MariaDB privilege
tables. I backed up the data directory before running repairs.
The repair of mysql.db reported a loss of three privilege rows.
A local aurora_app account was then configured with CRUD permissions
on aurora_shoes, and the PHP connection succeeded.


## Milestone 3

### Tools and assistance
ChatGPT/Codex helped implement services and CRUD routes, explain validation and transactions, prepare OpenAPI documentation, integrate the Frankfurter API, and perform local testing.

### Main prompts
- Explain how services and routes are organized and where each file belongs.
- Add services and CRUD routes for all six entities.
- Set up Swagger documentation.
- Add currency conversion with timeout and error handling.

### Mistakes and corrections
1. The assistant initially told me to test the root route through
   backend/index.php. Flight returned 404 because the defined route
   was GET /. Opening backend/ returned the expected JSON response.

2. The Composer command supplied by the assistant did not preserve
   the caret in the version constraint on my Windows terminal.
   Composer installed exactly Flight 3.17.0 and displayed a warning.
   I changed composer.json to use ^3.17 and ran Composer update.

3. Installing Swagger dependencies brought in a Symfony YAML
   version with security advisories. Composer audit identified the affected package. I added the compatible constraint
   ^5.4.54 and updated it. Composer then reported no security
   advisories. The separate abandoned-package warning for
   doctrine/annotations remains.

### Implementation
- Added services and CRUD routes for six entities.
- Added validation for required fields, IDs, prices, stock and ratings.
- Added password hashing and removed hashes from user responses.
- Added transactional order item changes and total recalculation.
- Added OpenAPI annotations and a Swagger UI page.
- Integrated Frankfurter currency conversion for EUR, USD and GBP.

### Verification
- Manually tested create, read, update and delete for all six entities.
- Tested order totals after adding, changing and deleting an item.
- Checked status 400 for an empty category name, duplicate category
  name, negative price, invalid email, user ID 0, quantity 0 and rating 6.
- Checked status 404 when deleting an already deleted category.
- Tested requests through Swagger UI.
- Tested successful currency conversion.
- Tested rejection of a negative amount and unsupported currency.
- Disconnected the internet and received a friendly error response.
- Confirmed the external API failure was written to error.log.
- Restored the internet and confirmed conversion worked again.
- Oversized category and product descriptions returned validation errors.
- Unknown API routes returned a JSON 404 response.

