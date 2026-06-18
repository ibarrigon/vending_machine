# How to start

[< Go back](../README.md)

## Makefile

(For Windows wsl is required)
I provide a make use some commands more easily.

You can execute ``make`` or ``make help`` and found the commands to execute

## Start

```console
make init
```

With this command you initialize de environment. Docker build, generate database

## Test

```console
make test
```

This execute all the test, but maybe you don't want to do that, ¿maybe only unit test?

```console
make test-unit
```

I have a bit of a conflict with this test. Well, in this case, it's good enought, but normally I prefer what I is named as
component test. For example, We have a VendigMachine, isn't it? Well, this machine has a CoinMachine inside, this box, never 
works out of the VendingMachine, then (for me) why unit test? maybe it's better to test how they interact as a total component?

Ok, now, nots this case, I know CoinMachine its "inside", but we can test it with much effort. I'm 100% on it in this case. I don't 
know if I explain well. Oh! but that's my opinion. Obviously, as a group, I do what everyone make a rule.

Next, I generate other commands:

1. Functional test

```console
make test-functional
```



2. Integration test. 

```console
make test-integration
```

Here is the most slow ones becouse it depends on a DB on memory, with SQL Lite, but requires to 
generate all the schema every time. I don't really like this ones in PHP. I prefer to use Cypress (I only use it twice, but
I think its really good enought).
