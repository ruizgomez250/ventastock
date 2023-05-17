var tabla;

//funcion que se ejecuta al inicio
function init() {
    mostrarform(false);
    listar();
    $("#formulario").on("submit", function(e) {
        guardaryeditar(e);
    });

    //cargamos los items al select cliente
    $.post("../ajax/venta.php?op=selectCliente", function(r) {
        $("#idcliente").html(r);
        $('#idcliente').selectpicker('refresh');
    });
    //Cargamos los impuestos
    $.post("../ajax/ingreso.php?op=selectImpuesto", function(r) {
        $("#impuesto").html(r);
        $('#impuesto').selectpicker('refresh');
    });
    cargarPag();
    verificarPagos()

}
function verificarPagos(){
    var tipo_comprobante=$("#tipo_comprobante option:selected").text();
    if(tipo_comprobante=='Credito'){
            document.getElementById("factCred").style.display = "block";
        }else{
            document.getElementById("factCred").style.display = "none";
            //Cargamos los metodos de pagos al contado
           
        }
}
function cargarPag(){

    n =  new Date();

    //Año
    y = n.getFullYear();
    //Mes
    m = n.getMonth() + 1;
    if(m < 10)
        m='0'+m;
    //Día
    d = n.getDate();
    if(d < 10)
        d='0'+d;
    fech=y+ "-" + m + "-" + d;
    cantp=document.getElementById("cant_pago").value;
    contAux=1
    var valor = [];
    var valor1 = [];
    //valor.splice(0,valor.length);
    for (let index = 0; index < cantp; index++) {    
        fecha= '<tr class="filas" id="fila'+index+'">'+
        '<td><input type="date" name="fechP[]" value="'+fech+'"></td>'+
        '</tr>';
        fecha1= '<tr class="filas" id="fila1'+index+'">'+
        '<td><input type="date" name="fechP1[]" onblur="cambiarFecha('+index+')" value="'+fech+'"></td>'+
        '</tr>';
    var book = new datosA(contAux, fecha); 
    var book1 = new datosA(contAux, fecha1); 
    valor.push(book);
    valor1.push(book1);
  //  console.log(obj);
        contAux++;
    }
    $('#tblpagare').DataTable({
      paging: false,
      searching: false,
      info: false,
      data: valor1,
      "bDestroy":true,
      columns: [
         { title: "Pago", data: "num" },
        { title: "Fecha", data: "fecha" }
      ]
    });
    $('#tblpagare1').DataTable({
      paging: false,
      searching: false,
      info: false,
      data: valor,
      "bDestroy":true,
      columns: [
         { title: "Pago", data: "num" },
        { title: "Fecha", data: "fecha" }
      ]
    });

}
function guardPago(id,index,idDeuda){
    fech=document.getElementById('pago'+index).value;
    $.post("../ajax/venta.php?op=guardPago",{idingreso : id,fechaPago:fech,iddeuda:idDeuda},
        function(data,status)
        {
            alert(data);
        });
}
function cargarPago(id){
    tabla=$('#pagosrealizados').dataTable({
        "aProcessing": true,//activamos el procedimiento del datatable
        "aServerSide": true,//paginacion y filrado realizados por el server
        dom: 'Bfrtip',//definimos los elementos del control de la tabla
        buttons: [
                  'copyHtml5',
                  'excelHtml5',
                  'csvHtml5',
                  'pdf'
        ],
        "ajax":
        {
            url:'../ajax/venta.php?op=listarDeuda&id='+id,
            dataType : "json",
            error:function(e){
                console.log(e.responseText);
            }
        },
        "bDestroy":true,
        "iDisplayLength":12,//paginacion
        "order":[[0,"asc"]]//ordenar (columna, orden)
    }).DataTable();
}
function datosA(num, fecha){
this.num = num;
this.fecha = fecha;
}
//funcion limpiar
function limpiar() {

    $("#idcliente").val("");
    $("#cliente").val("");
    $("#serie_comprobante").val("");
    $("#num_comprobante").val("");
    $("#impuesto").val("");

    $("#total_venta").val("");
    $(".filas").remove();
    $("#total").html("0");

    //obtenemos la fecha actual
    var now = new Date();
    var day = ("0" + now.getDate()).slice(-2);
    var month = ("0" + (now.getMonth() + 1)).slice(-2);
    var today = now.getFullYear() + "-" + (month) + "-" + (day);
    $("#fecha_hora").val(today);

    //marcamos el primer tipo_documento
    $("#tipo_comprobante").val("Boleta");
    $("#tipo_comprobante").selectpicker('refresh');

}

