# Docker

[< Go back](../README.md)

Maybe you'll look at it and think: *"WTF!"*

To be fair, that's exactly what I thought at some points too.

I used AI to bootstrap the whole setup, iterated on it a few times, and once everything was working, I moved on to solving the actual challenge.

I started with a very sophisticated prompt:

> "I want an Nginx server, a PHP container, and a database with persistent volumes."

That's it.

I had to remove some Symfony defaults and tweak a few things here and there, but nothing particularly interesting happened after that.

Want to know which images I created? Just search for any image whose name starts with `vending_machine_`.
