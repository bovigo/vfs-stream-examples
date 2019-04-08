Part 07: Structure inspection
=============================

Especially with larger directory structures one can get confused of how the
directory tree looks like. vfsStream provides a simple utility method to inspect
the directory structure: vfsStream::inspect(). As first parameter it expects a
vfsStreamVisitor - for simplicity and most use cases the vfsStreamPrintVisitor
should be sufficient. It prints out the directory structure, defaulting to
STDOUT, but can be changed to write to another stream (yes, even to a vfs://
stream ;-)).

vfsStream also provides the vfsStreamStructureVisitor which creates a structure
which can be used for vfsStream::create(). This way you can make sure the
overall structure after an operation like unlink() or rename() is still correct
without having to test a bunch of directories and files for existance.

If none of the delivered visitor implementations fit one can create another
implementation of the vfsStreamVisitor interface, best by extending from
vfsStreamAbstractVisitor which delivers a default implementation for the
vfsStreamVisitor::visit() method.

* Previous: [Part 06: Other setup possibilities](https://github.com/bovigo/vfs-stream-examples/tree/master/src/part06)
