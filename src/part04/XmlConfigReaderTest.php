<?php
/**
 * This file is part of 1and1/stubbles-admin.
 *
 * @package  com\oneandone\stubbles\admin
 */
namespace com\oneandone\stubbles\admin;
use org\bovigo\vfs\vfsStream;
use stubbles\lang;
/**
 * Test for com\oneandone\stubbles\admin\XmlConfigReader.
 *
 * @group  core
 */
class XmlConfigReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  XmlConfigReader
     */
    private $xmlConfigReader;
    /**
     * config directory
     *
     * @type  org\bovigo\vfs\vfsDirectoy
     */
    private $root;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->root = vfsStream::setup();
        vfsStream::newFile('apps.xml')
                 ->at($this->root)
                 ->withContent('<?xml version="1.0" encoding="utf-8"?>
<apps>
  <app id="balance">
    <title>Load Balancer Host</title>
    <vhost id="balance" title="Test host for load balancer"/>
    <server env="online" host="stubbles01.schlund.de" ip="172.23.4.194"/>
  </app>
  <app id="ovm-landing-pads" scmUri="https://svn.1and1.org/svn/memphis/shops/ovm-landing-pads">
    <title>Online Marketing Landing Pads</title>
    <vhost id="ovm-eue-de" title="I Landing-Pad DE" uriTemplate="http://om$stage.$server/" liveUri="http://om.1und1.de/"/>
    <vhost id="ovm-dsl-eue-de" title="DSL om.dsl.1und1.de" liveUri="http://om.dsl.1und1.de/"/>
    <server env="stage" host="wsstubblesfedev01.fe.server.lan"/>
    <server env="test" host="wsstubblesfetest01.fe.server.lan"/>
    <server env="ac1" host="ac1wsstubblesfea01.fe.server.lan"/>
    <server env="deploy" host="wsstubblesdeploy01.ops.server.lan" deploymentEnv="online"/>
    <server env="online" host="wsstubblesfea01.fe.server.lan"/>
    <server env="online" host="wsstubblesfeadmin01.fe.server.lan" syncVhost="false"/>
  </app>
  <app id="app-without-server-and-vhost" defaultSyncType="otherSync">
    <title>Some other app</title>
    <preventClearCache syncType="otherSync"/>
    <clearCacheCommand class="com\oneandone\stubbles\admin\cache\OtherClearCacheCommand"/>
  </app>
  <app id="another-app">
      <title>Another example app</title>
      <vhost id="example" title="Example instance" liveUri="http://example.1und1.de/"/>
      <server env="deploy" host="wsstubblesdeploy01.ops.server.lan" deploymentEnv="online"/>
      <server env="online" host="wsstubblesfea01.fe.server.lan"/>
  </app>
</apps>');
        $this->xmlConfigReader = new XmlConfigReader(vfsStream::url('root'));
    }

    /**
     * @test
     */
    public function isDefaultImplementation()
    {
        $refClass = lang\reflect('com\oneandone\stubbles\admin\ConfigReader');
        $this->assertTrue($refClass->hasAnnotation('ImplementedBy'));
        $this->assertEquals(get_class($this->xmlConfigReader),
                            $refClass->getAnnotation('ImplementedBy')
                                     ->getDefaultImplementation()
                                     ->getName()
        );
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $constructor = lang\reflectConstructor($this->xmlConfigReader);
        $this->assertTrue($constructor->hasAnnotation('Inject'));

        $parameters = $constructor->getParameters();
        $this->assertTrue($parameters[0]->hasAnnotation('Named'));
        $this->assertEquals(
                'stubbles.config.path',
                $parameters[0]->annotation('Named')->getName()
        );
        $this->assertTrue($parameters[1]->hasAnnotation('Property'));
        $this->assertEquals(
                'com.oneandone.stubbles.admin.config',
                $parameters[1]->getAnnotation('Property')->getValue()
        );
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\FileNotFoundException
     */
    public function throwsFileNotFoundExceptionWhenConfigFileIsMissing()
    {
        $xmlConfigReader = new XmlConfigReader(vfsStream::url('root'), 'doesNotExist.xml');
        $xmlConfigReader->getApplications();
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\ConfigurationException
     */
    public function throwsConfigurationExceptionWhenConfigFileIsWrong()
    {
        vfsStream::newFile('wrong.xml')
                 ->at($this->root);
        $xmlConfigReader = new XmlConfigReader(vfsStream::url('root'), 'wrong.xml');
        $xmlConfigReader->getApplications();
    }

    /**
     * @test
     */
    public function doesNotHaveApplicationIfNotConfigured()
    {
        $this->assertFalse($this->xmlConfigReader->hasApplication('no-such-application'));
    }

    /**
     * @test
     */
    public function hasApplicationIfConfigured()
    {
        $this->assertTrue($this->xmlConfigReader->hasApplication('ovm-landing-pads'));
    }

    /**
     * @test
     */
    public function returnsListOfAvailableApplicationIds()
    {
        $this->assertEquals(['balance',
                             'ovm-landing-pads',
                             'app-without-server-and-vhost',
                             'another-app'
                            ],
                            $this->xmlConfigReader->getApplicationIds()
        );
    }

    /**
     * @test
     */
    public function getNonExistingApplicationReturnsNull()
    {
        $this->assertNull($this->xmlConfigReader->getApplication('no-such-application'));
    }

    /**
     * @test
     */
    public function getExistingApplication()
    {
        $app = new Application('ovm-landing-pads', 'Online Marketing Landing Pads');
        $app->setScmUri('https://svn.1and1.org/svn/memphis/shops/ovm-landing-pads');
        $app->addVhost(new Vhost('ovm-eue-de', 'I Landing-Pad DE'))->setUriTemplate('http://om$stage.$server/')->setLiveUri('http://om.1und1.de/');
        $app->addVhost(new Vhost('ovm-dsl-eue-de', 'DSL om.dsl.1und1.de'))->setLiveUri('http://om.dsl.1und1.de/');
        $app->addServer(new Server('stage', 'wsstubblesfedev01.fe.server.lan'));
        $app->addServer(new Server('test', 'wsstubblesfetest01.fe.server.lan'));
        $app->addServer(new Server('ac1', 'ac1wsstubblesfea01.fe.server.lan'));
        $app->addServer(new Server('deploy', 'wsstubblesdeploy01.ops.server.lan'))->setDeploymentEnv('online');
        $app->addServer(new Server('online', 'wsstubblesfea01.fe.server.lan'));
        $app->addServer(new Server('online', 'wsstubblesfeadmin01.fe.server.lan'))->ignoreInVhostSync();
        $this->assertEquals($app,
                            $this->xmlConfigReader->getApplication('ovm-landing-pads')
        );
    }

    /**
     * @test
     */
    public function returnsListOfAllConfiguredApplications()
    {
        $app1 = new Application('balance', 'Load Balancer Host');
        $app1->addVhost(new Vhost('balance', 'Test host for load balancer'));
        $app1->addServer(new Server('online', 'stubbles01.schlund.de'))->setIp('172.23.4.194');
        $app2 = new Application('ovm-landing-pads', 'Online Marketing Landing Pads');
        $app2->setScmUri('https://svn.1and1.org/svn/memphis/shops/ovm-landing-pads');
        $app2->addVhost(new Vhost('ovm-eue-de', 'I Landing-Pad DE'))->setUriTemplate('http://om$stage.$server/')->setLiveUri('http://om.1und1.de/');
        $app2->addVhost(new Vhost('ovm-dsl-eue-de', 'DSL om.dsl.1und1.de'))->setLiveUri('http://om.dsl.1und1.de/');
        $app2->addServer(new Server('stage', 'wsstubblesfedev01.fe.server.lan'));
        $app2->addServer(new Server('test', 'wsstubblesfetest01.fe.server.lan'));
        $app2->addServer(new Server('ac1', 'ac1wsstubblesfea01.fe.server.lan'));
        $app2->addServer(new Server('deploy', 'wsstubblesdeploy01.ops.server.lan'))->setDeploymentEnv('online');
        $app2->addServer(new Server('online', 'wsstubblesfea01.fe.server.lan'));
        $app2->addServer(new Server('online', 'wsstubblesfeadmin01.fe.server.lan'))->ignoreInVhostSync();
        $app3 = new Application('app-without-server-and-vhost', 'Some other app');
        $app3->setDefaultSyncType('otherSync');
        $app3->preventCacheClearingOn('otherSync');
        $app3->setClearCacheCommandClass('com\oneandone\stubbles\admin\cache\OtherClearCacheCommand');
        $app4 = new Application('another-app', 'Another example app');
        $app4->addVhost(new Vhost('example', 'Example instance'))->setLiveUri('http://example.1und1.de/');
        $app4->addServer(new Server('deploy', 'wsstubblesdeploy01.ops.server.lan'))->setDeploymentEnv('online');
        $app4->addServer(new Server('online', 'wsstubblesfea01.fe.server.lan'));
        $this->assertEquals(['balance'                      => $app1,
                             'ovm-landing-pads'             => $app2,
                             'app-without-server-and-vhost' => $app3,
                             'another-app'                  => $app4
                            ],
                            $this->xmlConfigReader->getApplications()
        );
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\ConfigurationException
     */
    public function throwsConfigurationExceptionWhenServerConfiguredInEnvDeployButWithoutDeploymentEnv()
    {
        vfsStream::newFile('invalidDeploy.xml')
                 ->at($this->root)
                 ->withContent('<?xml version="1.0" encoding="utf-8"?>
<apps>
  <app id="ovm-landing-pads">
    <title>Online Marketing Landing Pads</title>
    <server env="deploy" host="wsstubblesdeploy01.ops.server.lan"/>
  </app>
</apps>');
        $xmlConfigReader = new XmlConfigReader(vfsStream::url('root'), 'invalidDeploy.xml');
        $xmlConfigReader->getApplications();
    }

    /**
     * @test
     * @since   1.2.0
     */
    public function returnsListOfApplicationsOnSpecificHost()
    {
        $app1 = new Application('ovm-landing-pads', 'Online Marketing Landing Pads');
        $app1->setScmUri('https://svn.1and1.org/svn/memphis/shops/ovm-landing-pads');
        $app1->addVhost(new Vhost('ovm-eue-de', 'I Landing-Pad DE'))->setUriTemplate('http://om$stage.$server/')->setLiveUri('http://om.1und1.de/');
        $app1->addVhost(new Vhost('ovm-dsl-eue-de', 'DSL om.dsl.1und1.de'))->setLiveUri('http://om.dsl.1und1.de/');
        $app1->addServer(new Server('stage', 'wsstubblesfedev01.fe.server.lan'));
        $app1->addServer(new Server('test', 'wsstubblesfetest01.fe.server.lan'));
        $app1->addServer(new Server('ac1', 'ac1wsstubblesfea01.fe.server.lan'));
        $app1->addServer(new Server('deploy', 'wsstubblesdeploy01.ops.server.lan'))->setDeploymentEnv('online');
        $app1->addServer(new Server('online', 'wsstubblesfea01.fe.server.lan'));
        $app1->addServer(new Server('online', 'wsstubblesfeadmin01.fe.server.lan'))->ignoreInVhostSync();
        $app2 = new Application('another-app', 'Another example app');
        $app2->addVhost(new Vhost('example', 'Example instance'))->setLiveUri('http://example.1und1.de/');
        $app2->addServer(new Server('deploy', 'wsstubblesdeploy01.ops.server.lan'))->setDeploymentEnv('online');
        $app2->addServer(new Server('online', 'wsstubblesfea01.fe.server.lan'));
        $this->assertEquals(['ovm-landing-pads' => $app1,
                             'another-app'      => $app2
                            ],
                            $this->xmlConfigReader->getApplicationsOnHost('wsstubblesfea01.fe.server.lan')
        );
    }

    /**
     * @test
     * @since   1.3.0
     */
    public function returnsUnifiedListOfServers()
    {
        $server1 = new Server('online', 'stubbles01.schlund.de');
        $server1->setIp('172.23.4.194');
        $server3 = new Server('online', 'wsstubblesfeadmin01.fe.server.lan');
        $server3->ignoreInVhostSync();
        $this->assertEquals([$server1,
                             new Server('online', 'wsstubblesfea01.fe.server.lan'),
                             $server3
                            ],
                            $this->xmlConfigReader->getServers('online')
        );
    }
}
