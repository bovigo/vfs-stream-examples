<?php
namespace org\bovigo\vfs\examples\part01;
use org\bovigo\vfs\vfsStream;

class FileSystemCacheWithVfsStreamTest extends \PHPUnit_Framework_TestCase
{
    private $root;

    public function setUp() {
        $this->root = vfsStream::setup();
    }

    /**
     * @test
     */
    public function createsDirectoryIfNotExists() {
        $cache = new FileSystemCache($this->root->url() . '/cache');
        $cache->store('example', ['bar' => 303]);
        $this->assertFileExists($this->root->url() . '/cache');
    }

    /**
     * @test
     */
    public function storesDataInFile() {
        $cache = new FileSystemCache($this->root->url() . '/cache');
        $cache->store('example', ['bar' => 303]);
        $this->assertTrue($this->root->hasChild('cache/example'));
        $this->assertEquals(
                ['bar' => 303],
                unserialize($this->root->getChild('cache/example')->getContent())
        );
    }
}
