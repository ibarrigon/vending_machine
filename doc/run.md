# Run

[< Go back](../README.md)

There are three ways to run the system.

Only the client side is exposed. I do not provide direct access to technician operations, but you can pre-fill the machine by running:

```console
make fill_machine
```

This will populate the machine with products and change, allowing you to purchase items immediately.

## Endpoints

Currently, there is a known issue with the API documentation. The documentation endpoint is available, but instead of rendering the Swagger UI, it displays the raw JSON specification:

```text
http://localhost:8080/docs
```

## By Script

```console
make execute SCRIPT="1, 0.25, 0.25, GET-SODA"
```

This executes the same scenario described in the statement, but you can replace the `SCRIPT` value with any sequence of commands you want.

You can also run the predefined examples:

```console
make examples
```

These are the exact examples provided in the original statement.

## Interactive Mode

```console
make interactive
```

This starts an interactive session where you can enter commands continuously until you type:

```text
SHUTDOWN
```
