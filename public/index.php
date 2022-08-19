<?php

require_once(__DIR__."/../app/Config/site.conf");
require_once __DIR__ . "/../vendor/autoload.php";

use App\AppInicio;

if ( session_status() == PHP_SESSION_NONE )
{
    session_start();
}

$ap = new AppInicio();
$ap->send();

