<h1 align="center"><a href="https://api-platform.com"><img src="https://api-platform.com/images/logos/Logo_Circle%20webby%20text%20blue.png" alt="API Platform" width="250" height="250"></a></h1>

API Platform is a next-generation web framework designed to easily create API-first projects without compromising extensibility
and flexibility:

## Install
Run below commands
1. `docker compose build --no-cache`
2. `docker compose up -d`

Please make sure that you have installed docker and running daemon.
<br>
After that API will start at https://localhost:8080/docs

### Unit Tests

To run the tests:
#### Run all cart tests
`docker compose exec php bin/phpunit tests/Api/CartTest.php`

#### Run specific test
`docker compose exec php bin/phpunit tests/Api/CartTest.php --filter testCartWithItems`

#### Run all API tests
`docker compose exec php bin/phpunit tests/Api/`


## E2E tests

Projects supports some e2e tests by playwright

### Run e2e tests (starts all services including e2e)
docker compose --profile test up e2e

### Or run e2e tests after services are already running
docker compose run --rm e2e

### Run with all services in the background, then run tests
docker compose up -d
docker compose run --rm e2e


