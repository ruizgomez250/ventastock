<?php 
//activamos almacenamiento en el buffer
ob_start();
session_start();
if (!isset($_SESSION['ventas'])) {
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
  <h1 class="box-title">Caja </h1>
 
</div>
<!--box-header-->
<!--centro-->
<div class="panel-body table-responsive" id="listadoregistros">
  <table id="tbllistado" class="table table-striped table-bordered table-condensed table-hover">
    <thead>
      <th>Cobrar</th>
      <th>Orden</th>
      <th>Codigo Usuario</th>
      <th>Documento</th>
      <th>Nombre</th>
      <th>Sector</th>
      <th>Medidor</th>
      <th>Categoria</th>
    </thead>
    <tbody>
    </tbody>
    <tfoot>
      <th>Cobrar</th>
      <th>Orden</th>
      <th>Codigo Usuario</th>
      <th>Documento</th>
      <th>Nombre</th>
      <th>Sector</th>
      <th>Medidor</th>
      <th>Categoria</th>
    </tfoot>   
  </table>
</div>
<div class="panel-body" style="height: 400px;" id="formularioregistros">
  
    
     
    
    
    
    
    
  
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
  <div class="modal fade" id="myModal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content borde1">
        <!--Cabecera-->
        <div class="modal-header align-self-center"> 
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <div class="card">
            <div class="card-header">
              <i class="fa fa-ruble"></i> <strong>Ingresar Pago</strong>
            </div>
              
          </div>
        </div>
        <!--body-->
        <div class="modal-body">
                
              <div>
                <div class="row">
                  <div class="col-xs-12 text-center ">
                      <h4><strong id="nombUs">Extracto del Usuario</strong></h4>
                  </div>
                </div>
                <div class="row">
                  <div class="col-xs-3 text-center ">
                      <h6>Nº Doc.:<label id="ndoc"></label></h6>
                  </div>
                  <div class="col-xs-3 text-center ">
                      <h6>Cod. Usu.:<label id="codus"></label></h6>
                  </div>
                  <div class="col-xs-6 text-center ">
                      <h6>Nombre:<label id="nombMod"></label></h6>
                  </div>
                      
                      
                  
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <table id = "tablaModalFech" class="table table-hover table-bordered">
                            <thead align="center">
                            <tr class="fondo">
                            <th class="txtWhite"><b>Descripcion</b></th>
                            <th class="txtWhite"><b>Exentas</b></th>
                                      </tr>
                                    </thead>
                                    <tbody id="tablaModBody">
                                    </tbody>
                        </table>
                    </div>
                    <div class="col-12 text-center">
                        <h2>
                            Cargar el monto a abonar
                        </h2>
                      </div>

                </div>
                  <div class="row">
                      <div class="col-3">
                        <h2><label>Monto a Abonar Gs. </label>
                          <input type="hidden"  name="idfac" id="idfac" value="" placeholder="Numero" >
                        <input type="number" oninput="verifMonto()" name="montoAbonar" id="montoAbonar"  class="btn-success"></h2>
                        <input type="hidden" name="montoAbonar1" id="montoAbonar1" class="form-control" value="" placeholder="Numero">
                        
                      </div>
                      <div class="col-3 text-center">
                        <h1>
                        <label>Diferencia Gs.</label>
                        <label id="diferenciaAbonar" class="btn-danger">Diferencia</label>                   

                        </h1>
                      </div>
                      
                </div>
                <div class="row">
                      <div class="col-3">
                        <h3><label>Dinero del Usuario Gs.</label>
                        <input type="number" oninput="verifVuelto()" name="dineroUs" id="dineroUs" class="btn-warning"></h3>                        
                      </div>
                      <div class="col-3 text-center">
                        <h3>
                        <label>Vuelto Gs.</label>
                        <label id="vuelto" class="btn-info">0</label>                   

                        </h3>
                      </div>
                      
                </div>
                      <button class=" btn btn-primary " id="guardarMedida" onclick="pagar()" > Guardar</button>
                  


                </div>
              </div>
        </div>
        <!--pie modal-->
          
      </div>
    </div>
  <!-- fin Modal-->
<?php 
}else{
 require 'noacceso.php'; 
}
require 'footer.php';
 ?>
 <script src="scripts/caja.js"></script>
 <?php 
}

ob_end_flush();
  ?>
