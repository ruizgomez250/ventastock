var tabla;

//funcion que se ejecuta al inicio
function init() {
    mostrarform(false);
    listar();

    $("#formulario").on("submit", function(e) {
        guardaryeditar(e);
    });

    //cargamos los items al select proveedor
    $.post("../ajax/ingreso.php?op=selectProveedor", function(r) {
        $("#idproveedor").html(r);
        $('#idproveedor').selectpicker('refresh');
    });
    //Cargamos los impuestos
    /*$.post("../ajax/ingreso.php?op=selectImpuesto", function(r) {
        $("#impuesto").html(r);
        $('#impuesto').selectpicker('refresh');
    });*/

}

//funcion limpiar
function limpiar() {

    $("#idproveedor").val("");
    $("#proveedor").val("");
    $("#serie_comprobante").val("");
    $("#num_comprobante").val("");
    $("#impuesto").val("");

    $("#total_compra").val("");
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
            url: '../ajax/ingreso.php?op=listar',
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
        ], //ordenar (columna, orden)

        "columnDefs": [
            { "width": "5%", "targets": 0 },
            { "width": "25%", "targets": 1 },

            { "width": "5%", "targets": 2 }
        ],
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
            url: '../ajax/articulo.php?op=listarArticulos',
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

    $.ajax({
        url: "../ajax/ingreso.php?op=guardaryeditar",
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

function mostrar(idingreso) {
    $.post("../ajax/ingreso.php?op=mostrar", { idingreso: idingreso },
        function(data, status) {
            data = JSON.parse(data);
            mostrarform(true);

            $("#idproveedor").val(data.idproveedor);
            $("#idproveedor").selectpicker('refresh');
            $("#tipo_comprobante").val(data.tipo_comprobante);
            $("#tipo_comprobante").selectpicker('refresh');
            $("#serie_comprobante").val(data.serie_comprobante);
            $("#num_comprobante").val(data.num_comprobante);
            $("#fecha_hora").val(data.fecha);
            $("#impuesto").val(data.impuesto);
            $("#impuesto").selectpicker('refresh');
            $("#idingreso").val(data.idingreso);
            //ocultar y mostrar los botones
            $("#btnGuardar").hide();
            $("#btnCancelar").show();
            $("#btnAgregarArt").hide();
        });
    $.post("../ajax/ingreso.php?op=listarDetalle&id=" + idingreso, function(r) {
        $("#detalles").html(r);
    });

}


//funcion para desactivar
function anular(idingreso) {
    bootbox.confirm("¿Esta seguro de desactivar este dato?", function(result) {
        if (result) {
            $.post("../ajax/ingreso.php?op=anular", { idingreso: idingreso }, function(e) {
                bootbox.alert(e);
                tabla.ajax.reload();
            });
        }
    })
}

//declaramos variables necesarias para trabajar con las compras y sus detalles
var impuesto = 18;
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

function agregarDetalle(idarticulo, articulo) {
    var cantidad = 1;
    var precio_compra = 1;
    var precio_venta = 1;
    impuesto = $("#impuesto").val();
    if (impuesto == null) {
        impuesto = $("#impuesto").prop("selectedIndex", 0).val();
    }
    impuesto = (precio_compra * impuesto) / 100;
    if (idarticulo != "") {
        var subtotal = cantidad * precio_compra;
        var fila = '<tr class="filas" id="fila' + cont + '">' +
            '<td><button type="button" class="btn btn-danger" onclick="eliminarDetalle(' + cont + ')">X</button></td>' +
            '<td><input type="hidden" name="idarticulo[]" value="' + idarticulo + '">' + articulo + '</td>' +
            '<td><input type="number" name="cantidad[]" onchange="calcImpuesto(' + cont + ')" id="cantidad[]" value="' + cantidad + '"></td>' +
            '<td><input type="number" name="precio_compra[]" onchange="calcImpuesto(' + cont + ')" id="precio_compra[]" value="' + precio_compra + '"></td>' +
            '<td><span name="impuest" id="impuest' + cont + '">' + impuesto + ' </span></td>' +
            '<td><input type="number" name="margen[]" id="margen[]" onchange="calcMargen(' + cont + ')" value="0"></td>' +
            '<td><input type="number" name="precio_venta[]" id="precio_venta[]" onchange="calcPorc(' + cont + ')" value="' + precio_venta + '"></td>' +
            '<td><span id="subtotal' + cont + '" name="subtotal">' + subtotal + '</span></td>' +
            '</tr>';
        cont++;
        detalles++;
        $('#detalles').append(fila);
        modificarSubtotales();

    } else {
        alert("error al ingresar el detalle, revisar las datos del articulo ");
    }
}

function calcPorc(fila) {
    precV = document.getElementsByName("precio_venta[]");
    precV = precV[fila].value;
    prec = document.getElementsByName("precio_compra[]");
    precCo = prec[fila].value;
    diferencia = precV - precCo;
    porcMargen = 0;
    if (diferencia < 0) {
        document.getElementsByName("precio_venta[]")[fila].value = Math.round(precCo);
    } else {
        porcMargen = (diferencia * 100) / precCo;

    }
    document.getElementsByName("margen[]")[fila].value = Math.round(porcMargen);
}

function calcMargen(fila) {
    marg = document.getElementsByName("margen[]");
    margV = marg[fila].value;
    prec = document.getElementsByName("precio_compra[]");
    precCo = prec[fila].value;
    totConMargen = ((precCo * margV) / 100) + (precCo * 1);

    document.getElementsByName("precio_venta[]")[fila].value = Math.round(totConMargen);
}

function calcImpuesto(fila) {
    prec = document.getElementsByName("precio_compra[]");
    precCo = prec[fila].value;
    comp = document.getElementsByName("cantidad[]");
    cant = comp[fila].value;
    precCo = precCo * cant;
    impuesto = $("#impuesto").val();
    if (impuesto == null) {
        impuesto = $("#impuesto").prop("selectedIndex", 0).val();
    }
    impuesto = (precCo * impuesto) / 100;
    document.getElementsByName("impuest")[fila].innerHTML = impuesto;
    calcMargen(fila);
    modificarTotales();

}

function modificarSubtotales() {
    var cant = document.getElementsByName("cantidad[]");
    var prec = document.getElementsByName("precio_compra[]");
    //var sub=document.getElementsByName("subtotal");

    for (var i = 0; i < cant.length; i++) {
        var inpC = cant[i];
        var inpP = prec[i];
        var inpS = sub[i];

        inpS.value = inpC.value * inpP.value;
        document.getElementsByName("subtotal")[i].innerHTML = inpS.value;
    }

    calcularTotales();
}

function modificarTotales() {
    var cant = document.getElementsByName("cantidad[]");
    var prec = document.getElementsByName("precio_compra[]");
    //var sub=document.getElementsByName("subtotal");

    for (var i = 0; i < cant.length; i++) {
        var inpC = cant[i];
        var inpP = prec[i];
        var subt = 0;

        subt = (inpC.value * inpP.value) + (document.getElementsByName("impuest")[i].innerHTML * 1); //+(document.getElementsByName("impuest")[i].value*1);
        //alert();
        document.getElementsByName("subtotal")[i].innerHTML = subt;
    }

    calcularTotales();
}

function calcularTotales() {
    var sub = document.getElementsByName("subtotal");
    var total = 0.0;
    for (var i = 0; i < sub.length; i++) {

        total = total + (document.getElementsByName("subtotal")[i].innerHTML * 1);

    }
    $("#total").html("Gs." + total);
    $("#total_compra").val(total);
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