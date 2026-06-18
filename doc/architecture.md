# Architecture

# Previously decisions

I decided to use PHP, Symfony and MySQL. In my opinion, maybe the data base was not really needed, but I included one to 
trazability and to generate consistent status of the machine.

You can Shut down (not in reality) and when comes up, you have the last inventory, the last change inside the machine and
the coins the last client put (I know that vending machine lost it, but I make concesion there for the client)

I don't put any security like ROLES or something similar, but I understand an API with to user types, required it. For example,
one client dont need to know there's admin part in the API (in this case, Tecnician operations)

I use doctrine to read and save in BD, but I don't like migrations. Maybe you can have everything versioned, but I prefer to 
evolve the BD manually, like you do if the DB Administrator is another division of the company, creating an issue with the alter. 

Obviously, I can work with migrations too but here I prefer to be an "old school man".

# Domain

Business model. Here is everything our business knows, using its own language

## VendingMachine
This is the core of the Domain. It provide the required funcionality with business rules, or, in DDD, Domain.

Like the statement describe, you have 3 available actions for the Client:
1. __Insert coin__: Code to accept coins from client
2. __Return coins__: The client gets his coins
3. __Select product__: Get the product, the change and remaining balance

And there is 3 available actions for the Tecnician:
1. __Refill slot__: Fill the slot
2. __Refill coins__: Add coins for change
3. __Retire coins__: Remove exceded change

## Machine
This folder contains everithing is insede the machine:
1. __Slots__: Where the products are located
2. __CoinMachine__: Its the "black" box inside the machine that controls everything about the coins.

# Application

Interaction, provide access to Domain from the "interface", "console", "api", ... Here you can find UseCases.
In this folder you can see two subfolders with the differents roles. Client and Tecnician.

## UseCase
The action.

## Command
Initially there was only one UseCase and with the command we can provide access to Domain operation inside VendingMachine,
but finally, everithings is more accurated (and maintainable) if we have differents UseCase.

Command is another name for Request. Every command contains the required data for the UseCase
