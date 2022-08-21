<?php

namespace App\Http\Controllers;
use App\Classes\Controlador;
use App\Http\Models\DB;
use App\Http\Response;

class UserController extends Controlador
{
    private $dbUsr;

    function __construct()
    {
        $this->bdUsr = DB::getInstance();
    }

   private function __cargarCampos($boton = "Login")
   {
    $datform=[];
    $estalogueado = UserController::usrlogin();    
        if ( $estalogueado == true && $boton == "nuevo")
        {
            if( isset($_POST['nuevo']) ){      
                $datform = ["usuario"  => $_POST['usuario'],
                "clave" => $_POST['clave'],
                "fecha" => date('Y-m-d'),
                "descripcion" => $_POST['descripcion']?$_POST['descripcion']:""
             ];
            }else{
                $datform = [
                    "usuario"  => "",
                    "clave" => "",
                    "descripcion" => "",
                    "boton" => $boton
                ];       
            }

        }elseif     (isset($_POST['usuario']) && 
                    isset($_POST['clave']) &&
                    isset($_POST['Login']) ){

            $datform = [
                "usuario"  => $_POST['usuario'],
                "clave" => $_POST['clave'],
            ];          
        }else{
        $datform = [
                    "usuario"  => "",
                    "clave" => "",
                    "boton" => $boton 
                ];       
        }

        return $datform;
    }
    
    public static function usrlogin()
    {
        return (isset($_SESSION["idUser"] ))?$_SESSION["idUser"]:false;
    }

    public function index()
    {
        $pag = "";

        if (  $this->buscar()  )
        {
            $pag = "/";
        }elseif ( isset($_POST["usuario"])){
            $pag = "/error";
        } else {
            $datos = $this->__cargarCampos("Login");            
            return (new Response('User',$datos));
        }
        redirect($pag);
    }

    public function buscar( )
    {
        $idusr = false;
        if ( isset($_POST["usuario"] ) &&  $_POST["clave"])
        {
            $usuario = $_POST["usuario"];
            $clave = $_POST["clave"];
       
            $tmpdb = $this->bdUsr->search('usuarios', ['usuario' => $usuario, 'clave' => $clave ]);
       
            if ( ! empty($tmpdb[0] ) && isset($tmpdb[0]['idUser']) )
            { 
                $_SESSION["idUser"] = $tmpdb[0]['idUser'];
                $_SESSION["usuario"] = $tmpdb[0]['usuario'];
                $idusr = true;
            }

        }
        return $idusr;
    }


    public function nuevo()
    {
        $datos = $this->__cargarCampos("nuevo");
     
        if (isset($datos['boton'])){
            return ( new Response("User",$datos ) );
        }else{
            DB::getInstance()->insert("usuarios",$datos);
        }      
        redirect('/Home');        
    }

    public function logout()
    {
        if ( isset($_SESSION ))
        {
            session_destroy();
        }
        redirect('/');
    }

    public function Login()
    {
        return( $this->index());
    }

}