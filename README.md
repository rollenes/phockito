# Phockito - Mockito for PHP

Mocking framework inspired by Mockito for Java

Checkout [the original's website](http://mockito.org/) for the philosophy behind the API and more examples
(although be aware that this is only a partial implementation for now)

Thanks to the developers of Mockito for the inspiration, and hamcrest-php for making this easy.

## Example mocking:

```php
// Create the mock
$iterator = Phockito::mock('ArrayIterator');

// Use the mock object - doesn't do anything, functions return null
$iterator->append('Test');
$iterator->asort();

// Selectively verify execution
Phockito::verify($iterator)->append('Test');
// 1 is default - can also do 2, 3  for exact numbers, or 1+ for at least one, or 0 for never
Phockito::verify($iterator, 1)->asort();
```

If PHPUnit is available, on failure verify throws a `PHPUnit_Framework_AssertionFailedError` (looks like an assertion failure),
otherwise just throws an `Exception`

## Example stubbing:

```php
// Create the mock
$iterator = Phockito::mock('ArrayIterator');

// Stub in a value
Phockito::when($iterator->offsetGet(0))->return('first');

// Prints "first"
print_r($iterator->offsetGet(0));

// Prints null, because get(999) not stubbed
print_r($iterator->offsetGet(999));
```

Alternative API, jsMockito style

```php
// Stub in a value
Phockito::when($iterator)->offsetGet(0)->return('first');
```

## Spies

Mocks are full mocks - method calls to unstubbed function always return null, and never call the parent function.

You can also create partial mocks by calling `spy` instead of `mock`. With spies, method calls to unstubbed functions
call the parent function.

Because spies are proper subclasses, this lets you stub in methods that are called by other methods in a class

```php
class A {
	function Foo(){ return 'Foo'; }
	function Bar(){ return $this->Foo() . 'Bar'; }
}

// Create a mock
$mock = Phockito::mock('A');
print_r($mock->Foo()); // 'null'
print_r($mock->Bar()); // 'null'

// Create a spy
$spy = Phockito::spy('A');
print_r($spy->Foo()); // 'Foo'
print_r($spy->Bar()); // 'FooBar'

// Stub a method 
Phockito::when($spy)->Foo()->return('Zap');
print_r($spy->Foo()); // 'Zap'
print_r($spy->Bar()); // 'ZapBar'
```

## Differences from Mockito

#### Stubbing methods more flexible

In Mockito, the methods when building a stub are limited to thenReturns, thenThrows. In Phockito, you can use any method
as long as it has 'return' or 'throw' in it, so `Poktio::when(...)->return(1)->thenReturn(2)` is fine.

#### Verify 'times' argument changed

In Mockito, the 'times' argument to verify is an object of interface VerificationMode (like returned by the functions times,
atLeastOnce, etc).

For now we just take either an integer, or an integer followed by '+'. It's not extensible.

#### Callback instead of answers

In Mockito, you can return dynamic results from a stubbed method by calling thenAnswer with an instance of an object
that has a specific method. In Phockito you call thenCallback with a `callback` argument, which gets called with the
arguments the stubbed method was called with.

#### Default arguments

PHP has default arguments, unlike Java. If you don't specify a default argument in your stub or verify matcher, it'll
match the default argument.

```php
class Foo {
  function Bar($a, $b = 2){ /* NOP */ }
}

// Create the mock
$mock = Phockito::mock('Foo');

// Set up a stub
Phockito::when($mock->Bar(1))->return('A');

$mock->Bar(1); // Returns 'A'
$mock->Bar(1, 2); // Also returns 'A'
$mock->Bar(1, 3); // Returns null, since no stubed return value matches
```

#### Return typing

Mockito returns a type-compatible false, based on the declared return type. We don't have defined type values in
PHP, so we always return null. TODO: Support using phpdoc @return when declared.

## TODO

 - Mochito-specific hamcrest matchers (anyString, etc)
 - Ordered verification

## License

Copyright (C) 2011 Hamish Friedlander / SilverStripe. Distributable under the same license as SilverStripe.

Hamcrest-php is under it's own license - see hamcrest-php/LICENSE.txt.
