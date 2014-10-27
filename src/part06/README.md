Part 06: Other setup possibilities
==================================

In most test, creating the vfsStream environment using `vfsStream::setup()` is
sufficient. Sometimes, however, we need to create a larger file system structure.
vfsStream offers two possibilities for this: one is two create a file system by
specifying its structure in an array and passing it to `vfsStream::setup()` as
third parameter which will use the given array to create the structure from it.

Arrays will become directories with their key as directory name, and strings
become files with their key as file name and their value as file content. Please
keep in mind that defining a value for the files is mandatory or the file will
not be created. Putting the key into brackets will create a block device instead
of a file.

An alternative possibility is to create the file system structure by copying an
existing directory structure from the real file system. This can be helpful when
you already have test which use an existing structure in the real file system,
and want to add new tests using vfsStream. Please note that file contents will
only be copied if they do not exceed the specified `$maxFileSize` parameter
which needs to be given in bytes. If it is not set it defaults to 1,024 KB.

* Previous: [Part 05: Mocking large files](https://github.com/mikey179/vfsStream-examples/tree/master/src/part05)
* Next: [Part 07: Structure inspection](https://github.com/mikey179/vfsStream-examples/tree/master/src/part07)
