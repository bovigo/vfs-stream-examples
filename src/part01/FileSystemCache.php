<?php
namespace org\bovigo\vfs\examples\part01;

class FileSystemCache {

    private $dir;

    public function __construct($dir) {
        $this->dir = $dir;
    }

    public function store($key, $data) {
        if (!file_exists($this->dir)) {
            mkdir($this->dir, 0700, true);
        }

        file_put_contents($this->dir . '/' . $key, serialize($data));
    }
}
