<?php 
$p = "deuda-condominio"; 
session_start();
 if (!empty($_GET['p'])){
   $p = $_GET['p'];
 }
 if(is_file("controlador/".$p.".php")){ 
    require_once("controlador/".$p.".php");
 }
 else{
   require_once("vista/404.php"); 
 }
?> 