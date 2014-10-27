Part 05: Mocking large files
============================

Working with large files with several hundred megabytes or even gigabytes is
possible, but how to test that the code behaves correctly when processing such
files? Of course one could put such a large file into the test directory, but
this is impractical and simply bloats the size of the application or library.
Instead, let's utilize the possibility of a virtual file system where a file can
pretend to be that large, but doesn't necessarily have to contain that much data.
With vfsStream we can mock large files with arbitrary sizes. Instead of putting
a simple string as content into the file we use a mock object.

Beside `LargeFileContent::withGigabytes()` there’s also
`LargeFileContent::withMegaBytes()` and `LargeFileContent::withKiloybytes()`.

Please note that the content of the large file when read consists of spaces only.
However, if one writes something into it and reads this portion of the file
afterwards, it will of course contain what was originally written at these
offsets.

* Previous: [Part 04: Putting the strength to play: different config files](https://github.com/mikey179/vfsStream-examples/tree/master/src/part04)
* Next: [Part 06: Other setup possibilities](https://github.com/mikey179/vfsStream-examples/tree/master/src/part06)
