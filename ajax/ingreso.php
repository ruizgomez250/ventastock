<?php 
require_once "../modelos/Ingreso.php";
if (strlen(session_id())<1) 
	session_start();

$ingreso=new Ingreso();

$idingreso=isset($_POST["idingreso"])? limpiarCadena($_POST["idingreso"]):"";
$idproveedor=isset($_POST["idproveedor"])? limpiarCadena($_POST["idproveedor"]):"";
$idusuario=1;//$_SESSION["idusuario"];
//$idusuario=$_POST["idusuario"];
$tipo_comprobante=isset($_POST["tipo_comprobante"])? limpiarCadena($_POST["tipo_comprobante"]):"0";
$serie_comprobante=isset($_POST["serie_comprobante"])? limpiarCadena($_POST["serie_comprobante"]):"0";
$num_comprobante=isset($_POST["num_comprobante"])? limpiarCadena($_POST["num_comprobante"]):"0";
$fecha_hora=isset($_POST["fecha_hora"])? limpiarCadena($_POST["fecha_hora"]):"";
$impuesto=isset($_POST["impuesto"])? limpiarCadena($_POST["impuesto"]):"";
$total_compra=isset($_POST["total_compra"])? limpiarCadena($_POST["total_compra"]):"";



switch ($_GET["op"]) {
	case 'guardaryeditar':
	if (empty($idingreso)) {
		$rspta=$ingreso->insertar($idproveedor,$idusuario,$tipo_comprobante,$serie_comprobante,$num_comprobante,$fecha_hora,$impuesto,$total_compra,$_POST["idarticulo"],$_POST["cantidad"],$_POST["precio_compra"],$_POST["precio_venta"],$_POST["margen"]);
		echo $rspta ? "Datos registrados correctamente" : "No se pudo registrar los datos";
	
	}else{
        
	}
		break;
	

	case 'anular':
		$rspta=$ingreso->anular($idingreso);
		echo $rspta ? "Ingreso anulado correctamente" : "No se pudo anular el ingreso";
		break;
	
	case 'mostrar':
		$rspta=$ingreso->mostrar($idingreso);
		echo json_encode($rspta);
		break;

	case 'listarDetalle':
		//recibimos el idingreso
		$id=$_GET['id'];
		$rspta=$ingreso->listarImpuesto($id);
		$porcentaje=0;
		while ($reg=$rspta->fetch_object()) {
			$porcentaje=$reg->impuesto;
		}
		
		$rspta=$ingreso->listarDetalle($id);
		$total=0;
		echo ' <thead style="background-color:#A9D0F5">
        <th>Opciones</th>
        <th>Articulo</th>
        <th>Cantidad</th>
        <th>Precio Compra</th>
        <th>Impuesto</th>
        <th>Margen</th>
        <th>Precio Venta</th>
        <th>Total</th>
       </thead>';
		while ($reg=$rspta->fetch_object()) {
			$tot=($reg->precio_compra*$reg->cantidad);
			$impuesto=($tot*$porcentaje)/100;
			$subt=($reg->precio_compra*$reg->cantidad)+$impuesto;
			echo '<tr class="filas">
			<td></td>
			<td>'.$reg->nombre.'</td>
			<td>'.$reg->cantidad.'</td>
			<td>'.$reg->precio_compra.'</td>
			<td>'.$impuesto.'</td>
			<td>'.$reg->margen.'</td>
			<td>'.$reg->precio_venta.'</td>
			<td>'.$subt.'</td>
			<td></td>
			</tr>';
			$total=$total+($reg->precio_compra*$reg->cantidad)+$impuesto;
		}
		echo '<tfoot>
         <th>TOTAL</th>
         <th></th>
         <th></th>
         <th></th>
         <th></th>
         <th></th>
         <th></th>
         <th><h4 id="total">Gs. '.$total.'</h4><input type="hidden" name="total_compra" id="total_compra"></th>
       </tfoot>';
		break;

    case 'listar':
		$rspta=$ingreso->listar();
		$data=Array();

		while ($reg=$rspta->fetch_object()) {
			$data[]=array(
            "0"=>($reg->estado=='Aceptado')?'<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->idingreso.')"><i class="fa fa-eye"></i></button>'.' '.'<button class="btn btn-danger btn-xs" onclick="anular('.$reg->idingreso.')"><i class="fa fa-close"></i></button>':'<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->idingreso.')"><i class="fa fa-eye"></i></button>',
            "1"=>$reg->fecha,
            "2"=>$reg->proveedor,
            "3"=>$reg->usuario,
            "4"=>$reg->tipo_comprobante,
            "5"=>$reg->serie_comprobante. '-' .$reg->num_comprobante,
            "6"=>$reg->total_compra,
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

		case 'selectProveedor':
			require_once "../modelos/Persona.php";
			$persona = new Persona();

			$rspta = $persona->listarp();

			while ($reg = $rspta->fetch_object()) {
				echo '<option value='.$reg->idpersona.'>'.$reg->nombre.'</option>';
			}
			break;
			case 'selectImpuesto':
			
			$rspta = $ingreso->list();

			while ($reg = $rspta->fetch_object()) {
				echo '<option value='.$reg->porcentaje.'>'.$reg->descripcion.'-'.$reg->porcentaje.'%'.'</option>';
			}
			break;

			case 'listarArticulos':
			require_once "../modelos/Articulo.php";
			$articulo=new Articulo();

				$rspta=$articulo->listarActivos();
				$data=Array();

				while ($reg=$rspta->fetch_object()) {
					$data[]=array(
		            "0"=>'<button class="btn btn-warning" onclick="agregarDetalle('.$reg->idarticulo.',\''.$reg->nombre.'\')"><span class="fa fa-plus"></span></button>',
		            "1"=>$reg->nombre,
		            "2"=>$reg->categoria,
		            "3"=>$reg->codigo,
		            "4"=>$reg->stock,
		            "5"=>"<img src='../files/articulos/".$reg->imagen."' height='50px' width='50px'>"
		          
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