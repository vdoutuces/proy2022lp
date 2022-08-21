<?php


namespace App\Views;
use App\Classes\Vista;


class Home extends Vista{
    
    private $cont="";

    function __construct($dt)
     {
        $this->datos = $dt;
     }

    public function render()
    {
        $datos = $this->datos;

        if ( isset( $_SESSION["idUser"]))
        {  
            $this->cont .= "<h4>Usuario: " . $_SESSION["usuario"] . "</h4>" ;
            $this->cont .=  "<h3><a href='/user/logout'>Logout</a></h3>";
        }else{
            $this->cont .=  "<h3><a href='/user'>Login</a></h3>";
        }

        foreach( $datos as $id => $vl )
        {
            $this->cont .= "<div><h3><strong>$id:</strong> $vl</h3></div>";
        }

        return $this->cont;
    }



}