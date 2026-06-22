# Hello

[< Go back](../README.md)

Sorry, I put some effort into explaining why and how I decided to do things (or not do them), but this document is too short to really cover everything that happened while I was evolving the code (like a Pokémon... sorry for the joke).

I also didn't make the commits in the proper order.

Do you know that feeling when something takes three hours of work, but it feels like only five minutes? Well, that's what happened here.

I can only say that one of my references is a novel called *The Blue Nowhere* (or *La estancia azul* in Spanish).

## Design Decisions

I decided to use PHP, Symfony, and MySQL.

In my opinion, a database was probably not strictly necessary, but I included one for traceability and to maintain a consistent machine state.

You can shut the machine down (not physically, of course), and when it comes back up, it still has the last inventory, the remaining change inside the machine, and even the coins inserted by the previous client. I know a real vending machine would probably return those coins, but I made a small concession there.

I didn't implement any security features such as roles or permissions, but I understand that an API with two different user types would require them. For example, a client does not need to know that technician operations even exist.

I use Doctrine to read from and write to the database, but I have mixed feelings about migrations. Maybe having everything versioned is objectively better, but I prefer evolving the database manually, just as you would if the Database Administration team belonged to a different department and you had to create a ticket for every schema change.

Of course, I can work with migrations too, but here I preferred to be a little old-school.

Everybody knows that floating-point arithmetic is a pain. One of the first decisions I made was to store monetary values in cents. That way I can completely avoid precision issues. Then I only need to convert values to decimals when presenting the output.

I also decided to keep the price inside the `Product` entity and treat it as immutable.

## Versions

1. PHP 8.4
2. Symfony 8.1
3. MySQL 8.4
