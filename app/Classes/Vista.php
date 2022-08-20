<?php


namespace App\Classes;


class Vista
{

    protected $datos;

function construct($datos=[])
{
    $this->datos = $datos;

}

function render()
{
    echo " render ". __CLASS__;

}

}