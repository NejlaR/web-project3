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
ChatGPT/Codex — adapting the reference project's configuration
and DAO classes, explaining the code, preparing sample data
and tests, and helping with local database troubleshooting
and documentation.

### Main prompts
- Follow the original shoe-shop project when preparing my DAO classes.
- Explain which code belongs in each file.
- Help diagnose database connection error 1045.
- Review my work against the Milestone 2 requirements.
- Keep the project local without deployment.

### Mistakes and corrections
1. The assistant initially provided a different configuration approach
   before inspecting the reference project. I noticed the difference
   and asked it to follow the original. The configuration was changed
   to use the Config class and Database::connect().

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

### Reference code
DAO structure and method names were adapted from the supplied
shoe-shop-web-app-main reference project.

Changes included matching my primary keys, validating writable
columns and using distinct placeholders for native prepared statements.
The transactional CRUD test was prepared for my project and was
not copied from the reference project.