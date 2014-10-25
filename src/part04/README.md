Part 04: Test with full disc
============================

In [part 3](https://github.com/mikey179/vfsStream-examples/tree/master/src/part03) we made sure that our class can cope with failure from the file system
and communicate it correctly to the client. However, there's still a catch: what
if the disc is full? In such cases, the file system function doesn't return with
an error, but rather with the amount of bytes written. To ensure our class works
correct we need to check that the correct amount of bytes was written, otherwise
we just have corrupt data.


