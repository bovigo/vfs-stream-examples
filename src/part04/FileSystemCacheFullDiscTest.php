<?php
namespace org\bovigo\vfs\examples\part03;
use org\bovigo\vfs\vfsStream;

class FileSystemCacheFullDiscTest extends \PHPUnit_Framework_TestCase
{
    private $root;

    public function setUp() {
        $this->root = vfsStream::setup();
    }

    /**
     * @test
     */
    public function returnsFalseWhenFailureOccurs() {
        vfsStream::setQuota(10); // set quota to 10 bytes
        $cache = new FileSystemCache($this->root->url());
        $this->assertFalse($cache->store('example', ['bar' => 303]));
        $this->assertFalse($this->root->hasChild('example'));
    }
}
