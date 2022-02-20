<?php

require_once __DIR__ . "/../loader.php";

$pluginDirectory = PLUGIN_PATH . "autogeneratecallback/";
$namespace       = isset($argv[1]) ? $argv[1] : '';
$moduleName      = isset($argv[2]) ? $argv[2] : '';
if (strpos($namespace, '\\') === false) {
    die("Error namespace\n");
}
if (strpos($moduleName, '\\') != false) {
    die("Error moduleName\n");
}
$namespaceDirectory = ROOT_PATH . str_replace("\\", '/', $namespace);

$namespacePattern  = '{{namespace}}';
$moduleNamePattern = '{{moduleName}}';
$configList        = [
    [
        'template_file' => $pluginDirectory . 'handler.template',
        'php_file'      => "{$namespaceDirectory}/{$moduleName}Handler.php",
    ],
    [
        'template_file' => $pluginDirectory . 'service.template',
        'php_file'      => "{$namespaceDirectory}/{$moduleName}Service.php",
    ],
];
foreach ($configList as $item) {
    if (file_exists($item['php_file'])) {
        echo "The file already exist: {$item['php_file']}\n";
        continue;
    }
    if (!is_dir($namespaceDirectory)) {
        mkdir($namespaceDirectory);
    }

    $handle = fopen($item['template_file'], 'r');
    while (!feof($handle)) {
        $content = fgets($handle);
        if (preg_match($namespacePattern, $content) > 0) {
            $content = str_replace($namespacePattern, $namespace, $content);
        }
        if (preg_match($moduleNamePattern, $content) > 0) {
            $content = str_replace($moduleNamePattern, $moduleName, $content);
        }
        file_put_contents($item['php_file'], $content, FILE_APPEND);
    }
}
