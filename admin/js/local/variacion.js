jQuery(document).ready(function ($) {
  $("#btnNeoDirectory").click(function () {
    $(".containAddName").css("display", "");
    $("#lblColor").css("color", "#6c757d");
    $("#sIdDirectory").attr("disabled", "disabled");
    $("#fileVNombreFile").val("");
    $("#btnNeoDirectory").attr("disabled", "disabled");
  });

  $("#btnCerrarDirectory").click(function () {
    $(".containAddName").css("display", "none");
    $("#sIdDirectory").removeAttr("disabled");
    $("#lblColor").css("color", "#212529");
    $("#btnNeoDirectory").removeAttr("disabled");
  });

  $("#btnNuevaVariacion").click(function () {
    $("#mdNuevaVariacion").modal("show");
  });

  $("#btnGenerarCode").click(function () {
    var codex = generateCodeRand(7);
    $("#idCupon").val(codex);
  });

  $("#tblListVariables").DataTable({
    order: [[8, "desc"]],
    language: {
      url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json",
    },
    lengthMenu: [
      [25, 50, 100, -1],
      [25, 50, 100, "Todos"],
    ],
    responsive: true,
    columnDefs: [
      { responsivePriority: 1, targets: 0 },
      { responsivePriority: 2, targets: -1 },
    ],
  });

  $(document).on("click", "a[data-del_var_id]", function () {
    var d_id = this.dataset.del_var_id;
    var d_url = SolicitudesAjax.url;
    var d_non = SolicitudesAjax.seguridad;
    // console.log("RDX > "+ n_id+ " | "+n_url+ " | "+ n_non);

    var objProducto = s_global_array_var_match_all.find(function (producto) {
      return producto.varId == d_id;
    });
   
    var codigo      = objProducto.varSKU;
    var file_ruta   = objProducto.varDirRuta;
    var file_name   = objProducto.varNameFile;
    var file_delete = file_ruta+"/"+file_name;
    console.log(objProducto);

    Swal.fire({
      title: 'Eliminar ?',
      text: "Desea eliminar el codigo "+codigo,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      if (result.isConfirmed) {        
        $.ajax({
          type: "POST",
          url: d_url,
          data:{
            action: "peticionEliminarVariacion",
            nonce: d_non,
            id: d_id,
            file: file_delete
          },
          success:function(data){
            Swal.fire(
              'Eliminado!',
              'Se elimino correctamente!',
              'success'
            );
            location.reload();
          }
        });
      }
    });    


    
  });

  $(document).on("click", "a[data-edt_var_id]", function () {
    var var_id = this.dataset.edt_var_id;
    console.log("Id > ", var_id);

    var objProducto = s_global_array_var_match_all.find(function (producto) {
      return producto.varId == var_id;
    });

    console.log(objProducto);
    var global_price = "";
    if (objProducto.varPRegular == "") {
      global_price = objProducto.varPrecio;
    } else {
      global_price = objProducto.varPRegular;
    }

    $("#txtEdVId").val(objProducto.varId);
    $("#txtEdVProId").val(objProducto.varProId);
    $("#txtEdFileVId").val(objProducto.varDirId);
    $("#txtEdVCodigo").val(objProducto.varSKU);
    $("#txtEdVNDetalle").val(objProducto.varName);
    $("#idEdVPRegular").val(global_price);
    $("#idEdVPVenta").val(objProducto.varPVenta);

    $("#idEdVPorcentaje").val(objProducto.varPorcentaje);
    $("#idEdVTiempo").val(objProducto.varTiempo);
    $("#idEdPFinal").val(objProducto.varPFinal);

    $("#sIdEdVNameFile").val(objProducto.varDirId);
    $("#idEdVUbicacionFile").val(objProducto.varDirRuta);

    $("#mdEditarVariacion").modal("show");
  });

  $("#btnExportaVariacion").click(function () {
    console.log("Exportar");
    variacionExcelExport();
  });

  $("#btnEditDirectoryFile").click(function () {
    $("#sIdEdVNameFile").removeAttr("disabled");
    // $("#idEdVUbicacionFile").removeAttr('disabled');

    $("#btnEditDirectoryFile").css("display", "none");
    $("#btnXEditDirectoryFile").css("display", "");
  });

  $("#btnXEditDirectoryFile").click(function () {
    $("#sIdEdVNameFile").attr("disabled", "disabled");
    // $("#idEdVUbicacionFile").attr('disabled', 'disabled');

    $("#btnEditDirectoryFile").css("display", "");
    $("#btnXEditDirectoryFile").css("display", "none");
  });
});

function variacionExcelExport() {
  //console.log("Exportar Excel");
  var dt = new Date();
  var day = dt.getDate();
  var month = dt.getMonth() + 1;
  var year = dt.getFullYear();
  var hour = dt.getHours();
  var mins = dt.getMinutes();
  var sec = dt.getSeconds();

  var a = document.createElement("a");
  var sa = document.createElement("sa");

  var tab_text = "<table border='2px'>" + "<tr>";
  var textRange;
  var j = 0;
  tab = document.getElementById("tblListVariables"); // id of table

  for (j = 0; j < tab.rows.length; j++) {
    tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
  }

  tab_text = tab_text + "</table>";
  tab_text = tab_text.replace(/<A[^>]*>|<\/A>/g, ""); //remove if u want links in your table
  tab_text = tab_text.replace(/<img[^>]*>/gi, ""); // remove if u want images in your table
  tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params

  tab_text = tab_text.replace(/<th class="c-gray">Opciones<\/th>/gi, "");
  tab_text = tab_text.replace(/<!--inicio_option--><td>/g, ""); // reomves input params
  tab_text = tab_text.replace(/<\/td><!--fin_option-->/g, ""); // reomves input params

  tab_text = tab_text.replace(/á/gi, "&aacute;");
  tab_text = tab_text.replace(/é/gi, "&eacute;");
  tab_text = tab_text.replace(/í/gi, "&iacute;");
  tab_text = tab_text.replace(/ó/gi, "&oacute;");
  tab_text = tab_text.replace(/ú/gi, "&uacute;");
  tab_text = tab_text.replace(/ñ/gi, "&ntilde;");

  var ua = window.navigator.userAgent;
  var msie = ua.indexOf("MSIE ");

  if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) {
    // If Internet Explorer
    txtArea1.document.open("txt/html", "replace");
    txtArea1.document.write(tab_text);
    txtArea1.document.close();
    txtArea1.focus();
    sa = txtArea1.document.execCommand(
      "SaveAs",
      true,
      "Tabla_Variacion_" +
        day +
        "-" +
        month +
        "-" +
        year +
        "_" +
        hour +
        "-" +
        mins +
        "-" +
        sec +
        ".xls"
    );
  } else {
    //other browser not tested on IE 11
    document.body.appendChild(a);
    a.href = "data:application/vnd.ms-excel," + encodeURIComponent(tab_text);
    a.download =
      "tabla_variacion_" +
      day +
      "-" +
      month +
      "-" +
      year +
      "_" +
      hour +
      "-" +
      mins +
      "-" +
      sec +
      ".xls";
    a.click();
  }
  Swal.fire({
    icon: "success",
    title: "Descargado!",
    text: "Espere un momento por favor !",
    showConfirmButton: false,
    timer: 2000,
  });
  return sa;
}
