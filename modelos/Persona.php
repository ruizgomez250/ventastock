<?php 
//incluir la conexion de base de datos
require "../config/Conexion.php";
class Persona{


	//implementamos nuestro constructor
public function __construct(){

}

//metodo insertar regiustro
public function insertar($tipo_persona,$nombre,$tipo_documento,$num_documento,$direccion,$telefono,$email){
	$sql="INSERT INTO persona (tipo_persona,nombre,tipo_documento,num_documento,direccion,telefono,email) VALUES ('$tipo_persona','$nombre','$tipo_documento','$num_documento','$direccion','$telefono','$email')";
	return ejecutarConsulta($sql);
}



public function editar($idpersona,$tipo_persona,$nombre,$tipo_documento,$num_documento,$direccion,$telefono,$email){
	$sql="UPDATE persona SET tipo_persona='$tipo_persona', nombre='$nombre',tipo_documento='$tipo_documento',num_documento='$num_documento',direccion='$direccion',telefono='$telefono',email='$email' 
	WHERE idpersona='$idpersona'";
	return ejecutarConsulta($sql);
}
//funcion para eliminar datos
public function eliminar($idpersona){
	$sql="DELETE FROM persona WHERE idpersona='$idpersona'";
	return ejecutarConsulta($sql);
}

//metodo para mostrar registros
public function mostrar($idpersona){
	$sql="SELECT * FROM persona WHERE idpersona='$idpersona'";
	return ejecutarConsultaSimpleFila($sql);
}

//listar registros
public function listarp(){
	$sql="SELECT * FROM persona WHERE tipo_persona='Proveedor'";
	return ejecutarConsulta($sql);
}
public function listarc(){
	$sql="SELECT * FROM persona WHERE tipo_persona='Cliente'";
	return ejecutarConsulta($sql);
}
}

 ?>
