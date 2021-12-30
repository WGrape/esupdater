<?php
/**
 * The main file of esupdater unit test.
 *
 * @author  wgrape <https://github.com/WGrape>
 * @license https://github.com/WGrape/esupdater/blob/master/LICENSE MIT Licence
 */

include_once __DIR__ . '/../bootstrap.php';
include_once __DIR__ . '/../config/test.php';

class ProjectTest
{
    /**
     * The root directory.
     *
     * @var string
     */
    protected $rootDirectory = "";

    /**
     * The directories need to test.
     *
     * @var array
     */
    protected $testDirectories = [];

    /**
     * ProjectTest constructor.
     */
    function __construct()
    {
        $this->rootDirectory = ROOT_PATH;
        global $test;
        if (isset($test['testcases_directory']) && !empty($test['testcases_directory'])) {
            $this->findTestDirectories($test['testcases_directory']);
        }
    }

    /**
     * Find all directories need to test.
     *
     * @param $directory
     */
    protected function findTestDirectories($directory)
    {
        $currentDirectory = "{$this->rootDirectory}{$directory}";
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

    /**
     * The main function of unit test.
     *
     * @return array
     */
    public function run(): array
    {
        $testResultMap = [];
        foreach ($this->testDirectories as $directory) {
            $path = "{$this->rootDirectory}/{$directory}";
            if (!is_dir($path)) {
                continue;
            }
            $handler = opendir($path);
            while (false !== ($filename = readdir($handler))) {
                if (!preg_match('/\.php$/', $filename)) {
                    continue;
                }
                $class      = $directory . explode('.', $filename)[0];
                $class      = str_replace("/", "\\", $class);
                $testObject = new $class();
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

                    if (!isset($testResultMap[$class])) {
                        $testResultMap[$class] = [];
                    }
                    if (!isset($testResultMap[$class][$method])) {
                        $testResultMap[$class][$method] = false;
                    }

                    if (!$testObject->$method()) {
                        echo "Unfortunately! You failed the test\n";
                        exit(1);
                    }

                    $testResultMap[$class][$method] = true;
                }
            }
        }
        echo "Congratulations! All testcases passed!\n";
        return $testResultMap;
    }

    /**
     * Output the test report of html format.
     *
     * @param array $testResultMap
     */
    public function outputHTML(array $testResultMap)
    {
        $file = ROOT_PATH . "test/report/index.html";

        $caseListHtml = '';
        $id           = 0;
        foreach ($testResultMap as $testClass => $item) {
            foreach ($item as $testMethod => $testResult) {
                ++$id;
                $testResult   = $testResult ? 'success' : 'failed';
                $caseListHtml .= "<tr><td>{$id}</td><td>{$testClass}</td><td>{$testMethod}</td><td>{$testResult}</td></tr>";
            }
        }
        $html = "<html><head><link rel='stylesheet' href='index.css'></head><body><div id='Container'><div class='section'><h2>ESUpdater UnitTest</h2><table id='table-2'><tr><th>Id</th><th>TestClass</th><th>TestMethod</th><th>TestResult</th></tr>" . $caseListHtml . "</table></div></div></body></html>";

        file_put_contents($file, $html);
    }
}

$projectTest   = new ProjectTest();
$testResultMap = $projectTest->run();
$projectTest->outputHTML($testResultMap);
