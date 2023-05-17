var tabla;

//funcion que se ejecuta al inicio
function init() {
    mostrarform(false);
    listar();

    $("#formulario").on("submit", function(e) {
        guardaryeditar(e);
    })

    //cargamos los items al celect categoria
    $.post("../ajax/articulo.php?op=selectCategoria", function(r) {
        $("#idcategoria").html(r);
        $("#idcategoria").selectpicker('refresh');
    });
    $("#imagenmuestra").hide();
}

//funcion limpiar
function limpiar() {
    $("#codigo").val("");
    $("#nombre").val("");
    $("#descripcion").val("");
    //$("#stock").val("");
    $("#imagenmuestra").attr("src", "");
    $("#imagenactual").val("");
    $("#print").hide();
    $("#idarticulo").val("");
}

//funcion mostrar formulario
function mostrarform(flag) {
    limpiar();
    if (flag) {
        $("#listadoregistros").hide();
        $("#formularioregistros").show();
        $("#btnGuardar").prop("disabled", false);
        $("#btnagregar").hide();
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
            url: '../ajax/articulo.php?op=listar',
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
    $("#btnGuardar").prop("disabled", true);
    var formData = new FormData($("#formulario")[0]);
    for (let [name, value] of formData) {
       // key1 = value1, luego key2 = value2
    }
    $.ajax({
        url: "../ajax/articulo.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,

        success: function(datos) {
            bootbox.alert(datos);
            mostrarform(false);
            tabla.ajax.reload();
        }
    });

    limpiar();
}

function mostrar(idarticulo) {
    $.post("../ajax/articulo.php?op=mostrar", { idarticulo: idarticulo },
        function(data, status) {
            data = JSON.parse(data);
            mostrarform(true);

            $("#idcategoria").val(data.idcategoria);
            $("#idcategoria").selectpicker('refresh');
            $("#codigo").val(data.codigo);
            $("#nombre").val(data.nombre);
            $("#stock").val(data.stock);
            $("#stock1").val(data.stock);
            $("#descripcion").val(data.descripcion);
            $("#imagenmuestra").show();
            $("#imagenmuestra").attr("src", "../files/articulos/" + data.imagen);
            $("#imagenactual").val(data.imagen);
            $("#idarticulo").val(data.idarticulo);
            generarbarcode();
        })
}


//funcion para desactivar
function desactivar(idarticulo) {
    bootbox.confirm("¿Esta seguro de desactivar este dato?", function(result) {
        if (result) {
            $.post("../ajax/articulo.php?op=desactivar", { idarticulo: idarticulo }, function(e) {
                bootbox.alert(e);
                tabla.ajax.reload();
            });
        }
    })
}

function activar(idarticulo) {
    bootbox.confirm("¿Esta seguro de activar este dato?", function(result) {
        if (result) {
            $.post("../ajax/articulo.php?op=activar", { idarticulo: idarticulo }, function(e) {
                bootbox.alert(e);
                tabla.ajax.reload();
            });
        }
    })
}

function generarbarcode() {
    codigo = $("#codigo").val();
    JsBarcode("#barcode", codigo);
    $("#print").show();

}

function imprimir() {
    $("#print").printArea();
}

init();