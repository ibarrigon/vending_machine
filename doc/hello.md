# Hello

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

## Versions

1. php 8.4
2. symfony 8.1
3. Mysql 8.4
