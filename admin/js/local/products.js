jQuery(document).ready(function ($) {
  $("#tblListProductos").DataTable({
    order: [[0, "desc"]],
    language: {
      url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json",
    },
    lengthMenu: [
      [25, 50, 100, -1],
      [25, 50, 100, "Todos"],
    ],
    responsive: true,
  });

  var popoverStar = new bootstrap.Popover(
    document.querySelector(".star-popover"),
    {
      container: "body",
    }
  );

  var popoverVariation = new bootstrap.Popover(
    document.querySelector(".buffer-variation"),
    {
      container: "body",
    }
  );

  // var popoverVariationPrice = new bootstrap.Popover(
  //   document.querySelector(".clock-variation-price"),
  //   {
  //     container: "body",
  //   }
  // );

  $("#idPDestacado").change(function () {
    var value = this.checked;
    //console.log("> ", value);
    if (value) {
      $("#fa-nwIdDestacado").css("color", "rgb(241, 196, 15)");
    } else {
      $("#fa-nwIdDestacado").css("color", "#50575e");
    }
  });

  $("#idEdtPDestacado").change(function () {
    var value = this.checked;
    //console.log("> ", value);
    if (value) {
      $("#fa-EdDestacado").css("color", "rgb(241, 196, 15)");
    } else {
      $("#fa-EdDestacado").css("color", "#50575e");
    }
  });

  $("#btnNuevoProducto").click(function () {
    console.log("Modal Nuevo");
    $("#mdNuevoProducto").modal("show");
  });

  $("#btnExportaProducto").click(function () {
    console.log("Exportar");
    ProductsExcelExport();
  });

  $(document).on("click", "a[data-del_prod_id]", function () {
    var d_id = this.dataset.del_prod_id;
    var d_url = SolicitudesAjax.url;
    var d_non = SolicitudesAjax.seguridad;
    console.log("Eliminar");
    console.log(d_id);
    console.log(d_url);
    console.log(d_non);

    Swal.fire({
      title: "Eliminar ?",
      html: "Desea eliminar el producto!",
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
            action: "peticionEliminarProducto",
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

  $(document).on("click", "a[data-edt_prod_id]", function () {
    var prod_id = this.dataset.edt_prod_id;
    var objProducto = s_global_array.find(function (producto) {
      return producto.id == prod_id;
    });

    console.log(objProducto);
    $("#editImgContenido").css("display", "");

    var etqHTMLDcorta = stripHtml(objProducto.short_description);
    var etqHTMLDlarga = stripHtml(objProducto.description);

    $("#idProdId").val(objProducto.id);
    $("#idEdtPSKU").val(objProducto.sku);
    $("#idEdtPNombre").val(objProducto.name);
    $("#idEdtPDescCorta").val(etqHTMLDcorta);
    $("#idEdtPDescLarga").val(etqHTMLDlarga);

    $("#idEdtStock").val(objProducto.stock_quantity);
    $("#idEdtPriceRegular").val(objProducto.regular_price);
    $("#idEdtPriceVenta").val(objProducto.sale_price);
    $("#idEdtCategorias").val(objProducto.categories[0].id);
    $("#idEdShowImage").attr("src", objProducto.images[0].src);

    $("#idShowImageLink").val(objProducto.images[0].src);

    if (objProducto.featured) {
      $("#idEdtPDestacado").prop("checked", true);
      $("#fa-EdDestacado").css("color", "rgb(241, 196, 15)");
    } else {
      $("#idEdtPDestacado").prop("checked", false);
      $("#fa-EdDestacado").css("color", "#50575e");
    }

    ///////////////////////////////////////////
    $("#mdEditarProducto").modal("show");
  });
});

function previsualizarNew(event) {
  addImageNew(event);
}
function addImageNew(e) {
  var file = e.target.files[0],
    imageType = /image.*/;

  if (!file.type.match(imageType)) return;

  var reader = new FileReader();
  reader.onload = fileOnloadNew;
  reader.readAsDataURL(file);
}
function fileOnloadNew(e) {
  var result = e.target.result;
  jQuery(document).ready(function ($) {
    $("#idNwShowImage").attr("src", result);
    $("#newImgContenido").css("display", "");
  });
}

function previsualizar_edit(event) {
  addImageEdit(event);
}
function addImageEdit(e) {
  var file = e.target.files[0],
    imageType = /image.*/;

  if (!file.type.match(imageType)) return;

  var reader = new FileReader();
  reader.onload = fileOnloadEdit;
  reader.readAsDataURL(file);
}
function fileOnloadEdit(e) {
  var result = e.target.result;
  jQuery(document).ready(function ($) {
    $("#idEdShowImage").attr("src", result);
    $("#editImgContenido").css("display", "");
  });
}

function stripHtml(html) {
  // Crea un nuevo elemento div
  var temporalDivElement = document.createElement("div");
  // Establecer el contenido HTML con el dado
  temporalDivElement.innerHTML = html;
  // Recuperar la propiedad de texto del elemento (compatibilidad con varios navegadores)
  return temporalDivElement.textContent || temporalDivElement.innerText || "";
}

function ProductsExcelExport() {
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
  tab = document.getElementById("tblListProductos"); // id of table

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
      "Tabla_Productos_" +
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
      "tabla_productos_" +
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

/////////////////////////////////////////////////////

// -------------------------------------------------------------------
// var selectMultiSelects;
// jQuery(document).ready(function ($) {
//   selectMultiSelects = $("#edit-states1-id, select[multiple='multiple']");
  
//   $("#edit-states1-id").change(function () {
//     console.log("Tracing change event on SELECT: #edit-states1-id changed");
//   });
//   install();
// });
// window.cssPatch = null;

// function install() {
//   selectMultiSelects.bsMultiSelect({
//     cssPatch: window.cssPatch,
//     setSelected: function (o, v) {
//       o.selected = v;
//     },
//   });
// }
// install();
// -------------------------------------------------------------------
/////////////////////////////////////////////////////////
