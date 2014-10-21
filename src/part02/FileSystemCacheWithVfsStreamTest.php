<?php
namespace org\bovigo\vfs\examples\part02;
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
    public function directoryIsCreatedWith0700ByDefault() {
        $cache = new FileSystemCache($this->root->url() . '/cache');
        $cache->store('example', ['bar' => 303]);
        $this->assertEquals(
                0700,
                $this->root->getChild('cache')->getPermissions()
        );
    }

    /**
     * @test
     */
    public function directoryIsCreatedWithProvidedPermissions() {
        $cache = new FileSystemCache($this->root->url() . '/cache', 0770);
        $cache->store('example', ['bar' => 303]);
        $this->assertEquals(
                0770,
                $this->root->getChild('cache')->getPermissions()
        );
    }
}
