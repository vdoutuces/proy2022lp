<?php


namespace App\http;

class Response
{


    public function send($dt)
    {
        $contenido = $dt;
        require_once __DIR__."/../Views/layout.php";
        
    }
}