<?php


namespace App\Views;

class User{

    private $cont;

    function __construct($dt = [])
    {
        $this->datos = $dt;
    }

    public function render($dt = [])
    {
        $dt = $this->datos;
        $html = "";
        $html .=sprintf( "<form name=frUsuarios method='post' action='/user/%s'>",
    $this->datos['boton']);
        foreach($dt as $i => $v)
        {
            if ( $i != "boton")
            {
            $html .= sprintf("
            <label for=%s> %s </label>
            <input type=Text id='%s' name='%s' value='%s'>",
            $i, $i, $i, $i, $v );
            }
        }

        $html .= sprintf("<input type=submit name='%s' value='%s'>",
    $this->datos['boton'], $this->datos['boton']);
    
    return $html;
 

    }
    
}