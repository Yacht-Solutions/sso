<?php

if(isset($_COOKIE['jwt']))
{
    $jwt = new JWT();

    $display = [
        'JWT' => $_COOKIE['jwt'],
    ];

    if($jwt->isValid())
    {
        $display['JWT valide'] = 'oui';
        $display['tokenId'] = $jwt->getTokenId();
        $display['userId'] = $jwt->getUserId() ?? 'NULL';
    }
    else
    {
        $display['JWT valide'] = 'non';
    }

    echo implode("\n", array_map_assoc($display, function($k, $v){return "$k : $v";}));
}
else
{
    echo 'Pas de cookie';
}
