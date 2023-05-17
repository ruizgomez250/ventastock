<?php 
//incluir la conexion de base de datos
require "../config/Conexion.php";
class Venta{


	//implementamos nuestro constructor
public function __construct(){

}
public function listarDeuda($id){
	$sql="SELECT id,fecha_emision,fecha_vencimiento,monto,pagofecha FROM pagare WHERE id_ingreso=$id order by id asc;";
	return ejecutarConsulta($sql);
	//return ($sql);
}
//metodo insertar registro
public function insertar($idcliente,$idusuario,$tipo_comprobante,$serie_comprobante,$num_comprobante,$fecha_hora,$impuesto,$total_venta,$idarticulo,$cantidad,$precio_venta,$descuento,$fechP,$cantidad_pagos){
	$sql="INSERT INTO venta (idcliente,idusuario,tipo_comprobante,serie_comprobante,num_comprobante,fecha_hora,impuesto,total_venta,estado) VALUES ('$idcliente','$idusuario','$tipo_comprobante','$serie_comprobante','$num_comprobante','$fecha_hora','$impuesto','$total_venta','Aceptado')";
	//return ejecutarConsulta($sql);
	 $idventanew=ejecutarConsulta_retornarID($sql);
	 $num_elementos=0;
	 $sw=true;
	 $total=0;
	 while ($num_elementos < count($idarticulo)) {
	 	$sql_detalle="INSERT INTO detalle_venta (idventa,idarticulo,cantidad,precio_venta,descuento) VALUES('$idventanew','$idarticulo[$num_elementos]','$cantidad[$num_elementos]','$precio_venta[$num_elementos]','$descuento[$num_elementos]')";
	 	ejecutarConsulta($sql_detalle) or $sw=false;
	 	$tot=($cantidad[$num_elementos]*$precio_venta[$num_elementos])-$descuento[$num_elementos];
	 	$num_elementos=$num_elementos+1;

	 	$total=$tot+$total;
	 }
	  $sqlPagare;
	 if($tipo_comprobante=='Credito'){
	 	$num_elementos=0;
	 	$cuota=$total/$cantidad_pagos;
	 	while ($num_elementos < count($fechP)) {	 		
	 		$sqlPagare="INSERT INTO pagare  (monto,fecha_emision,fecha_vencimiento,id_ingreso) VALUES ('$cuota','$fecha_hora','$fechP[$num_elementos]','$idventanew');";
	 		ejecutarConsulta($sqlPagare);
	 		//$metodo_pago='entro';
	 		$num_elementos++;
	 	}
	}
	echo($tipo_comprobante);
	 return $sw;
}

public function anular($idventa){
	$sql="UPDATE venta SET estado='Anulado' WHERE idventa='$idventa'";
	return ejecutarConsulta($sql);
}

public function guardPag($idingreso,$fechaPag,$iddeuda){
	/*$sql="UPDATE ingreso SET pagos_realizados=pagos_realizados+1 WHERE idingreso='$idingreso'";
	//return $sql;
	ejecutarConsulta($sql);*/
	$sql="SELECT monto FROM pagare WHERE id=$iddeuda;";
	$cuenta=ejecutarConsultaSimpleFila($sql);
	$monto=$cuenta['monto'];
	$sql="UPDATE pagare SET pagofecha='$fechaPag' WHERE id='$iddeuda'";
	return ejecutarConsulta($sql);
	//return $sql;
}
//implementar un metodopara mostrar los datos de unregistro a modificar
public function mostrar($idventa){
	$sql="SELECT v.idventa,DATE(v.fecha_hora) as fecha,v.idcliente,p.nombre as cliente,u.idusuario,u.nombre as usuario, v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,v.total_venta,v.impuesto,v.estado FROM venta v INNER JOIN persona p ON v.idcliente=p.idpersona INNER JOIN usuario u ON v.idusuario=u.idusuario WHERE idventa='$idventa'";
	return ejecutarConsultaSimpleFila($sql);
}

public function listarDetalle($idventa){
	$sql="SELECT dv.idventa,dv.idarticulo,a.nombre,dv.cantidad,dv.precio_venta,dv.descuento,(dv.cantidad*dv.precio_venta-dv.descuento) as subtotal FROM detalle_venta dv INNER JOIN articulo a ON dv.idarticulo=a.idarticulo WHERE dv.idventa='$idventa'";
	return ejecutarConsulta($sql);
}

//listar registros
public function listar(){
	$sql="SELECT v.idventa,DATE(v.fecha_hora) as fecha,v.idcliente,p.nombre as cliente,u.idusuario,u.nombre as usuario, v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,v.total_venta,v.impuesto,v.estado FROM venta v INNER JOIN persona p ON v.idcliente=p.idpersona INNER JOIN usuario u ON v.idusuario=u.idusuario ORDER BY v.idventa DESC";
	return ejecutarConsulta($sql);
}


public function ventacabecera($idventa){
	$sql= "SELECT v.idventa, v.idcliente, p.nombre AS cliente, p.direccion, p.tipo_documento, p.num_documento, p.email, p.telefono, v.idusuario, u.nombre AS usuario, v.tipo_comprobante, v.serie_comprobante, v.num_comprobante, DATE(v.fecha_hora) AS fecha, v.impuesto, v.total_venta FROM venta v INNER JOIN persona p ON v.idcliente=p.idpersona INNER JOIN usuario u ON v.idusuario=u.idusuario WHERE v.idventa='$idventa'";
	return ejecutarConsulta($sql);
}

public function ventadetalles($idventa){
	$sql="SELECT a.nombre AS articulo, a.codigo, d.cantidad, d.precio_venta, d.descuento, (d.cantidad*d.precio_venta-d.descuento) AS subtotal FROM detalle_venta d INNER JOIN articulo a ON d.idarticulo=a.idarticulo WHERE d.idventa='$idventa'";
         return ejecutarConsulta($sql);
}


}

 ?>
