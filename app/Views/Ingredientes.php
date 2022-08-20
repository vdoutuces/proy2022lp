<?php

namespace App\Views;
use App\Classes\Vista;

class Ingredientes extends  Paginar
{

    public function __construct($dt = []){

        $this->datos = $dt;
        $this->setPagina( __CLASS__ ) ;
    }


}