<?php


namespace App\Views;

use App\Classes\Vista;

class IngredientesEdit extends Vista{


    function __construct($dt)
    {
        $this->datos = $dt;

    }

    function render()
    {

        $dt = $this->datos;

        $html = "";

        $html .= "<form name=frIngredientes method='post' action='/ingredientes/actualizar'>";
        foreach($dt as $i => $v)
        {
            $html .= sprintf("
            <label for=%s> %s </label>
            <input type=text id='%s' name='%s' value='%s'>",
            $i, $i, $i, $i, $v );

        }

        $html .= "<input type=submit name=Actualizar value=Actualizar>";
    
    return $html;
    }

}