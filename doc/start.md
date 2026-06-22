# How to Start

[< Go back](../README.md)

## Makefile

(For Windows users, WSL is required.)

I provide a Makefile to make common commands easier to use.

You can run `make` or `make help` to see the list of available commands.

## Start

```console
make init
```

This command initializes the environment. It builds the Docker containers and creates the database.

### Note

During the installation process, I added the following command:

```console
$(COMPOSE) exec app git config --global --add safe.directory /app
```

The reason is that I encountered an error, and this was the quickest way to solve it. You may want to remove it if it is not needed in your environment.

Based on my experience and after "talking" with my friend SkyNet (movie reference, you know what I mean), we concluded that it is probably related to running the project on Windows.

## Tests

```console
make test
```

This executes all tests, but maybe you do not want to run everything. Perhaps you only want to execute the unit tests?

```console
make test-unit
```

What you see is what you get.

I also added some additional testing commands:

### Functional Tests

```console
make test-functional
```

Runs the functional test suite.

### Coverage

```console
make coverage
```

Generates the code coverage report.

## Code Quality

The following commands are available to verify code quality:

### PHP CS Fixer

```console
make cs
```

Automatically fixes coding style issues.

```console
make cs-dry
```

Checks coding style issues without modifying any files.

### PHPStan

```console
make phpstan
```

Runs static analysis on the project.

### Quality

```console
make quality
```

Runs all available quality checks.

## More Commands

```console
make build
```

Builds the Docker images.

```console
make install
```

Runs `composer install`.

```console
make up
```

Starts the environment.

For example, after restarting your computer, this is probably the command you want to run.

```console
make down
```

Stops the environment.

You probably will not need this very often.

```console
make clear
```

Clears the Symfony cache.

```console
make kill
```

Removes everything related to the project in Docker, including containers and other generated resources.

## Database

```console
make db-init
```

Creates the database only if it does not already exist.

```console
make db-reset
```

As the name suggests, it recreates the database from scratch.
