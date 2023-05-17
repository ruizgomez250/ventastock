<?php 
require_once "global.php";
function conexion_db(){
	$conexion=new mysqli('localhost','root','','junta',3306);
	//return $conexion;//pg_set_client_encoding($conexion,"UNICODE");
	if ($conexion->connect_error) {
    	die("ERROR: No se puede conectar al servidor: " . $conexion->connect_error);
  	}
  	return $conexion;

	}
