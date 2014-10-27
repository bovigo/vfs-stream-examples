<?php
namespace org\bovigo\vfs\examples\part06;
use org\bovigo\vfs\vfsStream;

class SetupWithArrayStructure extends \PHPUnit_Framework_TestCase {

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
        $this->assertTrue($root->hasChild('examples/test.php'));
    }
}
