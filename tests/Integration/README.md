# Integration Tests

This directory contains integration tests that verify actual HTTP communication with the DaData API.

## Setup

Integration tests require valid DaData API credentials to run. You need to set the following environment variables:

- `DADATA_API_KEY` - Your DaData API key
- `DADATA_SECRET_KEY` - Your DaData secret key

## Running Integration Tests

### Option 1: Set environment variables and run all tests
```bash
export DADATA_API_KEY="your_api_key_here"
export DADATA_SECRET_KEY="your_secret_key_here"
docker exec $(docker ps -q -f name=laravel.test) bash -c "cd /var/www/html/packages/ex3mm/dadata && ./vendor/bin/phpunit --testsuite Integration"
```

### Option 2: Run with inline environment variables
```bash
docker exec $(docker ps -q -f name=laravel.test) bash -c "cd /var/www/html/packages/ex3mm/dadata && DADATA_API_KEY=your_api_key DADATA_SECRET_KEY=your_secret_key ./vendor/bin/phpunit --testsuite Integration"
```

### Option 3: Run integration tests by group
```bash
docker exec $(docker ps -q -f name=laravel.test) bash -c "cd /var/www/html/packages/ex3mm/dadata && ./vendor/bin/phpunit --group integration"
```

## Test Credentials

For testing purposes, you can use DaData's test credentials if available, or create a test account with limited quota.

**Important**: Never commit real API credentials to version control. Use environment variables or a local `.env.testing` file that is gitignored.

## What Integration Tests Verify

Integration tests verify:
- Actual HTTP communication with DaData API endpoints
- Response structure and data format
- Error handling for real API errors
- Network connectivity and timeout handling
- Authentication with real credentials

These tests complement unit tests by ensuring the package works correctly with the actual DaData service.
