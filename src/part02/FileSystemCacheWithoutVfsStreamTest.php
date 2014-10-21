<?php
namespace org\bovigo\vfs\examples\part02;

class FileSystemCacheWithoutVfsStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * ensure that the directory and file are not present from previous run
     */
    private function clean() {
        if (file_exists(__DIR__ . '/cache/example')) {
            unlink(__DIR__ . '/cache/example');
        }

        if (file_exists(__DIR__ . '/cache')) {
            rmdir(__DIR__ . '/cache');
        }
    }

    public function setUp() {
        $this->clean();
    }

    public function tearDown() {
        $this->clean();
    }

    /**
     * @test
     */
    public function directoryIsCreatedWith0700ByDefault() {
        $cache = new FileSystemCache(__DIR__ . '/cache');
        $cache->store('example', ['bar' => 303]);
        if (DIRECTORY_SEPARATOR === '\\') {
            $this->assertEquals(40777, decoct(fileperms(__DIR__ . '/cache')));
        } else {
            $this->assertEquals(40700, decoct(fileperms(__DIR__ . '/cache')));
        }
    }

    /**
     * @test
     */
    public function directoryIsCreatedWithProvidedPermissions() {
        umask(0); // need to fixate umask, otherwise assertion might fail
        $cache = new FileSystemCache(__DIR__ . '/cache', 0770);
        $cache->store('example', ['bar' => 303]);
        if (DIRECTORY_SEPARATOR === '\\') {
            $this->assertEquals(40777, decoct(fileperms(__DIR__ . '/cache')));
        } else {
            $this->assertEquals(40770, decoct(fileperms(__DIR__ . '/cache')));
        }
    }
}
