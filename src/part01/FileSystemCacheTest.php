<?php
namespace org\bovigo\vfs\examples\part01;

class FileSystemCacheTest extends \PHPUnit_Framework_TestCase
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
    public function createsDirectoryIfNotExists() {
        $cache = new FileSystemCache(__DIR__ . '/cache');
        $cache->store('example', ['bar' => 303]);
        $this->assertFileExists(__DIR__ . '/cache');
    }

    /**
     * @test
     */
    public function storesDataInFile() {
        $cache = new FileSystemCache(__DIR__ . '/cache');
        $cache->store('example', ['bar' => 303]);
        $this->assertEquals(
                ['bar' => 303],
                unserialize(file_get_contents(__DIR__ . '/cache/example'))
        );
    }
}
