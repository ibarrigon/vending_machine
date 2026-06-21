# Run

[< Go back](../README.md)

There are three ways to run the system. Only Client side, I don't provide Tecnician access, but you can "fill" the machine with

```console
make fill_machine
```

And then you can purchase everything like the machine has change and products

## End-points

Currently it's a problem. The access to the API doc it's broken and shows the json

```
http://localhost:8080/docs
```

## By script

```console
make execute SCRIPT="1, 0.25, 0.25, GET-SODA"
```

This do the same execution we have in statement but you can change SCRIPT with anything you want


```console
make examples
```

The exact examples you can read in provided statement

## Interactive

```console
make interactive
```

You can put a lot of commands until you put SHUTDOWN.
