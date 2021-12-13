<?php

function autoload($classname)
{
    $classname = str_replace('\\', '/', $classname);

    $file = ROOT_PATH . "{$classname}.php";
    if (file_exists($file)) {
        include_once $file;
    } else {
        echo 'class file' . $classname . 'not found!';
    }
}

spl_autoload_register("autoload", true, true);
