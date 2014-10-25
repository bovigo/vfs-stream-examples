<?php
namespace org\bovigo\vfs\examples\part03;

class FileSystemCache {

    private $dir;

    private $permissions;

    public function __construct($dir, $permissions = 0700) {
        $this->dir = $dir;
        $this->permissions = $permissions;
    }

    public function store($key, $data) {
        if (!file_exists($this->dir)) {
            mkdir($this->dir, $this->permissions, true);
        }

        $storedData = serialize($data);
        $result = @file_put_contents($this->dir . '/' . $key, serialize($data));
        if (false === $result) {
            throw new \Exception('Failure while storing ' . $key . ': ' . error_get_last()['message']);
        } elseif (strlen($storedData) > $result) {
            throw new \Exception('Failure while storing ' . $key . ', disc full?');
        }
    }
}
