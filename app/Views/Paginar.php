<?php

namespace App\Views;
use App\Classes\Vista;

class Paginar extends Vista
{
    private $ptotal;
    private $porpag;
    private $actual;
    private $maxbotones = 10;
    protected $pagina; 

    public function __construct()    
    {
    }

    public function setPagina($pg)
    {
        $this->pagina = substr( $pg, strrpos($pg,'\\') + 1);
    }

    public function render($datospg)
    {
        $datos = $datospg['datos'];
        $paginas = $datospg['paginas'];
        $tbl = "";
        $campos = array_keys($datos[0]);

        $tbl = sprintf("<form name='form_ingredientes' action = '/%s/insertar' METHOD=POST>
                        <table><tr>", $this->pagina);
    foreach($campos as $inp )
    {

    $tbl .= sprintf("<td><label>%s</label><br><input id=%s type=text name=%s size=10></td>",
        $inp, $inp, $inp );

    }
    $tbl .= sprintf("</tr></table>
                    <input type=submit name=enviar /> 
                    </form>");
    

        if ( $paginas['cantPaginas'] > 1)
        {
            $tbl .= $this->botones($paginas);
        }

        $tbl .= '<div><a href="/"><img src="/images/flecha-cuadrado-izquierda.png" width=25/></a> </div>';

        $tbl .= '<table border = 1><tr>';

        if ( !empty( $campos) )
        {
            $campos = array_keys($datos[0]);

            foreach( $campos as $th)
            {
                $tbl .= "<th>" . $th . "</th>";
            }   

        $tbl .= '</tr><tr>';
        }

        foreach( $datos as $i => $v )
        {
            $valores = array_values($v);
            $tbl .= '<tr>';

            foreach( $valores as $td)
            {
                $tbl .= "<td>" . $td . "</td>";
            }

            $tbl .= '</tr>';
        }        

        $tbl .= "</table>";

        return( $tbl);
    }


    private function botones($paginas )
    {
    $cant = $paginas['cantPaginas'];

    $bot = sprintf('<div class="paginar"> <ul>');
 
    if ( $paginas['actual'] > 1)
    {
         $bot .= sprintf('<li><a href=/%s/pagina/%s><<</a></li>', 
                        $this->pagina , $paginas['actual'] - 1);
    }else{
        $bot .= sprintf('<li><<</li>');
    }

for( $i=0; $i < $cant; $i++)
    {
        if($i+1 != $paginas['actual'])
        {
             $bot .= sprintf('<li><a href=/%s/pagina/%s>%s</a></li>', 
                            $this->pagina , $i+1, $i+1 );
        }else{
            $bot .= sprintf('<li>[%s]</li>', 
             $i+1 );

        }
    }

    if ( $paginas['actual'] < $cant)
    {
         $bot .= sprintf('<li><a href=/%s/pagina/%s>>></a></li>', 
                        $this->pagina , $paginas['actual']+1);
    }else{
        $bot .= sprintf('<li>>></li>');
    }

    $bot .= sprintf('</ul></div>');


        return $bot;
    }



}