<?php

namespace App\Http\Controllers;
use \App\Classes\Controlador;

class IngredientesController extends Controlador{

    private  $dbIng;

    public function __construct()
    {
        $this->dbIng = \App\Http\Models\DB::getInstance();
    }


    public function index($pag=1)
    {
        
    $porpag = 50;
    $lista = $this->dbIng->getArray('SELECT * FROM ingredientes '." LIMIT 0, $porpag " );

    $retorno = [
        "datos" => $lista,
        "paginas"  => [ "actual" => $pag,
                "cantPaginas" => $pag ]

    ];

        return $retorno;

    }



    public function pagina($pag=1)
    {
        
    $porpag = 5;
    $nfilas = $this->dbIng->numRows('SELECT id FROM ingredientes');    
    
    $cantpgs = ceil($nfilas/$porpag);
    $inicio =  $porpag * $pag - $porpag ;

    if ( $pag > $cantpgs )
    {
        $inicio = 0;
    }
    
    $lista = $this->dbIng->getArray('SELECT * FROM ingredientes '. "LIMIT $inicio , $porpag");

    $retorno = [
        "datos" => $lista,
        "paginas"  => [ "actual" => $pag,
                "cantPaginas" => $cantpgs ]

    ];

        return $retorno;
    }

    public function insertar()
    {
        array_pop($_POST);
        array_shift($_POST);
        //       $this->dbIng->insert("ingredientes", $_POST);

        return( $this->index() );
    }


}