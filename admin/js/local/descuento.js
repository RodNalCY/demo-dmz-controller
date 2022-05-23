jQuery(document).ready(function ($) {
  $("#btnNuevoDescuento").click(function () {
    $("#mdNuevoDescuento").modal("show");
  });

  $("#tblListDescuento").DataTable({
    order: [[0, "desc"]],
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

  $(document).on("click", "a[data-ed_dsc_id]", function () {
    var pro_id = this.dataset.ed_dsc_id;

    var objProducto = s_global_array.find(function (producto) {
      return producto.aId == pro_id;
    });

    console.log(objProducto);

    $("#txtId").val(objProducto.aId);
    $("#txtEdCodigo").val(objProducto.aSKU);
    $("#txtEdDetalle").val(objProducto.aName);
    $("#idEdPRegular").val(objProducto.aRegPrice);
    $("#idEdPDescuento").val(objProducto.aSalPrice);

    if (objProducto.aFeIncio != null) {
      var dInicio = new Date(objProducto.aFeIncio);
      var Iyear = dInicio.getFullYear(); // 2019
      var Imonth = dInicio.getMonth() + 1; // 9
      var Idate = dInicio.getDate(); // 23
      var DateInicio = `${Iyear}-${Imonth < 10 ? 0 : ""}${Imonth}-${
        Idate < 10 ? 0 : ""
      }${Idate}`;

      $("#idEdFInicio").val(DateInicio.toString());
    } else {
      $("#idEdFInicio").val("");
    }

    if (objProducto.aFeFinal != null) {
      var dFinal = new Date(objProducto.aFeFinal);
      var Fyear = dFinal.getFullYear(); // 2019
      var Fmonth = dFinal.getMonth() + 1; // 9
      var Fdate = dFinal.getDate(); // 23
      var DateFinal = `${Fyear}-${Fmonth < 10 ? 0 : ""}${Fmonth}-${
        Fdate < 10 ? 0 : ""
      }${Fdate}`;

      $("#idEdFFin").val(DateFinal.toString());
    } else {
      $("#idEdFFin").val("");
    }

    $("#mdEditarDescuento").modal("show");
  });

  $("#btnExportaDescuento").click(function () {
    console.log("Exportar");
    descuentoExcelExport();
  });


});


function descuentoExcelExport() {
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
  tab = document.getElementById("tblListDescuento"); // id of table

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
      "Tabla_Descuento_" +
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
      "tabla_descuento_" +
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
