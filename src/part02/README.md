Part 02: Permissions
====================

We change the class to test so that the permissions for the cache directory can
be set in the constructor. Of course we want to add tests for this behavior, so
we create a two test cases: one for the default file permissions, and one for
the case that a different set of file permissions is given.

In the test without vfsStream, we have to take care of a lot of stuff: we need
to check the operating system we are running on. Now, the test doesn't make a
lot of sense when running on Windows, it will always succeed, and there isn't
much difference between the two test cases.

Furthermore, we have to explicitly calculate the permissions, and for the
expected value we have to consider the type (or remove it from the tested
value).

On a Linux system one must additionally ensure that the [umask](http://en.wikipedia.org/wiki/Umask) is considered.
This can be done by explicitly setting it as done here, or alternatively by
calculating the expected value based on the current umask value.

Take a look at the vfsStream based test: we don't have to care which exact
operating system the test is running on, and the intent of the test becomes much
clearer, as both the expected value is clearly stated, and the tested value has
a good description of where it comes from.


* Previous: [Part 01: Introduction](https://github.com/mikey179/vfsStream-examples/tree/master/src/part01)
* Next: [Part 03: Testing for failure](https://github.com/mikey179/vfsStream-examples/tree/master/src/part03)
