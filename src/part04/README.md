Part 04: Putting the strength to play: different config files
=============================================================

Another great use case for vfsStream is testing behavior with different
configuration files. Let’s assume we have a class which reads a configuration
file. It could do some tests on the configuration content, and maybe even behave
different depending on input when creating the class. Here is a simplified excerpt
from a class of the stubbles/dbal package which uses property files for database
configurations. As you can see its behavior  depends on some internal values as
on configuration file content.

With vfsStream we can now have several tests with different configuration file
contents, without having to create those files for real in the file system.
There’s also an additional value to that: the configuration used for the test is
directly inside the test. Anyone reading the test and trying to understand it
doesn’t have to open the different configuration files, but has anything that is
special in one place. In order to understand the code you only need to have open
the class under test and the test.

* Previous: [Part 03: Testing for failure](https://github.com/bovigo/vfs-stream-examples/tree/master/src/part03)
* Next: [Part 05: Mocking large files](https://github.com/bovigo/vfs-stream-examples/tree/master/src/part05)
