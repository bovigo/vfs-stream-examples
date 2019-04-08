<?php
namespace org\bovigo\vfs\examples\part02;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class FileSystemCacheWithVfsStreamTest extends TestCase
{
    private $root;

    public function setUp(): void {
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
