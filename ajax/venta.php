<?php 
require_once "../modelos/Venta.php";
if (strlen(session_id())<1) 
	session_start();
$_SESSION['idusuario']=1;
		$_SESSION['nombre']='angel';
		$_SESSION['imagen']='1535417472.jpg';
		$_SESSION['login']='admin';
$venta = new Venta();

$idventa=isset($_POST["idventa"])? $_POST["idventa"]:"";
$idcliente=isset($_POST["idcliente"])? $_POST["idcliente"]:"";
$idusuario=$_SESSION["idusuario"];
$iddeuda=isset($_POST["iddeuda"])? limpiarCadena($_POST["iddeuda"]):"";
$tipo_comprobante=isset($_POST["tipo_comprobante"])? $_POST["tipo_comprobante"]:"";
$serie_comprobante=isset($_POST["serie_comprobante"])? $_POST["serie_comprobante"]:"";
$num_comprobante=isset($_POST["num_comprobante"])? $_POST["num_comprobante"]:"";
$fecha_hora=isset($_POST["fecha_hora"])? $_POST["fecha_hora"]:"";
$impuesto=isset($_POST["impuesto"])? $_POST["impuesto"]:"";
$total_venta=isset($_POST["total_venta"])? $_POST["total_venta"]:"";
$cantidad_pagos=isset($_POST["cant_pago"])? limpiarCadena($_POST["cant_pago"]):"";
$fechaPago=isset($_POST["fechaPago"])? limpiarCadena($_POST["fechaPago"]):"";





