<?php 
//activamos almacenamiento en el buffer
ob_start();
if (strlen(session_id())<1) 
  session_start();

if (!isset($_SESSION['nombre'])) {
  echo "debe ingresar al sistema correctamente para vosualizar el reporte";
}else{

if ($_SESSION['ventas']==1) {

?>

<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
	<link rel="stylesheet" href="../public/css/ticket.css">
</head>
<body onload="window.print();">
	<?php 
// incluimos la clase venta
require_once "../modelos/PagoCuota.php";

$venta = new PagoCuota();

//en el objeto $rspta obtenemos los valores devueltos del metodo ventacabecera del modelo
$rspta = $venta->ventacabecera($_GET["id"],$_GET["pag"]);

$reg=$rspta->fetch_object();

//establecemos los datos de la empresa
$empresa = "ELECTRODOMESTICOS";
$documento = "102589524";
$direccion = "calle los alpes 120";
$telefono = "854715648";
$email = "angelinos257@gmail.com";
	 ?>
<div class="zona_impresion">
	<!--codigo imprimir-->
	<br>
	<table border="0" align="center" width="300px">
		<tr>
			<td align="center">
				<!--mostramos los datos de la empresa en el doc HTML-->
				.::<strong> <?php echo $empresa; ?></strong>::.<br>
				<?php echo $documento; ?><br>
				<?php echo $direccion . '-'.$telefono; ?><br>
			</td>
		</tr>
		<tr>
			<td align="center"><?php echo $reg->fecha; ?></td>
		</tr>
		<tr> 
			<td align="center"></td>
		</tr>
		<tr>
			<!--mostramos los datos del cliente -->
			<td>Cliente: <?php echo $reg->cliente; ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo $reg->tipo_documento.": ".$reg->num_documento; ?>
			</td>
		</tr>
		<tr>
			<td>
				N° de venta: <?php echo $reg->serie_comprobante." - ".$reg->num_comprobante; ?>
			</td>
		</tr>
	</table>
	<br>

	<!--mostramos lod detalles de la venta -->

	<table border="0" align="center" width="300px">
		<tr>
			<td>PAGO</td>
			<td>DESCRIPCION</td>
			<td align="right">IMPORTE</td>
		</tr>
		<tr>
			<td colspan="3">=============================================</td>
		</tr>
		<?php
		$rsptad = $venta->ventadetalles($_GET["id"]);
		$cantidad=0;
		while ($regd = $rsptad->fetch_object()) {
			$rsptad1 = $venta->ventadetalles1($_GET["id"],$_GET["pag"]);
			$pagadas=0;
			while ($regd1 = $rsptad1->fetch_object()) {
				$pagadas=$regd1->pag;
			}
		 	echo "<tr>";
		 	echo "<td> ".$pagadas." /".$regd->cuot."</td>";
		 	echo "<td>Cuota</td>";
		 	echo "<td align='right'>".$regd->monto." Gs.</td>";
		 	echo "</tr>";
		 	$cantidad=$regd->monto;
		 } 

		 ?>
		<tr>
			<td>&nbsp;</td>
			<td align="right"><b>TOTAL:</b></td>
			<td align="right"><b> <?php echo $cantidad.' '; ?>Gs.</b></td>
		</tr>
		<tr>
		</tr>
		<tr>
			<td colspan="3">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="3" align="center">¡Gracias por su pago!</td>
		</tr>
		<tr>
			<td colspan="3" align="center">Luque - Paraguay</td>
		</tr>
		<tr>
			<td colspan="3" align="center"></td>
		</tr>
	</table>
	<br>
</div>
<p>&nbsp;</p>
</body>
</html>



<?php

	}else{
echo "No tiene permiso para visualizar el reporte";
}

}


ob_end_flush();
  ?>