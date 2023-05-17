<?php
//activamos almacenamiento en el buffer
ob_start();
session_start();
if (!isset($_SESSION['nombre'])) {
  header("Location: login.html");
}else{


require 'header.php';

if ($_SESSION['ventas']==1) {

 ?>
    <div class="content-wrapper">
    <!-- Main content -->
    <section class="content">

      <!-- Default box -->
      <div class="row">
        <div class="col-md-12">
      <div class="box">
<div class="box-header with-border">
  <h1 class="box-title">Ventas <button class="btn btn-success" onclick="mostrarform(true)"><i class="fa fa-plus-circle"></i>Agregar</button></h1>
  <div class="box-tools pull-right">
    
  </div>
</div>
<!--box-header-->
<!--centro-->
<div class="panel-body table-responsive" id="listadoregistros">
  <table id="tbllistado" class="table table-striped table-bordered table-condensed table-hover">
    <thead>
      <th>Opciones</th>
      <th>Fecha</th>
      <th>Cliente</th>
      <th>Usuario</th>
      <th>Documento</th>
      <th>Número</th>
      <th>Total Venta</th>
      <th>Estado</th>
    </thead>
    <tbody>
    </tbody>
    <tfoot>
      <th>Opciones</th>
      <th>Fecha</th>
      <th>Cliente</th>
      <th>Usuario</th>
      <th>Documento</th>
      <th>Número</th>
      <th>Total Venta</th>
      <th>Estado</th>
    </tfoot>   
  </table>
</div>
<div class="panel-body" style="height: 400px;" id="formularioregistros">
  <form action="" name="formulario" id="formulario" method="POST">
    <div class="form-group col-lg-8 col-md-8 col-xs-12">
      <label for="">Cliente(*):</label>
      <input class="form-control" type="hidden" name="idventa" id="idventa">
      <select name="idcliente" id="idcliente" class="form-control selectpicker" data-live-search="true" required>
        
      </select>
    </div>
      <div class="form-group col-lg-4 col-md-4 col-xs-12">
      <label for="">Fecha(*): </label>
      <input class="form-control" type="date" name="fecha_hora" id="fecha_hora" required>
    </div>
     <div class="form-group col-lg-5 col-md-5 col-xs-12">
      <label for="">Tipo Comprobante(*): </label>
     <select name="tipo_comprobante" id="tipo_comprobante" onchange="verificarPagos()" class="form-control selectpicker" required>
       <option value="Boleta">Boleta</option>
       <option value="Factura">Factura</option>
       <option value="Ticket">Ticket</option>
       <option value="Credito">Credito</option>
     </select>
    </div>
     <div class="form-group col-lg-2 col-md-2 col-xs-6">
      <label for="">Serie: </label>
      <input class="form-control" type="text" name="serie_comprobante" id="serie_comprobante" maxlength="20" placeholder="Serie">
    </div>
     <div class="form-group col-lg-2 col-md-2 col-xs-6">
      <label for="">Número: </label>
      <input class="form-control" type="text" name="num_comprobante" id="num_comprobante" maxlength="20" placeholder="Número" required>
    </div>
    <div class="form-group col-lg-3 col-md-3 col-xs-6">
      <label for="">Impuesto: </label>
      <select name="impuesto" id="impuesto" class="form-control selectpicker" required>
       
     </select>
    </div>
     <div id="factCred" class="row">
          <div class="form-group col-lg-2 col-md-6 col-xs-12">
              <label for="">Cantidad de pagos(*): </label>
             <input class="form-control" value="1" type="number" name="cant_pago" id="cant_pago"  placeholder="Número" onchange="cargarPag()">
          </div>
          <div class="form-group col-lg-2 col-md-6 col-xs-12">
              <label for="">Pagos: </label>
              <a data-toggle="modal" href="#modalPagare"><button class="btn btn-success" ><i class="fa fa-plus-circle"></i>Agregar </button></a>
          </div>
          <div class="form-group col-lg-2 col-md-6 col-xs-12"  style="display: none">
              <table id="tblpagare1" class="table table-striped table-bordered table-condensed table-hover">
              <thead>
                <th>Cuota</th>
                <th>Fecha Pago</th>
              </thead>
              <tbody>
                
              </tbody>
            </table>
          </div>
        </div>
      <div class="row">
      <div class="form-group col-lg-3 col-md-3 col-sm-6 col-xs-12">
       <a data-toggle="modal" href="#myModal">
         <button id="btnAgregarArt" type="button" class="btn btn-primary"><span class="fa fa-plus"></span>Agregar Articulos</button>
       </a>
      </div>
    </div>
<div class="form-group col-lg-12 col-md-12 col-xs-12">
     <table id="detalles" class="table table-striped table-bordered table-condensed table-hover">
       <thead style="background-color:#A9D0F5">
        <th>Opciones</th>
        <th>Articulo</th>
        <th>Cantidad</th>
        <th>Precio Venta</th>
        <th>I.V.A.</th>
        <th>Descuento</th>
        <th>Subtotal</th>
       </thead>
       <tfoot>
         <th>TOTAL</th>
         <th></th>
         <th></th>
         <th></th>
         <th></th>
         <th></th>
         <th><h4 id="total">Gs. 0.00</h4><input type="hidden" name="total_compra" id="total_compra"></th>
       </tfoot>
       <tbody>
         
       </tbody>
     </table>
    </div>
    <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <button class="btn btn-primary" type="submit" id="btnGuardar"><i class="fa fa-save"></i>  Guardar</button>
      <button class="btn btn-danger" onclick="cancelarform()" type="button" id="btnCancelar"><i class="fa fa-arrow-circle-left"></i> Cancelar</button>
    </div>
  </form>
</div>
<!--fin centro-->
      </div>
      </div>
      </div>
      <!-- /.box -->

    </section>
    <!-- /.content -->
  </div>
<!--Modal-->
  <div class="modal fade" id="modalPago" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">Seleccione una cuenta</h4>
        </div>
        <div class="modal-body">
          <table id="pagosrealizados" class="table table-striped table-bordered table-condensed table-hover">
            <thead>
              <th>Cuota</th>
              <th>Opciones</th>
              <th>Cuota</th>
              <th>Fecha Deuda</th>
              <th>Fecha Vencimiento</th>
              <th>Fecha Pago</th>
            </thead>
            <tbody>
              
            </tbody>
            <tfoot>
              <th>Cuota</th>
              <th>Opciones</th>
              <th>Cuota</th>
              <th>Fecha Deuda</th>
              <th>Fecha Vencimiento</th>
              <th>Fecha Pago</th>
            </tfoot>
          </table>
        </div>
        <div class="modal-footer">
          <button class="btn btn-default" type="button" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>
  <!-- fin Modal-->
  <!--Modal-->
  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 65% !important;">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">Seleccione un Articulo</h4>
        </div>
        <div class="modal-body">
          <table id="tblarticulos" class="table table-striped table-bordered table-condensed table-hover">
            <thead>
              <th>Opciones</th>
              <th>Nombre</th>
              <th>Categoria</th>
              <th>Código</th>
              <th>Stock</th>
              <th>Precio Venta</th>
              <th>Imagen</th>
            </thead>
            <tbody>
              
            </tbody>
            <tfoot>
              <th>Opciones</th>
              <th>Nombre</th>
              <th>Categoria</th>
              <th>Código</th>
              <th>Stock</th>
              <th>Precio Venta</th>
              <th>Imagen</th>
            </tfoot>
          </table>
        </div>
        <div class="modal-footer">
          <button class="btn btn-default" type="button" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>
  <!-- fin Modal-->
  <!--Modal-->
  <div class="modal fade" id="modalPagare" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">Guardar las fechas de los pagos</h4>
        </div>
        <div class="modal-body">
            <table id="tblpagare" class="table table-striped table-bordered table-condensed table-hover">
              <thead>
                <th>Cuota</th>
                <th>Fecha Pago</th>
              </thead>
              <tbody>
                
              </tbody>
            </table>
        </div>
        <div class="modal-footer">
          <button class="btn btn-default" type="button" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>
  <!-- fin Modal-->
<?php 
}else{
 require 'noacceso.php'; 
}

require 'footer.php';
 ?>
 <script src="scripts/venta.js"></script>
 <?php 
}

ob_end_flush();
  ?>

