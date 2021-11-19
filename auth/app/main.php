<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/helpers.php';

header('Content-Type: application/json; charset=utf-8');

if(isset($_GET['action']))
{
    $file = __DIR__ . '/actions/' . $_GET['action'] . '.php';
    if(file_exists($file))
    {
        require_once($file);
    }
    else
    {
        err404();
    }
}
else
{
    err404();
}
