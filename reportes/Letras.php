<?php 
/*
// Numeros
// =======
// 
// Conversiones de numeros PHP
// 
// Clase original de Felix de Jesus Carrillo Celerino (dcreate)
//  https://github.com/dcreate/Numeros/blob/master/letras.php
// 
// 
// Modificada por Miquel Botanch
// 
// Cambios:
// 
//    * He adaptado el código para que muestre dos decimales.
// 
//    * Permite especificar nombres de moneda y de su centésima parte en singular y plural.
// 
//    * Permite de manera opcional substituir un_mil por mil (como en el castellano de España)
// 
//    * Adaptado para que sea directamente compatible con la versión original, por si se actualiza la clase,
//      NO SEA NECESARIO corregir las llamadas a lHa función ValorEnLetras().  (ya que ahora tiene 5 parámetros en vez de 2)
// 
//    * Añadida variable $anadir_MN_al_final para añadir M.N. al final de la cadena (en España no se usa este acrónimo)
//
*/
class EnLetras 
{ 
  var $Void = ""; 
  var $SP = " "; 
  var $Dot = "."; 
  var $Zero = "0"; 
  var $Neg = "Menos"; 
  var $substituir_un_mil_por_mil = true;
  var $anadir_MN_al_final = true;
  var $tratar_decimales = true;
function ValorEnLetras($n, $Moneda_singular)  
{ 
  switch (true) {
case ( $n >= 1 && $n <= 29) : return basico($n); break;
case ( $n >= 30 && $n < 100) : return decenas($n); break;
case ( $n >= 100 && $n < 1000) : return centenas($n); break;
case ($n >= 1000 && $n <= 999999): return miles($n); break;
case ($n >= 1000000): return millones($n);
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

}

}
//-------------- Programa principal ------------------------ 




?>