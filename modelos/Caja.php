<?php 
//incluir la conexion de base de datos
require "../config/Conexion.php";
class Caja{


	//implementamos nuestro constructor
public function __construct(){

}

//metodo insertar regiustro

public function insertar($codCliente,$idfactura,$diferencia,$monto,$fecha,$idusuario){
	if($idfactura == 0){
		/* Cuando el cobro se hace desde la tabla costos cliente*/
		$sql="SELECT  max(c.id) id  FROM costos_cliente c INNER JOIN costos cost on cost.id=c.id_costo WHERE c.pagos_realizados < c.cantidad_pago AND c.id_cliente=".$codCliente." AND not cost.descripcion LIKE '%Saldo pago Factura #%';";
		$rsp= ejecutarConsultaSimpleFila($sql);
		$idCosto=$rsp['id'];
		$sql = "SELECT cc.id,c.descripcion descripcion,c.monto monto,c.id_cuenta idcuenta FROM costos_cliente cc INNER JOIN costos c on c.id=cc.id_costo WHERE cc.id=$idCosto ORDER BY id desc;";
		$rsp= ejecutarConsultaSimpleFila($sql);
		$descripcion=$rsp['descripcion'];
		//$monto=$rsp['monto'];
		$idCuenta=$rsp['idcuenta'];
		$sql="INSERT INTO venta_agua (descripcion,estado,fechaemision,fechavencimiento,fecha_cierre,fecha_inicio,idcliente,idlectura,idusuario,num_extracto,num_factura,tipo_comprobante,total_venta) values ('$descripcion','2','$fecha','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','$codCliente','0','$idusuario','0','0','varios','$monto');";
		$id_venta=ejecutarConsulta_retornarID($sql);
		$sql="INSERT INTO venta_detalle (cantidad,concepto,idcuentacontable,idventa,precio_venta) values ('1','$descripcion','$idCuenta','$id_venta','$monto');";
		ejecutarConsulta($sql);
		if($diferencia < 1){
			$sql="UPDATE costos_cliente SET estado=0,cantidad_pago=1,pagos_realizados=1 WHERE id=$idCosto;";
			ejecutarConsulta($sql);
		}else{
			$sql="SELECT id_costo FROM costos_cliente WHERE id=$idCosto;";
			$res=ejecutarConsultaSimpleFila($sql);
			$cost=($res['id_costo']);
			$sql="UPDATE costos SET monto=$diferencia WHERE id=$cost;";
			ejecutarConsulta($sql);
		}
		
		$sql="SELECT coalesce((nro),0) fac FROM numerofactura WHERE id='1';";
			$res=ejecutarConsultaSimpleFila($sql);
			$nuevaFac=($res['fac']*1)+1;
			$sql="UPDATE numerofactura SET nro=nro+1 WHERE id='1';";
			$res=ejecutarConsulta($sql);
		$sql="INSERT INTO pago_factura (estado,fecha,idusuario,idventaagua,monto,numfactura) VALUES ('1','$fecha','$idusuario','$id_venta','$monto','$nuevaFac');";
		$idPago=ejecutarConsulta_retornarID($sql);
		$this->crearAsiento($idPago);
		return $idPago;
	}else{
			$sql="SELECT  count(monto) cant FROM  pago_factura  WHERE  idventaagua='$idfactura' AND estado=1;";
			$res= ejecutarConsultaSimpleFila($sql);
			if($res['cant'] > 0){
				
				$sql="SELECT max(cc.id) id FROM  costos_cliente cc INNER JOIN costos cost on cost.id=cc.id_costo WHERE cost.descripcion like 'Saldo pago Factura #%';";
				$res= ejecutarConsultaSimpleFila($sql);
				$idCost=$res['id'];
				$sql="UPDATE costos_cliente SET estado=0,cantidad_pago=0 WHERE id=$idCost;";
				ejecutarConsulta($sql);
			}
			

			


			$sql="SELECT coalesce((nro),0) fac FROM numerofactura WHERE id='1';";
			$res=ejecutarConsultaSimpleFila($sql);
			$nuevaFac=($res['fac']*1)+1;
			$sql="UPDATE numerofactura SET nro=nro+1 WHERE id='1';";
			$res=ejecutarConsulta($sql);
			$sql="UPDATE venta_agua SET estado=2 WHERE idcliente='$codCliente';";
				$res=ejecutarConsulta($sql);
			$sql="INSERT INTO pago_factura (estado,fecha,idusuario,idventaagua,monto,numfactura) VALUES ('1','$fecha','$idusuario','$idfactura','$monto','$nuevaFac');";
			$idPago=ejecutarConsulta_retornarID($sql);
			if($diferencia > 0){
			  $descr='Saldo pago Factura #'.$idPago;
		      $sql="INSERT INTO costos (descripcion,estado,id_cuenta,monto) VALUES ('$descr','1','5','$diferencia');";
		      $idCostos=ejecutarConsulta_retornarID($sql);
		      $sql="INSERT INTO costos_cliente (cantidad_pago,estado,fecha,id_cliente,id_costo,id_usuario,pagos_realizados) VALUES ('1','1','$fecha','$codCliente','$idCostos','$idusuario','0');";
		      ejecutarConsulta($sql);
			}
			
			/*****Funcion para generar asiento********************/
			$this->crearAsiento($idPago);
			/*fin funcion*/
			//echo($idPago);
			return $idPago;
			//return $sql;
	}
	//echo $sql;
}
public function crearAsiento($idPago){
	$montoTot=0;
	$idAsiento=0;
	$sql="SELECT  pf.fecha, pf.monto,pf.idventaagua,pf.idpago FROM pago_factura pf WHERE idpago=$idPago;";
	$rspta=ejecutarConsulta($sql);
	$sql="SELECT  id_cuenta FROM opcion_a_configurar WHERE cod='-5' ;";
    $reg1=ejecutarConsultaSimpleFila($sql);
    $cuentaErss=$reg1['id_cuenta'];
    $sql="SELECT  id_cuenta FROM opcion_a_configurar WHERE cod='-3' ;";
    $reg1=ejecutarConsultaSimpleFila($sql);
    $cuentaCons=$reg1['id_cuenta'];
	while ($reg=$rspta->fetch_row()) { 
	    $fechaAux = str_replace('/', '-', $reg[0]);
	    $fecha=date('Y-m-d', strtotime($fechaAux));
	    $monto=$reg[1];
	    $idventa=$reg[2]; 
	    $idpago=$reg[3];
	    /*se suma para ver si hay diferencia entre el pago y la factura*/
	    $sql="SELECT  sum(monto) suma FROM pago_factura WHERE idventaagua=$idventa;";
		$rspta21=ejecutarConsultaSimpleFila($sql);
		$diferencia=intval($rspta21['suma'])-$monto;
	    $montoAct=$monto;
	    /*se guarda el asiento contable*/
	    $sql="INSERT INTO asiento_contable (estado,fecha,origen,total_debe,total_haber) VALUES ('1','$fecha','venta','$monto','$monto');";
	    $idAsiento=ejecutarConsulta_retornarID($sql);
	    /*se guarda el id del asiento en la tabla pago factura*/
	    $sql="UPDATE pago_factura SET idasiento=$idAsiento WHERE idpago=$idpago;";
      	ejecutarConsulta($sql);
	    $montoTot=$montoTot+$monto;
	    $monto=$montoTot;
	    $sql="SELECT idcuentacontable,precio_venta FROM venta_detalle WHERE concepto like 'ERSSAN' AND idventa=$idventa;";
	    $reg1=ejecutarConsultaSimpleFila($sql);
	    if(!$reg1){
	    }else{  
		    $precio=$reg1['precio_venta'];
		    if($diferencia < 1){
		        if($monto < $precio){
		            $precio=$monto;
		        }
		        $sql="INSERT INTO asiento_detalle (id_asiento,id_cuenta,monto,tipo) VALUES ('$idAsiento','$cuentaErss','$precio','2');";
		        ejecutarConsulta($sql);
		        $monto=$monto-$precio;
		        $diferencia=$diferencia-$precio;
		    }
	   }        
	      
	    $sql="SELECT sum(precio_venta) precio_venta FROM venta_detalle WHERE idventa=$idventa AND not concepto like 'ERSSAN';";
	    $reg1=ejecutarConsultaSimpleFila($sql);
	    if(!$reg1){
	    }else{  
		    $precio=$reg1['precio_venta'];
		   
	         
		    if($diferencia < 1){
		        if($monto < $precio){
		            $precio=$monto;
		        }
		        $sql="INSERT INTO asiento_detalle (id_asiento,id_cuenta,monto,tipo) VALUES ('$idAsiento','$cuentaCons','$precio','2');";
		        ejecutarConsulta($sql);
		        $monto=$monto-$precio;
		        $diferencia=$diferencia-$precio;
		    }
	   }        
	    
	    $sql="SELECT  id_cuenta FROM opcion_a_configurar WHERE cod='-1' ;";
	    $rspta1=ejecutarConsulta($sql);
	    while ($reg1=$rspta1->fetch_row()) {

	      $idcuenta=$reg1[0];
	      $sql="INSERT INTO asiento_detalle (id_asiento,id_cuenta,monto,tipo) VALUES ('$idAsiento','$idcuenta','$montoAct','1');";
	      $query=ejecutarConsulta($sql);
	    }
	}

}
public function cargarLecturas(){
	session_start();
	$idusuario=$_SESSION['idusuario'];
	$fechaDef=date('Y-m-d');
	$sql="SELECT h.id FROM hidrometro h INNER JOIN cliente c on c.id=h.idcliente WHERE c.id_categoria=4;";
	$rspta=ejecutarConsulta($sql);
	while ($reg=$rspta->fetch_object()) {
		$idhidr=$reg->id;
		$sql="SELECT max(l.lectura) lectura,l.mesciclo,l.anhociclo FROM lectura l INNER JOIN hidrometro h on h.id=l.id_hidrometro WHERE h.id=$idhidr;";
		$rsp=ejecutarConsultaSimpleFila($sql);
		$lect=$rsp['lectura'];
		$mesciclo=$rsp['mesciclo']+1;
		$anhociclo=$rsp['anhociclo'];
		if($mesciclo > 12){
			$mesciclo=1;
			$anhociclo++;
		}
		
		$sql="INSERT INTO lectura (anhociclo,fecha_lectura,id_hidrometro,id_usuario,lectura,mesciclo,orden_emitido) VALUES ('$anhociclo','$fechaDef','$idhidr','$idusuario','$lect','$mesciclo','0')";
	 	$verif=ejecutarConsulta($sql);
	 	if(!$verif){
	 		return 'Errores al guardar el dato!!';
	 	}
	}
	return 'Guardado en forma exitosa!!';
}
public function editarCliente($idcategoria,$idsituacion,$idzona,$obs,$fecha,$idusuario,$idPersona){
	$sql="UPDATE cliente SET id_categoria='$idcategoria',fecha_carga='$fecha',id_situacion='$idsituacion',id_usuario='$idusuario',id_zona='$idzona',obs='$obs' WHERE id_persona=$idPersona;";
	return ejecutarConsulta($sql);
	
}
public function insertarNotaCred($timbrado,$numero,$idfac,$fecha,$descuento,$idusuario){
	/*$sql="SELECT idasiento from pago_factura WHERE idpago=$idfac;";
	$asiento=ejecutarConsultaSimpleFila($sql);
	$idAsiento=$asiento['idasiento'];
	$sql="SELECT max(monto) mont from asiento_detalle WHERE id_asiento=$idAsiento AND tipo=1;";
	$monto=ejecutarConsultaSimpleFila($sql);
	$mont=$monto['mont'];
	if($mont >= $descuento){*/
		
		
		$sql="INSERT INTO asiento_contable (estado,fecha,origen,total_debe,total_haber) VALUES ('1','$fecha','notacredito','$descuento','$descuento');";
	    $idAsiento=ejecutarConsulta_retornarID($sql);

	    $sql="SELECT id_cuenta from opcion_a_configurar WHERE cod='-6';";
		$cuenta=ejecutarConsultaSimpleFila($sql);
		$idCuenta=$cuenta['id_cuenta'];

		$sql="INSERT INTO asiento_detalle (id_asiento,id_cuenta,monto,tipo) VALUES ('$idAsiento','$idCuenta','$descuento','1');";
		ejecutarConsulta($sql);
		$sql="SELECT id_cuenta from opcion_a_configurar WHERE cod='-1';";
		$cuenta=ejecutarConsultaSimpleFila($sql);
		$idCuenta=$cuenta['id_cuenta'];

		$sql="INSERT INTO asiento_detalle (id_asiento,id_cuenta,monto,tipo) VALUES ('$idAsiento','$idCuenta','$descuento','2');";
		ejecutarConsulta($sql);
		
		$idVenta=$idfac;
		$sql="SELECT idventaagua from pago_factura WHERE idpago='$idfac';";
		$cuenta=ejecutarConsultaSimpleFila($sql);
		if(isset($cuenta['idventaagua'])){
			$idVenta=$cuenta['idventaagua'];
			$sql="SELECT v.idcliente cli FROM venta_agua v WHERE v.id='$idVenta';";
			$cuenta=ejecutarConsultaSimpleFila($sql);
			$idCliente=$cuenta['cli'];

			$sql="SELECT cc.id id FROM costos_cliente cc INNER JOIN costos c on c.id=cc.id_costo WHERE c.monto=$descuento AND cc.id_cliente=$idCliente;";
			$cuenta=ejecutarConsultaSimpleFila($sql);
			$idDeuda=$cuenta['id'];
			$sql="UPDATE costos_cliente SET cantidad_pago=0,pagos_realizados=0 WHERE id=$idDeuda;";
			ejecutarConsulta($sql);
		}else{
			$idfac=0;
		}

		$sql="UPDATE venta_agua SET total_venta=total_venta-$descuento WHERE id=$idVenta;";
		ejecutarConsulta($sql);

		

		$sql="INSERT INTO nota_credito (idpago,idusuario,numero_doc,timbrado,descuento,fecha,idasiento,idventa) VALUES ('$idfac','$idusuario','$numero','$timbrado','$descuento','$fecha','$idAsiento','$idVenta');";
		//echo($sql);
		return ejecutarConsulta($sql);
		/* return $sql;
		/*$sql="UPDATE pago_factura SET monto=monto-$descuento WHERE idpago=$idfac;";
		//echo($sql);
		return($sql);//return ejecutarConsulta($sql);*/
	/*}else{
		return 0;
	}
	return($descuento);*/
}
public function editar($idpersona,$tipo_persona,$nombre,$tipo_documento,$num_documento,$direccion,$telefono,$email){
	$sql="UPDATE persona SET tipo_persona='$tipo_persona', nombre='$nombre',tipo_documento='$tipo_documento',num_documento='$num_documento',direccion='$direccion',telefono='$telefono',email='$email' 
	WHERE idpersona='$idpersona'";
	return ejecutarConsulta($sql);
}
//funcion para eliminar datos
public function eliminar($idlectura){
	$sql="DELETE FROM lectura WHERE id='$idlectura'";
	ejecutarConsulta($sql);
}

//metodo para mostrar registros
public function mostrarDatos($idhidro){
	$sql="SELECT h.medidor,h.idCliente,p.nombre,p.apellido,p.num_documento,z.descripcion zona FROM hidrometro h INNER JOIN cliente c on c.id=h.idCliente INNER JOIN persona p on p.idpersona=c.id_persona INNER JOIN zona z on z.id=c.id_zona WHERE h.id=$idhidro;";
	return ejecutarConsultaSimpleFila($sql);
}
//metodo para mostrar registros
public function ultimaVenta($idCliente){
	$sql="SELECT  max(id) id  FROM venta_agua WHERE estado = 1 and idcliente=".$idCliente.";";
	return ejecutarConsultaSimpleFila($sql);
}
public function ultimoCosto($idCliente){
	$sql="SELECT  max(c.id) id  FROM costos_cliente c INNER JOIN costos cost on cost.id=c.id_costo WHERE c.pagos_realizados < c.cantidad_pago AND c.id_cliente=".$idCliente." AND not cost.descripcion LIKE '%Saldo pago Factura #%';";
	return ejecutarConsultaSimpleFila($sql);
}
public function ultimoCostoSaldo($idCliente){
	$sql="SELECT  max(c.id) id  FROM costos_cliente c INNER JOIN costos cost on cost.id=c.id_costo WHERE c.pagos_realizados < c.cantidad_pago AND c.id_cliente=".$idCliente." AND  cost.descripcion LIKE '%Saldo pago Factura #%';";
	return ejecutarConsultaSimpleFila($sql);
}
public function ultimaVentaDif($idCliente){
	$sql="SELECT count(c.id) cont FROM costos_cliente c INNER JOIN costos cost on cost.id=c.id_costo WHERE c.id_cliente=$idCliente AND cost.descripcion like '%Saldo pago Factura #%' AND c.estado=1;";
	$rspta1=ejecutarConsultaSimpleFila($sql);
	if(isset($rspta1['cont'])){
		if($rspta1['cont'] > 0){
			$sql="SELECT  max(va.id) id  FROM venta_agua va WHERE va.estado = 2 and idcliente='$idCliente' and va.tipo_comprobante like 'EXTRACTO';";
			return ejecutarConsultaSimpleFila($sql);
		}
		else{
			return ejecutarConsultaSimpleFila($sql);
		}
	}else{
		return ejecutarConsultaSimpleFila($sql);
	}
	
	
}
public function verificarSaldo($idFac){
	


	$sql="SELECT  sum(monto) diferencia  FROM  pago_factura  WHERE  idventaagua='$idFac' AND estado=1;";
	return ejecutarConsultaSimpleFila($sql);
}
public function verificarDescuento($idFac){
	$sql="SELECT  sum(descuento) descu  FROM  nota_credito  WHERE  idpago IN (SELECT  idpago  FROM  pago_factura  WHERE  idventaagua='$idFac');";
	$resp= ejecutarConsultaSimpleFila($sql);
	if(isset($resp)){
		return $resp;
	}else{
		return 0;
	}

}
public function compararDif($idFac){


	$sql="SELECT total_venta tot FROM  venta_agua  WHERE id='$idFac';";
	return ejecutarConsultaSimpleFila($sql);
}
//metodo para mostrar registros
public function contarLectura($idhidro){
	$sql="SELECT count(id) cont FROM lectura WHERE id_hidrometro = $idhidro;";
	return ejecutarConsultaSimpleFila($sql);
}
public function activarCliente($idhidro){
	$sql="UPDATE cliente c INNER JOIN hidrometro h SET c.id_situacion=1,c.estado=1 WHERE h.id='$idhidro' AND c.estado=10;";
	ejecutarConsulta($sql);
}
public function ultimoCiclo($idhidro){
	$sql="SELECT l.* FROM lectura l
    WHERE l.id IN (
        SELECT max(id) FROM lectura WHERE id_hidrometro = $idhidro
    );";
	return ejecutarConsultaSimpleFila($sql);
}
public function cicloAFac(){
	$sql="SELECT l.* FROM lectura l
    WHERE l.id IN (
        SELECT max(id) FROM lectura WHERE orden_emitido = 1
    );";
	return ejecutarConsultaSimpleFila($sql);
}
//metodo para mostrar registros
public function mostrar($idpersona){
	$sql="SELECT p.idpersona,p.direccion,p.email,p.nombre,p.num_documento,p.telefono,p.tipo_documento,p.tipo_persona,c.estado,c.fecha_carga,c.id_categoria,c.id_persona,c.id_situacion,c.id_usuario,c.id_zona,c.obs,c.orden FROM persona p INNER JOIN cliente c on c.id_persona=p.idpersona WHERE p.idpersona='$idpersona'";
	return ejecutarConsultaSimpleFila($sql);
}
public function mostrarCliente($idcliente){
	$sql="SELECT p.idpersona,p.direccion,p.email,p.nombre,p.num_documento,p.telefono,p.tipo_documento,p.tipo_persona,c.estado,c.fecha_carga,c.id_categoria,c.id_persona,c.id_situacion,c.id_usuario,c.id_zona,c.obs,c.orden FROM persona p INNER JOIN cliente c on c.id_persona=p.idpersona WHERE c.id='$idcliente';";
	return ejecutarConsultaSimpleFila($sql);
}
//metodo para mostrar registros
public function selectUltimaOrden(){
	$sql="SELECT coalesce(max(orden),0) ord FROM cliente";
	return ejecutarConsultaSimpleFila($sql);
}
public function verifCosto($idUsu){

	$sql="SELECT c.id,cost.descripcion,cost.monto FROM costos_cliente c INNER JOIN costos cost on cost.id=c.id_costo WHERE c.id_cliente='$idUsu' AND c.estado=1 AND pagos_realizados < cantidad_pago AND NOT cost.descripcion like 'Saldo pago Factura #%';";
	return ejecutarConsultaSimpleFila($sql);
}
public function verifPagado($idUsu){

	$sql="SELECT p.idpago, p.fecha, p.estado, p.monto, p.idventaagua, p.idusuario, p.numfactura, p.idasiento FROM pago_factura p  WHERE p.idpago IN (SELECT max(p.idpago) FROM pago_factura p INNER JOIN venta_agua v on v.id=p.idventaagua WHERE v.idcliente= $idUsu) ;";
	//echo($sql);
	return ejecutarConsultaSimpleFila($sql);
}
public function verifNoPagado($idUsu){

	$sql="SELECT p.id FROM venta_agua p  WHERE p.id IN (SELECT max(v.id) FROM venta_agua v  WHERE v.idcliente= $idUsu) ;";
	return ejecutarConsultaSimpleFila($sql);
}
public function pago($idpago){

	$sql="SELECT v.total_venta-p.monto monto FROM pago_factura p INNER JOIN venta_agua v on v.id=p.idventaagua WHERE p.idpago=$idpago;";
	//echo($sql);
	return ejecutarConsultaSimpleFila($sql);
}
public function noPago($idpago){

	$sql="SELECT v.total_venta monto FROM venta_agua v  WHERE v.id=$idpago;";
	//echo($sql);
	return ejecutarConsultaSimpleFila($sql);
}
//listar registros
public function listarp(){
	$sql="SELECT * FROM persona WHERE tipo_persona='Proveedor'";
	return ejecutarConsulta($sql);
}
//listar registros
public function listarDetalles($idFact){
	$sql = "SELECT id,cantidad,concepto descripcion,precio_venta exentas FROM venta_detalle WHERE idventa=$idFact ORDER BY id desc;";
	return ejecutarConsulta($sql);
}
public function listarCosto($idCosto){
	$sql = "SELECT cc.id,c.descripcion descripcion,c.monto exentas FROM costos_cliente cc INNER JOIN costos c on c.id=cc.id_costo WHERE cc.id=$idCosto ORDER BY id desc;";
	return ejecutarConsulta($sql);
}
//listar registros
public function listarDetallesDif($idFact){
	$sql = "SELECT id,cantidad,concepto descripcion,precio_venta exentas FROM venta_detalle WHERE idventa='$idFact' AND NOT concepto like 'ERSSAN' order by id desc;";
	return ejecutarConsulta($sql);
}
public function listarDetallesDifErssan($idFact){
	$sql = "SELECT id,cantidad,concepto descripcion,precio_venta exentas FROM venta_detalle WHERE idventa='$idFact' AND concepto like 'ERSSAN' order by id desc";
	return ejecutarConsulta($sql);
}
public function listarLecturas($id){
	$sql="SELECT * FROM lectura WHERE id_hidrometro=$id";
	return ejecutarConsulta($sql);
}
public function listar(){
	$sql="SELECT h.id,h.medidor,c.orden,c.id id_usuario,z.descripcion zona,p.nombre,p.apellido,p.num_documento,cat.descripcion categoria FROM cliente c INNER JOIN zona z on z.id=c.id_zona INNER JOIN persona p on p.idpersona=c.id_persona LEFT JOIN hidrometro h on h.idcliente=c.id INNER JOIN categoria_usuario cat on cat.id=c.id_categoria ;";
	return ejecutarConsulta($sql);
}
public function listarCaja($fecha){
	$fechaSig=date("Y-m-d",strtotime($fecha."+ 1 days"));
	$sql="SELECT pf.fecha,pf.monto,pf.numfactura,per.nombre,per.apellido,per.num_documento,cli.id numusuario FROM pago_factura pf INNER JOIN venta_agua vf on vf.id=pf.idventaagua INNER JOIN cliente cli on cli.id=vf.idcliente INNER JOIN persona per on per.idpersona=cli.id_persona WHERE pf.estado=1 AND pf.fecha BETWEEN '$fecha' AND '$fechaSig';";
	return ejecutarConsulta($sql);

	//echo($sql);
}
public function listarUltLectura($idHidro){
	$sql="SELECT l.* FROM lectura l
    WHERE l.id IN (
        SELECT max(id) FROM lectura WHERE id_hidrometro = $idHidro AND orden_emitido=1
    );";
	return ejecutarConsulta($sql);
}
public function listarNuevatLectura($idHidro,$mesp,$anhop){
	$sql="SELECT max(lectura_ant) lectura FROM lectura  WHERE id_hidrometro=$idHidro;";
	$sql="SELECT l.* FROM lectura l
    WHERE l.id IN (
        SELECT max(id) FROM lectura WHERE id_hidrometro = $idHidro AND mesciclo=$mesp AND anhociclo=$anhop
    );";
	return ejecutarConsulta($sql);
}
public function listarAsig(){
	$sql="SELECT c.id,c.orden,c.obs,p.nombre,z.descripcion zona,s.descripcion situacion,cat.descripcion categoria FROM cliente c INNER JOIN persona p on p.idpersona=c.id_persona INNER JOIN zona z on z.id=c.id_zona INNER JOIN situacion s on s.id=c.id_situacion INNER JOIN categoria_usuario cat on cat.id=c.id_categoria;";
	return ejecutarConsulta($sql);
}
}

 ?>
