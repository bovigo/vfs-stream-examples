<?php
namespace org\bovigo\vfs\examples\part04;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class PropertyBasedDatabaseConfigurationsTest extends TestCase {
    private $propertyBasedConfigurations;
    private $configFile;

    /**
     * set up test environment
     */
    public function setUp(): void {
        $this->propertyBasedConfigurations = new PropertyBasedDatabaseConfigurations($this->createConfig());
    }

    /**
     * creates config folder and returns its url
     *
     * @param   string  name of config file
     * @return  string
     */
    private function createConfig($filename = 'rdbms.ini') {
        $root = vfsStream::setup();
        $this->configFile = vfsStream::newFile($filename)->at($root);
        return $root->url();
    }

    /**
     * @test
     */
    public function containsConfigWhenPresentInFile() {
        $this->configFile->setContent('[foo]
dsn="mysql:host=localhost;dbname=example"');
        $this->assertTrue($this->propertyBasedConfigurations->contain('foo'));
    }

    /**
     * @test
     */
    public function containsConfigWhenNotPresentInFileButDefaultAndFallbackEnabled() {
        $this->configFile->setContent('[default]
dsn="mysql:host=localhost;dbname=example"');
        $this->assertTrue($this->propertyBasedConfigurations->contain('foo'));
    }

    /**
     * @test
     */
    public function doesNotContainConfigWhenNotPresentInFileAndNoDefaultAndFallbackEnabled() {
        $this->configFile->setContent('[bar]
dsn="mysql:host=localhost;dbname=example"');
        $this->assertFalse($this->propertyBasedConfigurations->contain('foo'));
    }

    /**
     * @test
     */
    public function doesNotContainConfigWhenNotPresentInFileAndFallbackDisabled() {
        $propertyBasedConfigurations = new PropertyBasedDatabaseConfigurations($this->createConfig() ,'rdbms', false);
        $this->configFile->setContent('[default]
dsn="mysql:host=localhost;dbname=example"');
        $this->assertFalse(
                $propertyBasedConfigurations->contain('foo')
        );
    }

    /**
     * @test
     */
    public function throwsExceptionWhenDsnPropertyMissing() {
        $this->configFile->setContent('[foo]
username="root"');
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Missing dsn property in database configuration with id foo");
        $this->propertyBasedConfigurations->get('foo');
    }

    /**
     * @test
     */
    public function returnsConfigWhenPresentInFile() {
        $this->configFile->setContent('[foo]
dsn="mysql:host=localhost;dbname=foo"');
        $this->assertEquals(
                ['dsn' => 'mysql:host=localhost;dbname=foo'],
                $this->propertyBasedConfigurations->get('foo')
        );
    }

    /**
     * @test
     */
    public function returnsDefaultConfigWhenNotPresentInFileButDefaultAndFallbackEnabled() {
        $this->configFile->setContent('[default]
dsn="mysql:host=localhost;dbname=example"');
        $this->assertEquals(
                ['dsn' => 'mysql:host=localhost;dbname=example'],
                $this->propertyBasedConfigurations->get('foo')
        );
    }

    /**
     * @test
     */
    public function throwsExceptionWhenNotPresentInFileAndNoDefaultAndFallbackEnabled() {
        $this->configFile->setContent('[bar]
dsn="mysql:host=localhost;dbname=example"');
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("No database configuration known for database requested with id foo");
        $this->propertyBasedConfigurations->get('foo');
    }

    /**
     * @test
     */
    public function throwsExceptionWhenNotPresentInFileAndFallbackDisabled() {
        $propertyBasedConfigurations = new PropertyBasedDatabaseConfigurations($this->createConfig() ,'rdbms', false);
        $this->configFile->setContent('[default]
dsn="mysql:host=localhost;dbname=example"');
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("No database configuration known for database requested with id foo");
        $propertyBasedConfigurations->get('foo');
    }

    /**
     * @test
     */
    public function usesDifferentFileWhenDescriptorChanged() {
        $propertyBasedConfigurations = new PropertyBasedDatabaseConfigurations($this->createConfig('rdbms-test.ini') , 'rdbms-test');
        $this->configFile->setContent('[foo]
dsn="mysql:host=localhost;dbname=example"');
        $this->assertEquals(
                ['dsn' => 'mysql:host=localhost;dbname=example'],
                $propertyBasedConfigurations->get('foo')
        );
    }
}
