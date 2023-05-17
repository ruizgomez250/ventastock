<?php 
//incluir la conexion de base de datos
require "../config/Conexion.php";
class PagoCuota{


	//implementamos nuestro constructor
public function __construct(){

}
public function listarDeuda($id){
	$sql="SELECT id,fecha_emision,fecha_vencimiento,monto,pagofecha FROM pagare WHERE id=$id order by id asc;";
	return ejecutarConsulta($sql);
	//return ($sql);
}
//metodo insertar registro


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


public function ventacabecera($idventa,$idpago){
	$sql= "SELECT v.idventa, v.idcliente, p.nombre AS cliente, p.direccion, p.tipo_documento, p.num_documento, p.email, p.telefono, v.idusuario, u.nombre AS usuario, v.tipo_comprobante, v.serie_comprobante, v.num_comprobante, DATE(pag.pagofecha) AS fecha, v.impuesto, v.total_venta FROM venta v INNER JOIN persona p ON v.idcliente=p.idpersona INNER JOIN usuario u ON v.idusuario=u.idusuario INNER JOIN pagare pag on pag.id_ingreso=v.idventa WHERE v.idventa='$idventa' AND pag.id= $idpago";
	return ejecutarConsulta($sql);
}

public function ventadetalles($idventa){
	$sql="SELECT monto,count(id) cuot FROM pagare WHERE id_ingreso='$idventa' GROUP BY monto";
         return ejecutarConsulta($sql);
}
public function ventadetalles1($idventa,$idpago){
	$sql="SELECT count(id) pag FROM pagare WHERE id_ingreso='$idventa' AND not pagofecha IS NULL AND id <= $idpago";
         return ejecutarConsulta($sql);
}


}

 ?>
