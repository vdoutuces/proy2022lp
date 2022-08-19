<?php


if (!function_exists('redirect')) {
    function redirect( $url) {
        
        header("Location: $url");
        exit();
    
    }
  } 

