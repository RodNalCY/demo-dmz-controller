jQuery(document).ready(function ($) {
  $("#btnNuevoCupon").click(function () {
    $("#mdNuevoCupon").modal("show");
  });

  $("#btnGenerarCode").click(function () {
    var codex = generateCodeRand(7);
    $("#idCupon").val(codex);
  });

  $("#btnEditGenerarCode").click(function () {
    var codex = generateCodeRand(7);
    $("#edTxTCodCupon").val(codex);
  });

  $("#tblListCupons").DataTable({
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

  $(document).on("click", "a[data-edt_id]", function () {
    var cupon_id = this.dataset.edt_id;
    var objCupon = s_global_array.find(function (cupon) {
      return cupon.id == cupon_id;
    });

    console.log(objCupon);

    var d = new Date(objCupon.date_expires);
    var year = d.getFullYear(); // 2019
    var month = d.getMonth() + 1; // 2019
    var date = d.getDate(); // 23
    var shortDate = `${year}-${month < 10 ? 0 : ""}${month}-${
      date < 10 ? 0 : ""
    }${date}`;

    $("#edTXTIdCupon").val(objCupon.id);
    $("#edTxTCodCupon").val(objCupon.code);
    $("#edTxTDescripcion").val(objCupon.description);
    $("#edTxTMontoCupon").val(objCupon.amount);
    $("#edTxTFExpire").val(shortDate.toString());
    $("#edSlTipoDescuento").val(objCupon.discount_type);

    if (objCupon.free_shipping) {
      $(".fdIdSWEnvioFree").prop("checked", true);
    } else {
      $(".fdIdSWEnvioFree").prop("checked", false);
    }

    $("#mdEditarCupon").modal("show");
  });

  $(document).on("click", "a[data-del_id]", function () {
    var d_id = this.dataset.del_id;
    var d_url = SolicitudesAjax.url;
    var d_non = SolicitudesAjax.seguridad;

    Swal.fire({
      title: "Eliminar ?",
      html: "Desea eliminar el ID: <strong>" + d_id + "</strong>",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, delete it!",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          type: "POST",
          url: d_url,
          data: {
            action: "peticionEliminarCupon",
            nonce: d_non,
            id: d_id,
          },
          success: function (data) {
            console.log("RDX>", data);
            Swal.fire("Eliminado!", "Se elimino correctamente!", "success");
            location.reload();
          },
          error: function (data) {
            console.log("Error", data);
          },
        });
      }
    });
  });

  $("#btnExportaCupon").click(function () {
    console.log("Exportar");
    cuponExcelExport();
  });
});

function cuponExcelExport() {
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
  tab = document.getElementById("tblListCupons"); // id of table

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
      "Tabla_Cupones_" +
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
      "tabla_cupones_" +
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


function generateCodeRand(length, type) {
  switch (type) {
    case "num":
      characters = "0123456789";
      break;
    case "alf":
      characters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
      break;
    case "rand":
      //FOR ↓
      break;
    default:
      characters =
        "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
      break;
  }
  var code = "";
  for (i = 0; i < length; i++) {
    if (type == "rand") {
      code += String.fromCharCode((Math.floor(Math.random() * 100) % 94) + 33);
    } else {
      code += characters.charAt(Math.floor(Math.random() * characters.length));
    }
  }
  return code;
}
