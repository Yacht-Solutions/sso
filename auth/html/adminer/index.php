<?php

function adminer_object()
{
    include_once "./plugins/plugin.php";
    
    foreach (glob("plugins/*.php") as $filename) {
        include_once "./$filename";
    }
    
    $plugins = array(
        new FCSqliteConnectionWithoutCredentials(),
    );
    
    return new AdminerPlugin($plugins);
}

// include original Adminer or Adminer Editor
include "./adminer.php";