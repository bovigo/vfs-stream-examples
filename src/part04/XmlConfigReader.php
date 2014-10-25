<?php
/**
 * This file is part of 1and1/stubbles-admin.
 *
 * @package  com\oneandone\stubbles\admin
 */
namespace com\oneandone\stubbles\admin;
use stubbles\lang\exception\ConfigurationException;
use stubbles\lang\exception\FileNotFoundException;
/**
 * Class for reading the application configuration from an XML file.
 */
class XmlConfigReader implements ConfigReader
{
    /**
     * path to config file
     *
     * @type  string
     */
    private $configPath;
    /**
     * name config file
     *
     * @type  string
     */
    private $configFile = 'apps.xml';
    /**
     * holds the config data
     *
     * @type  array
     */
    private $config     = null;

    /**
     * constructor
     *
     * @param  string  $configPath  path to config file
     * @param  string  $configFile  name of config file
     * @Inject
     * @Named{configPath}('stubbles.config.path')
     * @Property{configFile}('com.oneandone.stubbles.admin.config')
     */
    public function __construct($configPath, $configFile = 'apps.xml')
    {
        $this->configPath = $configPath;
        $this->configFile = $configFile;
    }

    /**
     * checks whether a certain shop application exists
     *
     * @param   string  $appId
     * @return  bool
     */
    public function hasApplication($appId)
    {
        if (null === $this->config) {
            $this->config = $this->loadConfig();
        }

        return isset($this->config[$appId]);
    }

    /**
     * returns the application config
     *
     * Returns null if no such application exists.
     *
     * @param   string  $appId
     * @return  \com\oneandone\stubbles\admin\Application
     */
    public function getApplication($appId)
    {
        if (null === $this->config) {
            $this->config = $this->loadConfig();
        }

        if (isset($this->config[$appId])) {
            return $this->config[$appId];
        }

        return null;
    }

    /**
     * returns the application config
     *
     * @return  \com\oneandone\stubbles\admin\Application[]
     */
    public function getApplications()
    {
        if (null === $this->config) {
            $this->config = $this->loadConfig();
        }

        return $this->config;
    }

    /**
     * returns a list of all application ids
     *
     * @return  string[]
     */
    public function getApplicationIds()
    {
        if (null === $this->config) {
            $this->config = $this->loadConfig();
        }

        return array_keys($this->config);
    }

    /**
     * returns all applications which are installed on given host
     *
     * @param   string  $hostname
     * @return  \com\oneandone\stubbles\admin\Application[]
     * @since   1.2.0
     */
    public function getApplicationsOnHost($hostname)
    {
        if (null === $this->config) {
            $this->config = $this->loadConfig();
        }

        $applications = [];
        foreach ($this->config as $appId => $application) {
            /* @var $application Application */
            if ($application->hasInstanceOnHost($hostname)) {
                $applications[$appId] = $application;
            }
        }

        return $applications;
    }

    /**
     * returns a list of all servers in given environment
     *
     * @param   string    $environment
     * @return  \com\oneandone\stubbles\admin\Server[]
     * @since   1.3.0
     */
    public function getServers($environment)
    {
        if (null === $this->config) {
            $this->config = $this->loadConfig();
        }

        $servers = [];
        foreach ($this->config as $application) {
            foreach ($application->getServerIn($environment) as $server) {
                if (!isset($servers[$server->getHostname()])) {
                    $servers[$server->getHostname()] = $server;
                }
            }
        }

        return array_values($servers);
    }

    /**
     * loads the config from config file
     *
     * @return  array
     * @throws  \stubbles\lang\exception\FileNotFoundException
     * @throws  \stubbles\lang\exception\ConfigurationException
     */
    private function loadConfig()
    {
        $sourceFile = $this->configPath . DIRECTORY_SEPARATOR . $this->configFile;
        if (!file_exists($sourceFile)) {
             throw new FileNotFoundException($sourceFile);
        }

        $sourceXml = @simplexml_load_string(file_get_contents($sourceFile));
        if (false === $sourceXml) {
            throw new ConfigurationException('Can not parse configuration ' . $sourceFile);
        }

        $result = [];
        foreach ($sourceXml->app as $app) {
            $id = (string) $app['id'];
            $result[$id] = new Application($id, (string) $app->title);
            if (isset($app['defaultSyncType'])) {
                $result[$id]->setDefaultSyncType((string) $app['defaultSyncType']);
            }

            if (isset($app['scmUri'])) {
                $result[$id]->setScmUri((string) $app['scmUri']);
            }

            if (isset($app->server)) {
                foreach ($app->server as $server) {
                    $result[$id]->addServer($this->parseServer($server, $id));
                }
            }

            if (isset($app->vhost)) {
                foreach ($app->vhost as $vhost) {
                    $result[$id]->addVhost($this->parseVhost($vhost));
                }
            }

            if (isset($app->preventClearCache)) {
                foreach ($app->preventClearCache as $preventClearCache) {
                    $result[$id]->preventCacheClearingOn((string) $preventClearCache['syncType']);
                }
            }

            if (isset($app->clearCacheCommand) && isset($app->clearCacheCommand['class'])) {
                $result[$id]->setClearCacheCommandClass((string) $app->clearCacheCommand['class']);
            }
        }

        return $result;
    }

    /**
     * parses server data
     *
     * @param   \SimpleXMLElement  $xmlSource
     * @param   string             $appId
     * @return  \com\oneandone\stubbles\admin\Server
     * @throws  \stubbles\lang\exception\ConfigurationException
     */
    private function parseServer(\SimpleXMLElement $xmlSource, $appId)
    {
        $server = new Server((string) $xmlSource['env'], (string) $xmlSource['host']);
        if (isset($xmlSource['ip'])) {
            $server->setIp((string) $xmlSource['ip']);
        }

        if (isset($xmlSource['deploymentEnv'])) {
            $server->setDeploymentEnv((string) $xmlSource['deploymentEnv']);
        }

        if (isset($xmlSource['syncVhost']) && $this->isFalse($xmlSource['syncVhost'])) {
            $server->ignoreInVhostSync();
        }

        if (isset($xmlSource['syncApp']) && $this->isFalse($xmlSource['syncApp'])) {
            $server->ignoreInAppSync();
        }

        if ($server->getEnv() === 'deploy' && !$server->hasDeploymentEnv()) {
             throw new ConfigurationException('Server ' . $server->getHostname() . ' for application ' . $appId . ' configured as deployment server, but has no deployment environment');
        }

        return $server;
    }

    /**
     * checks whether given element is set to false
     *
     * @param  \SimpleXMLElement $xmlSource
     * @return  bool
     */
    private function isFalse(\SimpleXMLElement $xmlSource) {
        return 'false' === (string) $xmlSource;
    }

    /**
     * parses vhost data
     *
     * @param   \SimpleXMLElement  $xmlSource
     * @return  \com\oneandone\stubbles\admin\Vhost
     */
    private function parseVhost(\SimpleXMLElement $xmlSource)
    {
        $vhost = new Vhost((string) $xmlSource['id'], (string) $xmlSource['title']);
        if (isset($xmlSource['uriTemplate'])) {
            $vhost->setUriTemplate((string) $xmlSource['uriTemplate']);
        }

        if (isset($xmlSource['liveUri'])) {
            $vhost->setLiveUri((string) $xmlSource['liveUri']);
        }

        return $vhost;
    }
}
