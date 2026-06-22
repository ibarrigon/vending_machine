# Test

[< Go back](../README.md)

This is probably the section where I put most of my effort using AI.

Why?

Because I defined the rules, and AI helped me refine and complete the test coverage around them.

I started with unit tests, obviously. Those are usually straightforward: given an input, I expect a specific output.

Then I moved to property-based testing and, at first, I thought:

*"WTF is this?"*

But after reading about it, it started to make sense.

Property-based testing is not about specific examples. Instead, it focuses on rules that must always hold true. In contrast, unit tests are more like: *if I put X in, I expect Y out*.

It is simply another layer of validation.

And here is what I learned:

- **Unit tests** are about *what the system needs to do*
- **Property-based tests** (or "rule tests") are about *what should never be broken*

You might say:

*"Hey! Aren't some of these tests duplicated?"*

And yes, I agree... to some degree.

It is true that some behaviors checked by property-based tests can also appear in unit tests. However, I believe that using both improves the understanding of the system:

- Unit tests demonstrate specific expected scenarios
- Property-based tests express system-wide rules and invariants

Together, they provide better coverage and a clearer mental model of how the system behaves.

## Unit Tests

Like in a movie or TV series:

*Previously...*

I have mixed feelings about this type of test.

In this project, unit tests are perfectly fine, but in general I tend to prefer what I call component tests.

For example, we have a `VendingMachine`, right?

This machine contains a `CoinMachine`.

If the `CoinMachine` never operates independently from the `VendingMachine`, then why focus exclusively on testing it in isolation? Wouldn't it be more useful to test how both parts interact as a complete component?

However, that is not really an issue here.

The `CoinMachine` is isolated enough that testing it separately requires very little effort, so I am completely comfortable with unit tests in this case.

I am not sure if I explained myself well, but that is my personal opinion.

Of course, when working in a team, I follow the testing strategy and conventions agreed upon by everyone.

*And now...*

These days I usually prefer component tests, or perhaps what many people would call integration tests.

From a higher level, they allow us to verify complete behaviors rather than individual implementation details.

## Functional Tests

Here comes the last layer: heavier, slower, and generally more expensive to maintain.

I only implemented a few functional tests for this project, but the amount of functional testing always depends on the required coverage and the complexity of the system.

## Integration Tests and E2E

### Integration Tests

I did not implement integration tests this time.

I started experimenting with a few tests around persistence and object mapping, but they require additional database setup and test data preparation.

Given the scope of the challenge, I decided to focus my effort elsewhere.

### E2E Tests

I did not implement any E2E tests for this project.

I have worked with Cypress in the past, and at Atrápalo I also executed Selenium and Behat test suites that were created and maintained by another department.

For a project of this size, I did not consider E2E tests to be the best investment of the available time.
