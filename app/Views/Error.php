<?php


namespace App\Views;
use App\Classes\Vista;

class Error extends Vista
{

        public function render($datos)
        {

            
            $html = sprintf("
                <h1> 404 </h1>
                <h3> %s </h3>",  implode(" ",$datos));

            return $html;

        }



}