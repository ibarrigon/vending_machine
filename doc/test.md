# Test

[< Go back](../README.md)

This is the section where I put most of my effort using AI. Why? Because I defined the rules, and AI helps me refine and complete the test coverage around them.

I started with unit tests, obviously. Those are usually straightforward: given an input, I expect a specific output.

Then I moved to property-based testing… and at first I was thinking: *“WTF is this?”*  
But after reading about it, it started to make sense. Property-based testing is not about specific examples, but about rules that must always hold true. In contrast, unit tests are: *if I put X, I expect Y*. It’s simply another layer of validation.

And here is what I’ve learned:

- **Unit tests** are about *what the system needs to do*
- **Property-based tests** (or “rule tests”) are about *what should never be broken*

You might say: *“Hey! but there are duplicated tests!”*  
And yes, I agree… to some degree.

It is true that some behaviors checked in property-based tests can also appear in unit tests. However, I believe that using both improves the understanding of the system:

- Unit tests show concrete expected scenarios
- Property-based tests express system-wide rules and invariants

Together, they provide better coverage and a clearer mental model of how the system behaves.

## Unit Test

Like a movie or serie: *Previously...*
I have mixed feelings about this type of test. In this case, it is good enough, but in general I usually prefer what I call component tests.

For example, we have a `VendingMachine`, right? This machine contains a `CoinMachine`. If the `CoinMachine` never works independently from the `VendingMachine`, then why focus on unit tests? Would it not be more useful to test how both parts interact as a complete component?

However, that is not the case here. The `CoinMachine` is isolated enough that testing it separately does not require much effort, so I am completely fine with unit tests in this situation.

I am not sure if I explained myself well, but that is my personal opinion. Of course, when working in a team, I follow the rules and conventions agreed upon by everyone.

*And now...*
What I mean is what you can find in "Functional test". Yes, obviously, you have less functional test than unit test, becouse the layer where you are working is superior, isn't it? I prefer this tests, but only if it completes all the routes (coverage) the code can be executed.

## Integration

I create some integration test, and for it, there is a BD, but not the same as normally use. Every time you execute the test, the BD is truncated to avoid problems.
