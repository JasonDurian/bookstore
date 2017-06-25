<?php

function classLoader($class)
{
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $file = __DIR__ . '/lib/' . $path . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
}
spl_autoload_register('classLoader');

require_once  __DIR__ . '/lib/WxPay.Api.php';
require_once  __DIR__ . '/lib/WxPay.Notify.php';