switch ($_GET["op"]) {
	case 'guardaryeditar':
	if (empty($idventa)) {
		$rspta=$venta->insertar($idcliente,$idusuario,$tipo_comprobante,$serie_comprobante,$num_comprobante,$fecha_hora,$impuesto,$total_venta,$_POST["idarticulo"],$_POST["cantidad"],$_POST["precio_venta"],$_POST["descuento"],$_POST["fechP"],$cantidad_pagos); 
		echo $rspta ? "Datos registrados correctamente" : "No se pudo registrar los datos";
	}else{
        
	}
		break;
	

	case 'anular':
		$rspta=$venta->anular($idventa);
		echo $rspta ? "Ingreso anulado correctamente" : "No se pudo anular el ingreso";
		break;
	
	case 'mostrar':
		$rspta=$venta->mostrar($idventa);
		echo json_encode($rspta);
		break;

	case 'listarDetalle':
		//recibimos el idventa
		$id=$_GET['id'];

		$rspta=$venta->listarDetalle($id);
		$total=0;
		echo ' <thead style="background-color:#A9D0F5">
        <th>Opciones</th>
        <th>Articulo</th>
        <th>Cantidad</th>
        <th>Precio Venta</th>
        <th>Descuento</th>
        <th>Subtotal</th>
       </thead>';
		while ($reg=$rspta->fetch_object()) {
			echo '<tr class="filas">
			<td></td>
			<td>'.$reg->nombre.'</td>
			<td>'.$reg->cantidad.'</td>
			<td>'.$reg->precio_venta.'</td>
			<td>'.$reg->descuento.'</td>
			<td>'.$reg->subtotal.'</td></tr>';
			$total=$total+($reg->precio_venta*$reg->cantidad-$reg->descuento);
		}
		echo '<tfoot>
         <th>TOTAL</th>
         <th></th>
         <th></th>
         <th></th>
         <th></th>
         <th><h4 id="total">S/. '.$total.'</h4><input type="hidden" name="total_venta" id="total_venta"></th>
       </tfoot>';
		break;

    case 'listar':
		$rspta=$venta->listar();
		$data=Array();

		while ($reg=$rspta->fetch_object()) {
				$pagoCuot='';
                 if ($reg->tipo_comprobante=='Ticket') {
                 	$url='../reportes/exTicket.php?id=';
                 }else{
                    $url='../reportes/exFactura.php?id=';
                    if($reg->tipo_comprobante=='Credito')
                    	$pagoCuot='<a data-toggle="modal" href="#modalPago"><button onclick="cargarPago('.$reg->idventa.')" class="btn btn-success btn-xs"><i class="fa fa-rouble"></i></button></a>';
                 }

			$data[]=array(
            "0"=>(($reg->estado=='Aceptado')?'<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->idventa.')"><i class="fa fa-eye"></i></button>'.' '.'<button class="btn btn-danger btn-xs" onclick="anular('.$reg->idventa.')"><i class="fa fa-close"></i></button>':'<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->idventa.')"><i class="fa fa-eye"></i></button>').
            '<a target="_blank" href="'.$url.$reg->idventa.'"> <button class="btn btn-info btn-xs"><i class="fa fa-file"></i></button></a> '.$pagoCuot,
            "1"=>$reg->fecha,
            "2"=>$reg->cliente,
            "3"=>$reg->usuario,
            "4"=>$reg->tipo_comprobante,
            "5"=>$reg->serie_comprobante. '-' .$reg->num_comprobante,
            "6"=>$reg->total_venta,
            "7"=>($reg->estado=='Aceptado')?'<span class="label bg-green">Aceptado</span>':'<span class="label bg-red">Anulado</span>'
              );
		}
		$results=array(
             "sEcho"=>1,//info para datatables
             "iTotalRecords"=>count($data),//enviamos el total de registros al datatable
             "iTotalDisplayRecords"=>count($data),//enviamos el total de registros a visualizar
             "aaData"=>$data); 
		echo json_encode($results);
		break;
		case 'listarDeuda':
		$idd=$_GET["id"];
		$rspta=$venta->listarDeuda($idd);
		$data=Array();
		$cont=0;
		while ($reg=$rspta->fetch_object()) {
			$pagoCuot='';
			$fechp='<input id="pago'.$cont.'" name="pago'.$cont.'"  class="form-control" type="date" value="'.date('Y-m-d').'">';
			if(!isset($reg->pagofecha)){
				$pagoCuot='<a data-toggle="modal" href="#modalPago"><button onclick="guardPago('.$idd.','.$cont.','.$reg->id.')" class="btn btn-success btn-xs"><i class="fa fa-rouble"></i></button></a>';
			}else{
				$fechp=$reg->pagofecha;
				$pagoCuot='<a target="_blank" href=" ../reportes/pagoCuota.php?id='.$idd.'&pag='.$reg->id.'"> <button class="btn btn-info btn-xs"><i class="fa fa-file"></i></button></a>';
			}
			$data[]=array(
						"0"=>$cont+1,
            "1"=>$pagoCuot,
            "2"=>$reg->monto,
            "3"=>$reg->fecha_emision,
            "4"=>$reg->fecha_vencimiento,
            "5"=>$fechp
              );
			$cont++;
		}
		$results=array(
             "sEcho"=>1,//info para datatables
             "iTotalRecords"=>count($data),//enviamos el total de registros al datatable
             "iTotalDisplayRecords"=>count($data),//enviamos el total de registros a visualizar
             "aaData"=>$data); 
		echo json_encode($results);
		//echo($idd);
		break;
		case 'guardPago':
		$fecha = str_replace('/', '-', $fechaPago);
	    $fechap = date('Y-m-d', strtotime($fecha));
			$rspta=$venta->guardPag($idventa,$fechap,$iddeuda);
			echo $rspta ? "Datos registrados correctamente" : "No se pudo registrar los datos";
			//echo $rspta;
			//echo($_POST["fechP"]);
	
		break;
		case 'selectCliente':
			require_once "../modelos/Persona.php";
			$persona = new Persona();

			$rspta = $persona->listarc();

			while ($reg = $rspta->fetch_object()) {
				echo '<option value='.$reg->idpersona.'>'.$reg->nombre.'</option>';
			}
			break;

			case 'listarArticulos':
			require_once "../modelos/Articulo.php";
			$articulo=new Articulo();

				$rspta=$articulo->listarActivosVenta();
		$data=Array();

		while ($reg=$rspta->fetch_object()) {
			$data[]=array(
            "0"=>'<button class="btn btn-warning" onclick="agregarDetalle('.$reg->idarticulo.',\''.$reg->nombre.'\','.$reg->precio_venta.')"><span class="fa fa-plus"></span></button>',
            "1"=>$reg->nombre,
            "2"=>$reg->categoria,
            "3"=>$reg->codigo,
            "4"=>$reg->stock,
            "5"=>$reg->precio_venta,
            "6"=>"<img src='../files/articulos/".$reg->imagen."' height='50px' width='50px'>"
          
              );
		}
		$results=array(
             "sEcho"=>1,//info para datatables
             "iTotalRecords"=>count($data),//enviamos el total de registros al datatable
             "iTotalDisplayRecords"=>count($data),//enviamos el total de registros a visualizar
             "aaData"=>$data); 
		echo json_encode($results);

				break;
}
 ?>