//funcion mostrar formulario
function mostrarform(flag) {
    verificarPagos();
    limpiar();
    if (flag) {
        $("#listadoregistros").hide();
        $("#formularioregistros").show();
        //$("#btnGuardar").prop("disabled",false);
        $("#btnagregar").hide();
        listarArticulos();

        $("#btnGuardar").hide();
        $("#btnCancelar").show();
        detalles = 0;
        $("#btnAgregarArt").show();


    } else {
        $("#listadoregistros").show();
        $("#formularioregistros").hide();
        $("#btnagregar").show();
    }
}

//cancelar form
function cancelarform() {
    limpiar();
    mostrarform(false);
}

//funcion listar
function listar() {
    tabla = $('#tbllistado').dataTable({
        "aProcessing": true, //activamos el procedimiento del datatable
        "aServerSide": true, //paginacion y filrado realizados por el server
        dom: 'Bfrtip', //definimos los elementos del control de la tabla
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdf'
        ],
        "ajax": {
            url: '../ajax/venta.php?op=listar',
            type: "get",
            dataType: "json",
            error: function(e) {
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
        "iDisplayLength": 5, //paginacion
        "order": [
                [0, "desc"]
            ] //ordenar (columna, orden)
    }).DataTable();
}

function listarArticulos() {
    tabla = $('#tblarticulos').dataTable({
        "aProcessing": true, //activamos el procedimiento del datatable
        "aServerSide": true, //paginacion y filrado realizados por el server
        dom: 'Bfrtip', //definimos los elementos del control de la tabla
        buttons: [

        ],
        "ajax": {
            url: '../ajax/articulo.php?op=listarArticulos1',
            type: "get",
            dataType: "json",
            error: function(e) {
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
        "iDisplayLength": 5, //paginacion
        "order": [
                [0, "desc"]
            ] //ordenar (columna, orden)
    }).DataTable();
}
//funcion para guardaryeditar
function guardaryeditar(e) {
    e.preventDefault(); //no se activara la accion predeterminada 
    //$("#btnGuardar").prop("disabled",true);
    var formData = new FormData($("#formulario")[0]);
    // Mostrar los valores
    /*   for (let [name, value] of formData) {
           alert(`${name} = ${value}`); // key1 = value1, luego key2 = value2
       }*/


    $.ajax({
        url: "../ajax/venta.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,

        success: function(datos) {
            bootbox.alert(datos);
            mostrarform(false);
            listar();
        }
    });

    limpiar();
}

function mostrar(idventa) {
    $.post("../ajax/venta.php?op=mostrar", { idventa: idventa },
        function(data, status) {
            data = JSON.parse(data);
            mostrarform(true);

            $("#idcliente").val(data.idcliente);
            $("#idcliente").selectpicker('refresh');
            $("#tipo_comprobante").val(data.tipo_comprobante);
            $("#tipo_comprobante").selectpicker('refresh');
            $("#serie_comprobante").val(data.serie_comprobante);
            $("#num_comprobante").val(data.num_comprobante);
            $("#fecha_hora").val(data.fecha);
            $("#impuesto").val(data.impuesto);
            $("#idventa").val(data.idventa);

            //ocultar y mostrar los botones
            $("#btnGuardar").hide();
            $("#btnCancelar").show();
            $("#btnAgregarArt").hide();
        });
    $.post("../ajax/venta.php?op=listarDetalle&id=" + idventa, function(r) {
        $("#detalles").html(r);
    });

}


//funcion para desactivar
function anular(idventa) {
    bootbox.confirm("¿Esta seguro de desactivar este dato?", function(result) {
        if (result) {
            $.post("../ajax/venta.php?op=anular", { idventa: idventa }, function(e) {
                bootbox.alert(e);
                tabla.ajax.reload();
            });
        }
    })
}

//declaramos variables necesarias para trabajar con las compras y sus detalles
var impuesto = 10;
var cont = 0;
var detalles = 0;

$("#btnGuardar").hide();
$("#tipo_comprobante").change(marcarImpuesto);

function marcarImpuesto() {
    var tipo_comprobante = $("#tipo_comprobante option:selected").text();
    if (tipo_comprobante == 'Factura') {
        $("#impuesto").val(impuesto);
    } else {
        $("#impuesto").val("0");
    }
}

function agregarDetalle(idarticulo, articulo, precio_venta) {
    var cantidad = 1;
    var descuento = 0;

    if (idarticulo != "") {
        var subtotal = cantidad * precio_venta;
        var iva=(subtotal*10)/100;
        var fila = '<tr class="filas" id="fila' + cont + '">' +
            '<td><button type="button" class="btn btn-danger" onclick="eliminarDetalle(' + cont + ')">X</button></td>' +
            '<td><input type="hidden" name="idarticulo[]" value="' + idarticulo + '">' + articulo + '</td>' +
            '<td><input type="number" name="cantidad[]" id="cantidad[]" value="' + cantidad + '" onchange="modificarSubtotales()"></td>' +
            '<td><input type="number" name="precio_venta[]" id="precio_venta[]" value="' + precio_venta + '" onchange="modificarSubtotales()"></td>' +
            '<td><input type="number" name="iva[]" id="iva[]" value="' + iva + '" readonly></td>' +
            '<td><input type="number" name="descuento[]" value="' + descuento + '" onchange="modificarSubtotales()"></td>' +
            '<td><span id="subtotal' + cont + '" name="subtotal">' + subtotal + '</span></td>' +
            '<td><button type="button" onclick="modificarSubtotales()" class="btn btn-info"><i class="fa fa-refresh"></i></button></td>' +
            '</tr>';
        cont++;
        detalles++;
        $('#detalles').append(fila);
        modificarSubtotales();

    } else {
        alert("error al ingresar el detalle, revisar las datos del articulo ");
    }
}

function modificarSubtotales() {
    var cant = document.getElementsByName("cantidad[]");
    var prev = document.getElementsByName("precio_venta[]");
    var desc = document.getElementsByName("descuento[]");
    var sub = document.getElementsByName("subtotal");
    var iva = document.getElementsByName("iva");
     var impuesto=document.getElementById("impuesto").value;
    if(impuesto.length < 1){
        impuesto=10;

    }
    for (var i = 0; i < cant.length; i++) {
        var inpV = cant[i];
        var inP = prev[i];
        var inpS = sub[i];
        var des = desc[i];
        var aux=(inpV.value * inP.value)-des.value;
        var ivat = (aux * impuesto)/100;
        document.getElementsByName("iva[]")[i].value=ivat;
        inpS.value = (inpV.value * inP.value) - des.value+ivat;
        document.getElementsByName("subtotal")[i].innerHTML = inpS.value;
        
    }

    calcularTotales();
}

function calcularTotales() {
    var sub = document.getElementsByName("subtotal");
    var total = 0.0;

    for (var i = 0; i < sub.length; i++) {
        total += document.getElementsByName("subtotal")[i].value;
    }
    $("#total").html("Gs." + total);
    $("#total_venta").val(total);
    evaluar();
}

function evaluar() {

    if (detalles > 0) {
        $("#btnGuardar").show();
    } else {
        $("#btnGuardar").hide();
        cont = 0;
    }
}

function eliminarDetalle(indice) {
    $("#fila" + indice).remove();
    calcularTotales();
    detalles = detalles - 1;
}

init();