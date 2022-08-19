<?php

namespace App\http\Controllers;
use \App\Classes\Controlador;

class  HomeController extends Controlador
{

    public function index()
    {
        
        $dat = [
            "UTU" => "Escuela Técnica las Piedras",
            "Materia" => "PROGRAMACION III",
            "Proyecto" => "Gestión de eventos y Marketing"
            
        ];

        return $dat;
    }

}