# How to Start

[< Go back](../README.md)

## Makefile

(For Windows, WSL is required.)

I provide a Makefile to make some commands easier to use.

You can execute `make` or `make help` to see the available commands.

## Start

```console
make init
```

With this command you initialize the environment. It builds the Docker containers and generates the database.

### Note

During the installation process, I added the following command:

```console
$(COMPOSE) exec app git config --global --add safe.directory /app
```

The reason is that I encountered an error, and this was the easiest and fastest way to resolve it. You may want to remove it.

From my experience and after "talking" with my friend SkyNet (movie reference, you know what I mean), we concluded that it is probably related to working on Windows.

## Tests

```console
make test
```

This executes all tests, but maybe you do not want to run all of them. Perhaps you only want to run the unit tests?

```console
make test-unit
```

What you see it's what you get

I also added other testing commands:

### Functional Tests

```console
make test-functional
```

### Integration Tests

```console
make test-integration
```

These are the slowest tests because they depend on an in-memory database using SQLite. They also require the schema to be generated every time they are executed.

I do not particularly like this type of test in PHP. I generally prefer using Cypress. I have only used it a couple of times, but I think it is a really good tool.

### Coverage

```console
make coverage
```

This command generates the code coverage report.

## Quality Code

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

Build Docker images.

```console
make install
```

Runs `composer install`.

```console
make up
```

For example, after restarting your computer...

```console
make down
```

You probably won't need this for now.

```console
make clear
```

Clears the Symfony cache.

```console
make kill
```

Maybe you want to remove everything related to the project — at least the Docker containers... this is the one.

## For DB

```console
make db-init
```

Generate the database only if it does not exist previously.

```console
make db-reset
```

As the name suggests... recreates the database.
