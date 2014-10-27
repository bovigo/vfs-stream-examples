<?php
namespace org\bovigo\vfs\examples\part06;
use org\bovigo\vfs\vfsStream;

class SetupByCopyFromFileSystem extends \PHPUnit_Framework_TestCase {

    /**
     * @test
     */
    public function example() {
        $root = vfsStream::setup();
        vfsStream::copyFromFileSystem(__DIR__ . '/..', $root);
        $this->assertTrue($root->hasChild('part06/SetupByCopyFromFileSystem.php'));
    }
}
