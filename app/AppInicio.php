<?php

namespace App;
use App\Http\Request;
use App\Http\Controllers\UserController;
use App\Http\Response;

class AppInicio{

    private $idUsuario;
    private $controlador;
    private $metodo;
    private $parametros;
    private $request;
    private $vista;

    function __construct()
    {
        $this->idUsuario = UserController::usrlogin();
        $this->iniciarApp();
    }

    function iniciarApp()
    {
        if ( $this->idUsuario == false)
        {
            $this->controlador = "\App\Http\Controllers\UserController";
            $this->metodo = "index";
            $this->vista = "\App\Views\User";
            $this->parametros = [];           
        }else{

            $this->request= new Request;
            $this->controlador = ($this->request)->getControlador();
            $this->metodo = ($this->request)->getMetodo();
            $this->vista = ($this->request)->getVista();
            $this->parametros = ($this->request)->getParametros();
            
        }


    }

    public function send()
    {

        $datos = call_user_func_array(   [ new $this->controlador , 
                                            $this->metodo ],
                                            $this->parametros
                                        );    
        $vi = new $this->vista;
        $htmlvista = $vi->render($datos);
 
        $enviar = new Response;
        $enviar->send($htmlvista);

    }
        
}