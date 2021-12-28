<?php
/**
 * The main file of esupdater.
 *
 * @author  wgrape <https://github.com/WGrape>
 * @license https://github.com/WGrape/esupdater/blob/master/LICENSE MIT Licence
 */

include_once 'bootstrap.php';

$command = strtolower(isset($argv[1]) ? $argv[1] : '');
if (empty($command)) {
    echo "Command empty!\n";
    return;
}

$manager = new \framework\Manager();
switch ($command) {
    case "start":
        $manager->commandStart();
        break;
    case "stop":
        $success = $manager->commandStop();
        echo "{$success}\n";
        break;
    case "work":
        $canalData = isset($argv[2]) ? $argv[2] : "";
        if (empty($canalData)) {
            return;
        }
        $success = $manager->commandWork($canalData);
        echo "{$success}\n";
        break;
    default:
        echo "Not support command: {$command}\n";
        return;
}
