<?php
namespace org\bovigo\vfs\examples\part07;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\visitor\vfsStreamStructureVisitor;

class AssertionByInspection extends \PHPUnit_Framework_TestCase {

    /**
     * @test
     */
    public function example() {
        $structure = [
                'examples' => [
                    'test.php'    => 'some text content',
                    'other.php'   => 'Some more text content',
                    'Invalid.csv' => 'Something else',
                ],
                'an_empty_folder' => [],
                'badlocation.php' => 'some bad content',
                '[Foo]'           => 'a block device'
        ];
        $root = vfsStream::setup('root', null, $structure);
        $this->someLogicThatChangesTheStructure($root);
        $this->assertEquals(
                [
                    'root' => [
                        'an_empty_folder' => [],
                        'badlocation.php' => 'some bad content',
                        '[Foo]'           => 'a block device'
                    ]
                ],
                vfsStream::inspect(new vfsStreamStructureVisitor())
                         ->getStructure()
        );

    }

    /**
     * this is just a mock, one could imagine much broader operations that need to be tested
     *
     * @param  vfsStreamDirectory  $root
     */
    private function someLogicThatChangesTheStructure(vfsStreamDirectory $root) {
        $root->removeChild('examples');
    }
}
