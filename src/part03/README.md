Part 03: Testing for failure
============================

Until now our class under test gives no feedback on whether storing the data
was successful. However, file operations may fail for various reasons, and we
would like that a) our code can handle failure, and b) that clients of our code
get notified about the failure. To do this, we introduce a return value which
signals whether storing the data was successful. Of course, another way could be
to throw an exception on failure instead of just returning false.

In order to provoke a failure in the test, we put a file with insufficient
permissions into the place where the new file would be created normally. By
default this test using vfsStream will run on every platform as we learned in
[part 2](https://github.com/mikey179/vfsStream-examples/tree/master/src/part02), and of course we don't need to fiddle with the real filesystem to provoke
the error. Testing the failure scenario becomes a piece of cake now.
