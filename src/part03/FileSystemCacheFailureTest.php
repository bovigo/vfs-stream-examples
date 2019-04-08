<?php
namespace org\bovigo\vfs\examples\part03;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class FileSystemCacheFailureTest extends TestCase
{
    private $root;

    public function setUp(): void {
        $this->root = vfsStream::setup();
    }

    /**
     * @test
     */
    public function returnsTrueWhenNoFailureOccurs() {
        $cache = new FileSystemCache($this->root->url() . '/cache');
        $this->assertTrue($cache->store('example', ['bar' => 303]));
    }

    /**
     * @test
     */
    public function returnsFalseWhenFailureOccurs() {
        vfsStream::newFile('example', 0000)
                 ->withContent('notoverwritten')
                 ->at($this->root);
        $cache = new FileSystemCache($this->root->url());
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("failed to open stream");
        $cache->store('example', ['bar' => 303]);
    }

    /**
     * @test
     */
    public function returnsFalseWhenFailureOccursAlternative() {
        vfsStream::setQuota(10);
        $cache = new FileSystemCache($this->root->url());
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("possibly out of free disk space");
        $cache->store('example', ['bar' => 303]);
    }
}
