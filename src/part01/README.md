Part 01: Introduction
=====================

This example shows how a test for a class with file system usage might look like.
A big drawback is the extensive clean up which is required before and after the
test runs so that there is a clean environment available. We need to make sure
that the file and directory that will be created during the test runs don’t exist
before the test starts, and after the test we should remove the stuff. Both
applies also if we would use the `/tmp` directory here.

In constrast, the test using vfsStream is much simpler: set up consists of
configuring vfsStream, and no clean up after test run is required. Also, in case
the test fails and clean up doesn't run there will be no leftovers, as everything
happens in memory.

The tests itself aren't that much different except that we can utilize vfsStream
functionality to verify the correct result instead of relying on file system
functions as well.


* Next: [Part 02: Permissions](https://github.com/mikey179/vfsStream-examples/tree/master/src/part02)
