jQuery(document).ready(function ($) {
  $("#tblListUpdateAPI").DataTable({
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
      {
        responsivePriority: 1,
        targets: 0,
      },
      {
        responsivePriority: 2,
        targets: -1,
      },
    ],
  });

  $("#idAPIUpdate").click(function () {
    console.log("EDITAR");
    $("#id_Dominio").val("http://"+location.hostname);
    $("#mdEditarAPI").modal("show");

  });


  $("#idAPICreate").click(function () {
    console.log("Crear");
    $("#id_CreDominio").val("http://"+location.hostname);
    $('#id_CreDominio').css('border-color', '#0d96fd');
    $('#id_CreDominio').css('box-shadow', '0 0 0 0.2rem rgb(13 110 253 / 45%)');
    $("#mdCrearAPI").modal("show");

  });
  

  $("#btnTutorial").click(function () {
    $("#ImgContenedorDemo").css("display", "");
    $("#myVideo").get(0).play();
  });
  $("#btnClosed").click(function () {
    $("#ImgContenedorDemo").css("display", "none");
    $("#myVideo").get(0).pause();
  });
});
