Part 01: Introduction
=====================

This example shows how a test for a class with file system usage might look like.
A big drawback is the extensive clean up which is required before and after the
test runs so that there is a clean environment available.

In constrast, the test using vfsStream is much simpler: set up consists of
configuring vfsStream, and no clean up after test run is required. Also, in case
the test fails and clean up doesn't run there will be no leftovers, as everything
happens in memory.
