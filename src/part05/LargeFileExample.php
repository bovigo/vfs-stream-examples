<?php
namespace org\bovigo\vfs\examples\part05;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\content\LargeFileContent;

class LargeFileExample extends \PHPUnit_Framework_TestCase {

    /**
     * @test
     */
    public function largeFileIsLarge() {
        $root      = vfsStream::setup();
        $largeFile = vfsStream::newFile('large.txt')
                              ->withContent(LargeFileContent::withGigabytes(100))
                              ->at($root);
        $this->assertEquals(107374182400, filesize($largeFile->url()));
    }
}