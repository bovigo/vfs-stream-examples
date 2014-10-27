<?php
namespace org\bovigo\vfs\examples\part07;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\visitor\vfsStreamPrintVisitor;

class Inspection extends \PHPUnit_Framework_TestCase {

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
        vfsStream::setup('root', null, $structure);
        vfsStream::inspect(new vfsStreamPrintVisitor());
    }
}
