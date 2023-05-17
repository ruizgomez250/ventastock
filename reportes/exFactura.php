<?php 
//activamos almacenamiento en el buffer
ob_start();
if (strlen(session_id())<1) 
  session_start();

if (!isset($_SESSION['nombre'])) {
  echo "debe ingresar al sistema correctamente para visualizar el reporte";
}else{

if ($_SESSION['ventas']==1) {

//incluimos el archivo factura
require('Factura.php');

//establecemos los datos de la empresa
$logo="logo.png";
$ext_logo="png";
$empresa="Tecnology Center S.A.C.";
$documento="1074528547";
$direccion="Calle los alpes 120";
$telefono="958524158";
$email="angelinos257@gmail.com";
$total=0;
//obtenemos los datos de la cabecera de la venta actual
require_once "../modelos/Venta.php";
$venta= new Venta();
$rsptav=$venta->ventacabecera($_GET["id"]);

//recorremos todos los valores que obtengamos
$regv=$rsptav->fetch_object();
$size=0;
//configuracion de la factura
$pdf = new PDF_Invoice('P','mm','A4');
$pdf->AddPage();

//enviamos datos de la empresa al metodo addSociete de la clase factura
$pdf->addSociete(utf8_decode($empresa),
                 $documento."\n".
                 utf8_decode("Direccion: "). utf8_decode($direccion)."\n".
                 utf8_decode("Telefono: ").$telefono."\n".
                 "Email: ".$email,$logo,$ext_logo);

$pdf->fact_dev("$regv->tipo_comprobante ","$regv->serie_comprobante- $regv->num_comprobante");
$pdf->temporaire( "" );
$pdf->addDate($regv->fecha);
$impuesto=$regv->impuesto;
//enviamos los datos del cliente al metodo addClientAddresse de la clase factura
$pdf->addClientAdresse(utf8_decode($regv->cliente),
                       "Domicilio: ".utf8_decode($regv->direccion), 
                       $regv->tipo_documento.": ".$regv->num_documento, 
                       "Email: ".$regv->email, 
                       "Telefono: ".$regv->telefono);

//establecemos las columnas que va tener lña seccion donde mostramos los detalles de la venta
$cols=array( "CODIGO"=>23,
	         "DESCRIPCION"=>78,
	         "CANTIDAD"=>22,
	         "P.U."=>25,
	         "DSCTO"=>20,
	         "SUBTOTAL"=>22);
$pdf->addCols( $cols);
$cols=array( "CODIGO"=>"L",
             "DESCRIPCION"=>"L",
             "CANTIDAD"=>"C",
             "P.U."=>"R",
             "DSCTO"=>"R",
             "SUBTOTAL"=>"C" );
$pdf->addLineFormat( $cols);
$pdf->addLineFormat($cols); 

//actualizamos el valor de la coordenada "y" quie sera la ubicacion desde donde empecemos a mostrar los datos 
$y=85;

//obtenemos todos los detalles del a venta actual
$rsptad=$venta->ventadetalles($_GET["id"]);

while($regd=$rsptad->fetch_object()){
  $aux=($regd->subtotal*$impuesto)/100;
  $total=$total+$aux+$regd->subtotal;
  //echo($subtSinIVA);
  $line = array( "CODIGO"=>"$regd->codigo",
                 "DESCRIPCION"=>utf8_decode("$regd->articulo"),
                 "CANTIDAD"=>"$regd->cantidad",
                 "P.U."=>"$regd->precio_venta",
                 "DSCTO"=>"$regd->descuento",
                 "SUBTOTAL"=>"$regd->subtotal");
  $size = $pdf->addLine( $y, $line );
  $y += $size +2;

}  

/*aqui falta codigo de letras*/
/*require_once "Letras.php";
$V = new EnLetras();*/

//$total=$regv->total_venta; 
/*$V=new EnLetras(); 
$V->substituir_un_mil_por_mil = true;
*/

 $con_letra=strtoupper(convertir($total)); 
$pdf->addCadreTVAs("---".$con_letra);


//mostramos el impuesto
$pdf->addTVAs( $impuesto, $total, "Gs/ ");
$pdf->addCadreEurosFrancs("IVA"." $regv->impuesto %");
$pdf->Output('Reporte de Venta' ,'I');

	}else{
echo "No tiene permiso para visualizar el reporte";
}

}



/*******Convertir numeros a letras******/
function basico($numero) {
$valor = array ('uno','dos','tres','cuatro','cinco','seis','siete','ocho',
'nueve','diez','once','doce','trece','catorce','quince','dieciseis','diecisiete','dieciocho','diecinueve','veinte','veintiuno ','veintidos ','veintitres ', 'veinticuatro','veinticinco',
'veintiseis','veintisiete','veintiocho','veintinueve');
return $valor[$numero - 1];
}

function decenas($n) {
$decenas = array (30=>'treinta',40=>'cuarenta',50=>'cincuenta',60=>'sesenta',
70=>'setenta',80=>'ochenta',90=>'noventa');
if( $n <= 29) return basico($n);
$x = $n % 10;
if ( $x == 0 ) {
return $decenas[$n];
} else return $decenas[$n - $x].' y '. basico($x);
}

function centenas($n) {
$cientos = array (100 =>'cien',200 =>'doscientos',300=>'trecientos',
400=>'cuatrocientos', 500=>'quinientos',600=>'seiscientos',
700=>'setecientos',800=>'ochocientos', 900 =>'novecientos');
if( $n >= 100) {
if ( $n % 100 == 0 ) {
return $cientos[$n];
} else {
$u = (int) substr($n,0,1);
$d = (int) substr($n,1,2);
return (($u == 1)?'ciento':$cientos[$u*100]).' '.decenas($d);
}
} else return decenas($n);
}

function miles($n) {
if($n > 999) {
if( $n == 1000) {return 'mil';}
else {
$l = strlen($n);
$c = (int)substr($n,0,$l-3);
$x = (int)substr($n,-3);
if($c == 1) {$cadena = 'mil '.centenas($x);}
else if($x != 0) {$cadena = centenas($c).' mil '.centenas($x);}
else $cadena = centenas($c). ' mil';
return $cadena;
}
} else return centenas($n);
}

function millones($n) {
if($n == 1000000) {return 'un millón';}
else {
$l = strlen($n);
$c = (int)substr($n,0,$l-6);
$x = (int)substr($n,-6);
if($c == 1) {
$cadena = ' millon ';
} else {
$cadena = ' millones ';
}
return miles($c).$cadena.(($x > 0)?miles($x):'');
}
}
function convertir($n) {
switch (true) {
case ( $n >= 1 && $n <= 29) : return basico($n); break;
case ( $n >= 30 && $n < 100) : return decenas($n); break;
case ( $n >= 100 && $n < 1000) : return centenas($n); break;
case ($n >= 1000 && $n <= 999999): return miles($n); break;
case ($n >= 1000000): return millones($n);
}
}
ob_end_flush();
  ?>