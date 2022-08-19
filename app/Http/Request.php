<?php

namespace App\Http;

class Request{

    private $controlador;
    private $metodo;
    private $vista;
    private $param;

     public function __construct()
    {

            $url = trim($_SERVER['REQUEST_URI'], "/");
                
            $url = filter_var($url, FILTER_SANITIZE_STRING);
            $url = explode('/', $url); 
            
            $this->setController(isset($url[0])?$url[0]:"");
            $this->setMethod(isset($url[1])?$url[1]:"");

            if ( count($url) > 2 )
            {
                $this->param = array_splice($url, 2);
            }else{
                $this->param = [];
            }

    }


    public function setController( $controlador='' )
    {
        $controlador = ucfirst($controlador);
        $tmpcont = "App\Http\Controllers\\{$controlador}Controller";

        if ($controlador == '')
        {
            $this->controlador = "App\Http\Controllers\HomeController";
            $this->vista = 'App\Views\Home';

        }elseif ( ! class_exists( $tmpcont )  ){
        
            $this->controlador = "App\Http\Controllers\ErrorController";
            $this->vista = 'App\Views\Error';

         }else       {
            $this->controlador = "App\Http\Controllers\\{$controlador}Controller";
            $this->vista = "App\Views\\{$controlador}";
        }
    }

    public function setMethod( $metodo = '' )
    {
        if ( $metodo == '' || ! method_exists($this->controlador, $metodo ))
        {
            $this->metodo = "index";            
        }else
        {
            $this->metodo = $metodo;

        }

    }

    public function getControlador(){
    return ( $this->controlador);
    }

    public function getMetodo(){
        return ( $this->metodo);
        }

    public function getVista(){
        return ( $this->vista);
    }
    public function getParametros(){
        return ( $this->param);
    }
    

}
