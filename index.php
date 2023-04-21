<?php

// Class autoloader
spl_autoload_register(
    function ($class_name) { 
        $class_name = str_replace('\\', '/',$class_name); 
        include( "./app/$class_name.php"); 
    });

// Cookie safety
session_set_cookie_params( array( 'httponly' => true, 'samesite' => 'Strict') );

// Kickstart app
$app = new LGlass();
$app->init();

# vim: set et ts=4 sw=4:
