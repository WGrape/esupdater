<?php

include_once __DIR__ . '/../bootstrap.php';
include_once __DIR__ . '/../config/test.php';

class ProjectTest
{
    protected $baseDirectory = "";
    protected $testDirectories = [];

    function __construct()
    {
        $this->baseDirectory = ROOT_PATH;
        global $test;
        if (isset($test['testcases_directory']) && !empty($test['testcases_directory'])) {
            $this->findTestDirectories($test['testcases_directory']);
        }
    }

    protected function findTestDirectories($directory)
    {
        $currentDirectory = "{$this->baseDirectory}{$directory}";
        $handler          = opendir($currentDirectory);
        if ($handler === false) {
            return;
        }

        while (false !== ($subDir = readdir($handler))) {
            $path = $currentDirectory . $subDir;
            if (is_dir($path) && !in_array($subDir, ['.', '..'])) {
                $targetDirectory         = $directory . $subDir . '/';
                $this->testDirectories[] = $targetDirectory;
                $this->findTestDirectories($targetDirectory);
            }
        }
    }

    public function run()
    {
        global $test;
        foreach ($this->testDirectories as $directory) {
            $path = "{$this->baseDirectory}/{$directory}";
            if (!is_dir($path)) {
                continue;
            }
            $handler = opendir($path);
            while (false !== ($filename = readdir($handler))) {
                if (!preg_match('/\.php$/', $filename)) {
                    continue;
                }
                $class = $directory . explode('.', $filename)[0];
                $class = str_replace("/", "\\", $class);
                $test  = new $class();
                try {
                    $reflectClass = new \ReflectionClass($class);
                } catch (ReflectionException $e) {
                    exit(1);
                }
                $methodObjects = $reflectClass->getMethods();
                foreach ($methodObjects as $methodObject) {
                    $method     = $methodObject->getName();
                    $ownerClass = $methodObject->getDeclaringClass()->getName();
                    if ('TestBase' == $ownerClass || strpos($method, 'test') !== 0) {
                        continue;
                    }
                    if (!$test->$method()) {
                        echo "Unfortunately! You failed the test\n";
                        exit(1);
                    }
                }
            }
        }
        echo "Congratulations! All testcases passed!\n";
    }
}

$projectTest = new ProjectTest();
$projectTest->run();