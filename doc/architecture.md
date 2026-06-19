# Architecture

[< Go back](../README.md)

Sorry, I put some effort here into explaining why and how I decided what to do (or not do), but it's too short to really explain everything that came up while I was evolving the code (like a Pokémon, sorry for the joke).

I really didn't make the commits in the proper order... Do you know when something takes 3 hours of work, but it feels like only 5 minutes to you?

I can only say that one of my references is a novel called "The Blue Nowhere" or, in Spanish, "La estancia azul".

## Design Decisions

I decided to use PHP, Symfony and MySQL. In my opinion, the database was probably not really needed, but I included one for traceability and to maintain a consistent machine state.

You can shut the machine down (not in reality), and when it comes back up, you still have the last inventory, the last amount of change inside the machine, and even the coins inserted by the last client. I know that a real vending machine would probably return them, but I made a concession there for the client.

I didn't implement any security such as roles or anything similar, but I understand that an API with two user types would require it. For example, a client doesn't need to know there is an admin area in the API (in this case, technician operations).

I use Doctrine to read from and save to the database, but I don't like migrations. Maybe having everything versioned is better, but I prefer to evolve the database manually, just as you would if the Database Administration team were a different division of the company and you had to create a ticket for every schema change.

Obviously, I can work with migrations too, but here I preferred to be a bit old-school.

Everybody knows that floating-point arithmetic is a pain. The first thing I did was use cents becouse that way I can completely avoid those issues. Ah! Then I only need to convert values to decimals in the output.

## Domain

Business model. This is where everything the business knows lives, using its own language.

There's one thing worth mentioning. This system allows the creation of multiple machines, each with its own state. I didn't read anything in the statement about this, but I implemented it to make the solution more extensible and to make something like a vending machine fleet possible.

### VendingMachine

This is the core of the Domain. It provides the required functionality and business rules or, in Hexagonal Architecture terms, the Domain itself.

As described in the statement, there are three available actions for the client:

1. **Insert coin**: Accept coins from the client.
2. **Return coins**: The client gets their coins back.
3. **Select product**: Deliver the product, return any change, and calculate the remaining balance.

And there are three available actions for the technician:

1. **Refill slot**: Fill a slot with products.
2. **Refill coins**: Add coins for change.
3. **Withdraw coins**: Remove excess change.

### Machine

This folder contains everything inside the machine:

1. **Slots**: Where the products are stored.
2. **CoinMachine**: The "black box" inside the machine that controls everything related to coins.

## Application

Interaction layer. It provides access to the Domain from the interface, console, API, etc. Here you can find the Use Cases.

In this folder you can see two subfolders representing the different roles: Client and Technician.

### UseCase

As in the Domain, this layer contains the operations we can perform. These classes are intentionally small because they mainly act as a bridge between external input (Infrastructure or Ports) and internal logic (Domain).

I don't think they need much work: obtain a repository (through an interface), call a domain operation (or create domain objects when needed), and return a response.

### Command

Initially there was only one Use Case, and commands were used to provide access to Domain operations inside VendingMachine.

However, the solution became cleaner and more maintainable once I created different Use Cases.

A Command is simply another name for a Request. Every command contains the data required by a Use Case.

## Infrastructure

Well, here we have the concrete implementation details: the database, the ORM, and so on. I don't think this part is especially interesting.

This is the layer where we need to establish cooperation with the DevOps team, or where we need to make requests to the Systems Department.

We can say this is the "metal" part of the project, and one that can be replaced like puzzle pieces.

For example, today we use MySQL, but maybe MariaDB would be enough, or perhaps PostgreSQL would be preferred... or maybe we have an AS/400 and can connect to that hardcore system instead (I know because I've done it).

Ah! This is also where docker-compose finds its alma mater 😅

## Improvements

In every UseCase, you can find this:

```PHP
        } catch (Throwable $e) {
            // TODO: Implement diferents exceptions and if machine becomes unavailable, set state as out of order
            throw $e;
```

If we want to deploy to production, this "TODO" needs to be implemented, with two variants. Clients gets "Out of order" but Tecnics recibes an inform about what happends. This requires some more iterations.
