# Worktime

A service for getting user shedule, in accordance with the working calendar, holidays and internal breaks in the work of the organization.

**This is a test task. It's not intended for use as a real service, and will not be supported. (bold)**

## Requirements

PHP 7.1+, MySQL, Composer

## Installation

1. Download files and place in site root directory. You should get this list in the site root folder:

```
config
db
src
tests
.htaccess
composer.json
index.php
```

2. Set up database connection preferences (`host`, `username`, `password`, `db`) in file `config/config.php`, into `db` section.

3. This service receive common rest days by API, from https://data.gov.ru/ . So you need to receive API key:

3.1. Register on site https://data.gov.ru/

3.2. After registration, open page https://data.gov.ru/get-api-key and generate key by instructions.

3.3. Insert API key to file `config/config.php`, to `DataGovRu => accessToken` value.

4. Load dumps to database:

`db/tables.sql` - Empty tables. This is required dump.

`db/test_data.sql` - Test data, for example. You can insert in tables any other your data, if needs.

5. Install dependences by composer. To do this, run this command in site root folder:

composer install

5. Open URL: https://your.domain/update_rest_days . Rest days will be received and saved to local DB.



It's all, service ready. Now, we can use `shedule` endpoint.


## Use

Open URL like https://your.domain/shedule?startDate=2018-01-01&endDate=2018-01-14&userId=1

startDate - First day of period, string date in 'Y-m-d' format.
endDate - Last day of period, string date in 'Y-m-d' format.
userId - User id, integer.

## Run tests

The `schedule` endpoint test verifies the service in a complex, and requires a live database with specific test data.

1. Prepare test database:

Create new empty database and load to it this dump file: `tests/db_data/test_db_data.sql`

2. Set up database connection preferences (`host`, `username`, `password`, `db`) in file `tests/configs/test_shedule_endpoint_config.php`, into `db` section.

Now, we can run tests:

```
vendor/bin/phpunit tests
```

