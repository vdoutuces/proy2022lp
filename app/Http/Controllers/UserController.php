<?php

namespace App\Http\Controllers;
use App\Classes\Controlador;
use App\Http\Models\DB;
use App\Http\Response;

class UserController extends Controlador
{
    private $dbUsr;
    private $usuarios=[];
    private $datos = ["idUser" => -1, "usuario" => ""];

    function __construct()
    {
        if ( ! $this->usrlogin() ){
            $this->bdUsr = DB::getInstance();
        }
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
            return (new Response('User',$this->datos));
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

    public function logout()
    {
        if ( isset($_SESSION ))
        {
            session_destroy();
        }
 
        redirect('/');

    }

}