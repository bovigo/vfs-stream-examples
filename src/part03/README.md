Part 03: Testing for failure
============================

Until now our class under test gives no feedback on whether storing the data
was successful. However, file operations may fail for various reasons, and we
would like that a) our code can handle failure, and b) that clients of our code
get notified about the failure. To do this, we throw an exception in case
storing the data fails.

In order to provoke a failure in the test, we put a file with insufficient
permissions into the place where the new file would be created normally. By
default this test using vfsStream will run on every platform as we learned in
[part 2](https://github.com/mikey179/vfsStream-examples/tree/master/src/part02), and of course we don't need to fiddle with the real filesystem to provoke
the error. Testing the failure scenario becomes a piece of cake now.

An alternative way to provoke failure is to pretend that we are out of free disc
space. With vfsStream we can simply set the quota, in this case to 10 bytes. The
`file_put_content()` function checks that the correct amount of bytes was written,
and returns with `false` and triggers a warning if that's not the case.

When using a quota, keep in mind the following rules:

* Only writing through stream functions (i.e. `file_put_contents()`, `fwrite()`, etc.) respects the quota. Writing directly on `vfsStreamFile` instances is not limited.
* If no quota is set disk space is considered to be unlimited.
* Each call to `vfsStream::setup()` or `vfsStreamWrapper::register()` will reset the quota to unlimited.

* Previous: [Part 02: Permissions](https://github.com/mikey179/vfsStream-examples/tree/master/src/part02)
* Next: [Part 04: Putting the strength to play: different config files](https://github.com/mikey179/vfsStream-examples/tree/master/src/part04)
