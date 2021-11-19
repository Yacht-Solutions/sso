<?php

$result = $api->login($_POST['login'], $_POST['password']);

if($result->success)
{
    $_SESSION['user'] = $result->user;
    $_SESSION['jwt'] = $result->jwt;

    echo json_encode([
        'success' => TRUE,
        'jwt' => $result->jwt,
    ]);
}
else
{
    echo json_encode([
        'success' => FALSE,
    ]);
}
