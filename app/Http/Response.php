<?php


namespace App\http;

class Response
{
    private $vista;
    
    function __construct($vta, $datos)
    {
        $v = sprintf("\App\Views\%s",$vta);
        $this->vista = new $v($datos);
    }

    public function send()
    {
        $contenido = $this->vista->render();
        require_once __DIR__."/../Views/layout.php";    
    }
}