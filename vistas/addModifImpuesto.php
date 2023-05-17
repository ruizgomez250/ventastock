<?php
$estado=1;
//activamos almacenamiento en el buffer
ob_start();
session_start();
if (!isset($_SESSION['nombre'])) {
  header("Location: login.html");
}else{


require 'header.php';

if ($_SESSION['compras']==1) {

 ?>
    <div class="content-wrapper">
    <!-- Main content -->
    <section class="content">

      <!-- Default box -->
      <div class="row">
        <div class="col-md-12">
      <div class="box">
<div class="box-header with-border">
  <h1 class="box-title">Ingresos <button class="btn btn-success" onclick="mostrarform(true)"><i class="fa fa-plus-circle"></i>Agregar</button></h1>
  <div class="box-tools pull-right">
    
  </div>
</div>
<!--box-header-->
<!--centro-->
<div class="panel-body table-responsive" id="listadoregistros">
  <table id="tbllistado" class="table table-striped table-bordered table-condensed table-hover">
    <thead>
      <th>Opciones</th>
      <th>Descripcion</th>
      <th>Porcentaje</th>
      <th>Estado</th>
    </thead>
    <tbody>
    </tbody>
    <tfoot>
      <th>Opciones</th>
      <th>Descripcion</th>
      <th>Porcentaje</th>
      <th>Estado</th>
    </tfoot>   
  </table>
</div>
<div class="panel-body" style="height: 400px;" id="formularioregistros">
  <form action="" name="formulario" id="formulario" method="POST">
    <div class="form-group col-lg-2 col-md-2 col-xs-12">
      <div class="card-header">
                        <label for="cedul">
                        <font style="vertical-align: inherit;">Activo &nbsp;&nbsp;</font>
                      </label>
                      <input class="form-control" type="hidden" name="estad" id="estad" value="1">
                      <input id="activo" name="estado" class="" type="radio" value="1" <?php if($estado == 1)echo('checked'); ?>>
                      
                      <label for="cedul">
                        <font style="vertical-align: inherit;">Inactivo</font>
                      </label>
                      <input id="inactivo" name="estado" class="" type="radio" value="0" <?php if($estado == 0)echo('checked'); ?>>

      </div>
    </div>
    <div class="form-group col-lg-5 col-md-5 col-xs-12">
      <label for="">Descripcion(*):</label>
      <input class="form-control" type="hidden" name="idimpuesto" id="idimpuesto">
      <input class="form-control" type="text" name="descripcion" id="descripcion">
      
    </div>
    <div class="form-group col-lg-3 col-md-3 col-xs-12">
      <label for="">Porcentaje(*): </label>
      <input class="form-control" type="number" name="porcentaje" id="porcentaje" required>
    </div>
    
    <div class="form-group col-lg-3 col-md-3 col-sm-6 col-xs-12">
    <!-- <a data-toggle="modal" href="#myModal"> -->
       <button class="btn btn-primary" type="submit" id="btnAgregarArt"><i class="fa fa-save"></i>  Guardar</button>
       <button class="btn btn-danger" onclick="cancelarform()" type="button"><i class="fa fa-arrow-circle-left"></i> Cancelar</button>
     <!-- </a> -->
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
  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
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
<?php 
}else{
 require 'noacceso.php'; 
}

require 'footer.php';
 ?>
 <script src="scripts/addModifImpuesto.js"></script>
 <?php 
}

ob_end_flush();
  ?>

