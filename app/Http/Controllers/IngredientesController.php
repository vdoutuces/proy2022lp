<?php

namespace App\Http\Controllers;
use \App\Classes\Controlador;
use \App\Http\Response;

class IngredientesController extends Controlador{

    private  $dbIng;
    private  $datos_bd = ["id" => "Id Ingredientes",
    "nombre_producto" => "Nombre Producto",
    "unidades_medida" => "Unidades",
    "descripcion_producto" => "Descripción",
    "marca_producto" => "Marca",
    "fecha_ingreso" => "Fecha",
    "cantidad"  => "Cantidad"];

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

        return (new response("Ingredientes",$retorno));

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

        return (new Response('Ingredientes',$retorno));
    }


    private function _cargarDatos()
    {
        $campos = $this->datos_bd;
    
        foreach($campos as $key => $val )
        {
           $campos["$key"] = isset($_POST["$key"])?$_POST["$key"]:"";
        }
       
    return $campos;
    }

    function edit($id = 0)
    {
    
    $sql = 'SELECT * FROM ingredientes where id = '.$id." LIMIT 1 " ;
    $lista = $this->dbIng->getArray($sql);
    return ( new Response('IngredientesEdit',$lista[0]));    
    }
    
    
    function actualizar()
    {
    $campos = $this->_cargarDatos();
    
    if ( ! empty($campos['id']))
    {
        $this->dbIng->upsert('ingredientes', $campos, ['id' => $campos['id']]);
    }else{
        array_shift($campos);
        $this->dbIng->insert('ingredientes', $campos);
    }
    
    return ( $this->index());
    }
    

    public function agregar()
    {
        $campos = $this->_cargarDatos();
        array_shift($campos);
    return ( new Response('IngredientesEdit',$campos));
       
    }

    function borrar($id = 0)
    {
        $lista = $this->dbIng->delete('ingredientes',['id' => $id]);   
    return ( $this->index());
    }
    
    

}