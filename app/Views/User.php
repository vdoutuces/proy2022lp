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
        
        if (isset( $dt["idUser"]) && $dt["idUser"] > 0  )
        {
            $this->cont .= "<div><p> Usuario: {$dt['usuario']} - <a href='/user/logout'> salir </a></p></di>";
        }else
        {
            $this->cont .=
            '
            <div class="login">
            <form action="/user" method = "POST">

            <label for="usuario">Usuario</label>
            <input type="text" id="usuario" name="usuario"  size=9/>
  
            <label for="clave">Clave</label>
            <input type="password" id="clave" name="clave" size=9 />
  
            <input type="submit" name"enviar" value="login"/>
             </form>
            </div>
            ';
        }
        return $this->cont;
    }
    
}