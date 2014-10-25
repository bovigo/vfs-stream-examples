<?php
namespace org\bovigo\vfs\examples\part05;

class PropertyBasedDatabaseConfigurations {
    private $configPath;
    private $descriptor;
    private $fallback;
    private $dbProperties;

    /**
     * @param  string  $configPath  path to database config files
     * @param  string  $descriptor  config file name
     * @param  string  $fallback    optional  whether fallback to a default is allowed
     */
    public function  __construct($configPath, $descriptor = 'rdbms', $fallback = true) {
        $this->configPath = $configPath;
        $this->descriptor = $descriptor;
        $this->fallback   = $fallback;
    }

    /**
     * checks whether fallback is enabled and exists
     *
     * @return bool
     */
    private function hasFallback() {
        return ($this->fallback && isset($this->properties()['default']));
    }

    /**
     * checks whether database configuration for given id exists
     *
     * @param   string  $id
     * @return  bool
     */
    public function contain($id) {
        if (isset($this->properties()[$id])) {
            return true;
        }

        return $this->hasFallback();
    }

    /**
     * returns database configuration for given id
     *
     * @param   string  $id
     * @return  array
     * @throws  \Exception
     */
    public function get($id) {
        if (!isset($this->properties()[$id])) {
            if (!$this->hasFallback()) {
                throw new \Exception('No database configuration known for database requested with id ' . $id);
            }

            $id = 'default';
        }

        if (!isset($this->properties()[$id]['dsn'])) {
            throw new \Exception('Missing dsn property in database configuration with id ' . $id);
        }

        return $this->properties()[$id];
    }

    /**
     * reads properties if not done yet
     *
     * @return  array
     * @throws  \Exception
     */
    protected function properties() {
        if (null === $this->dbProperties) {
            $propertiesFile = $this->configPath . '/' . $this->descriptor . '.ini';
            if (!file_exists($propertiesFile) || !is_readable($propertiesFile)) {
                throw new \Exception('File ' . $propertiesFile . ' not present or readable');
            }

            $propertyData = @parse_ini_file($propertiesFile, true);
            if (false === $propertyData) {
                throw new \Exception('Property file at ' . $propertiesFile . ' contains errors and can not be parsed.');
            }

            $this->dbProperties = $propertyData;
        }

        return $this->dbProperties;
    }
}
