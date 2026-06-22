# Architecture

[< Go back](../README.md)

## Domain

The business model. This is where everything the business knows lives, using its own language.

There's one thing worth mentioning. This system allows the creation of multiple machines, each with its own state. I didn't read anything in the statement about this, but I implemented it to make the solution more extensible and to support something like a vending machine fleet in the future.

### VendingMachine

This is the core of the Domain. It provides the required functionality and business rules or, in Hexagonal Architecture terms, the Domain itself.

As described in the statement, there are three available actions for the client:

1. **Insert coin**: Accept coins from the client.
2. **Return coins**: Return the client's inserted coins.
3. **Select product**: Deliver the product, return any change, and calculate the remaining balance.

And there are three available actions for the technician:

1. **Refill slot**: Fill a slot with products.
2. **Refill coins**: Add coins for change.
3. **Withdraw coins**: Remove excess change.

### Machine

This folder contains everything inside the machine:

1. **Slots**: Where the products are stored.
2. **CoinMachine**: The "black box" inside the machine that controls everything related to coins.
3. **Configuration**: The vending machine setup. It provides the available slots (products) and their prices. It can be extended to include additional configuration, such as coin limits.

## Application

The interaction layer. It provides access to the Domain from the interface, console, API, etc. Here you can find the Use Cases.

In this folder, you can see two subfolders representing the different roles: Client and Technician.

### UseCase

As in the Domain, this layer contains the operations we can perform. These classes are intentionally small because they mainly act as a bridge between external input (Infrastructure or Ports) and internal logic (Domain).

I don't think they need much explanation: obtain a repository (through an interface), call a domain operation (or create domain objects when needed), and return a response.

### Command

Initially, there was only one Use Case, and commands were used to provide access to Domain operations inside `VendingMachine`.

However, the solution became cleaner and more maintainable once I created separate Use Cases.

A Command is simply another name for a Request. Every command contains the data required by a Use Case.

## Infrastructure

Well, here we have the concrete implementation details: the database, the ORM, and so on. I don't think this part is especially interesting.

This is the layer where we need to establish cooperation with the DevOps team or where we need to make requests to the Systems Department.

We can say this is the "metal" part of the project, and one that can be replaced like puzzle pieces.

For example, today we use MySQL, but maybe MariaDB would be enough, or perhaps PostgreSQL would be preferred... or maybe we have an AS/400 and can connect to that hardcore system instead (I know because I've done it).

Ah! This is also where `docker-compose` finds its alma mater 😅

## Improvements

In every Use Case, you can find this:

```PHP
        } catch (Throwable $e) {
            // TODO: Implement different exceptions and, if the machine becomes unavailable, set its state to out of order
            throw $e;
```

If we want to deploy this to production, this TODO needs to be implemented.

There are two different concerns here:

- Clients should only receive an "Out of order" message.
- Technicians should receive detailed information about what happened.

This requires a few more iterations and a proper exception hierarchy to separate business errors from technical failures.
```
