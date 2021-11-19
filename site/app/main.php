<?php

session_start();
define('APP', getenv('APP'));

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/api.php';

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
    if(isset($_SESSION['jwt']))
    {
        $result = $api->verifyToken($_SESSION['jwt']);
        if($result->success)
        {
            $_SESSION['user'] = $result->user;
        }
        else
        {
            $api->getNewToken();
        }
    }
    else
    {
        if(isset($_GET['jwt']))
        {
            $result = $api->verifyToken($_GET['jwt']);
            if($result->success)
            {
                $_SESSION['user'] = $result->user;
                $_SESSION['jwt'] = $_GET['jwt'];

                (new Url())->deleteQuery('jwt')->redirect();
            }
            else
            {
                $api->getNewToken();
            }
        }
        else
        {
            $api->getNewToken();
        }
    }

    $less = new lessc();
    $less->compileFile(__DIR__ . '/style.less', __DIR__ . '/../html/style.css');

    $page = $_SESSION['user'] ? 'logout' : 'login';
    require_once __DIR__ . '/views/template.php';
}
