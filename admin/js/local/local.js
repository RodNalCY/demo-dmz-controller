jQuery(document).ready(function ($) {

  //console.log("AJX",SolicitudesAjax);
  //Swal.fire('Any fool can use a computer');

  $("#btnNuevoProducto").click(function () {
    $("#mdNuevoProducto").modal("show");
  });

  $("#btnImportar").click(function () {
    $("#mdImportar").modal("show");
  });

  $("#btnExportar").click(function () {
    $("#mdExportar").modal("show");
  });


  var contador = 1;
  $("#add").click(function () {
    contador++;   
       

      $("#formDataContainer").append('<div  id="row'+contador+'"><center> <h5>N° '+contador+'</h5> </center> <div class="row"> <div class="col"> <input type="text" name="sku[]" id="sku" class="form-control sku_list" placeholder="SKU"> </div> <div class="col"> <select name="marca[]" id="marca" class="form-control marca_list" placeholder="Marca"> <option value="" selected>Marca:</option> <option value="YOKOHAMA">YOKOHAMA</option> <option value="ROADSTONE">ROADSTONE</option> <option value="NEXEN">NEXEN</option> <option value="APOLLO">APOLLO</option> <option value="OVATION">OVATION</option> <option value="D.HAPPINESS">D.HAPPINESS</option> <option value="JINYU">JINYU</option> <option value="MAZZINI">MAZZINI</option> <option value="DOUBLE STAR">DOUBLE STAR</option> <option value="WANLI">WANLI</option> <option value="ROCKSTONE">ROCKSTONE</option> <option value="LEAO">LEAO</option> <option value="SOLIDEAL">SOLIDEAL</option> <option value="HABILEAD">HABILEAD</option> <option value="BKT">BKT</option> <option value="BEAUTRAK">BEAUTRAK</option> <option value="ORNET">ORNET</option> <option value="DOUBLECAMEL">DOUBLECAMEL</option> <option value="TECHKING">TECHKING</option> <option value="ARMOUR">ARMOUR</option> <option value="ANNAITE">ANNAITE</option> <option value="ACTIVE">ACTIVE</option> <option value="MAXION">MAXION</option> <option value="KENNO">KENNO</option> <option value="INDUSTRIAL EL SOL">INDUSTRIAL EL SOL</option> <option value="VIPAL">VIPAL</option> </select> </div> </div> <div class="row" style="margin-top: 5px;"> <div class="col"> <select name="linea[]" id="linea" class="form-control linea_list" placeholder="Linea"> <option value="" selected>Linea:</option> <option value="LLANTAS">LLANTAS</option> <option value="AROS">AROS</option> <option value="BANDAS">BANDAS</option> <option value="CAMARAS Y PROTECTORES">CAMARAS Y PROTECTORES</option> <option value="ARTICULOS PROMOCIONALES">ARTICULOS PROMOCIONALES</option> <option value="CONSUMIBLES">CONSUMIBLES</option> <option value="HERRAMIENTA,VALVULA, ACCESORIO">HERRAMIENTA,VALVULA, ACCESORIO</option> <option value="PARCHES">PARCHES</option> <option value="GOMA COJIN">GOMA COJIN</option> <option value="SERVICIOS DE REENCAUCHE">SERVICIOS DE REENCAUCHE</option> <option value="CAUCHOS">CAUCHOS</option> <option value="PLOMOS SERVICIO DE PLAYA">PLOMOS SERVICIO DE PLAYA</option> <option value="NEGROS DE HUMO">NEGROS DE HUMO</option> <option value="CARGAS REFORZANTES">CARGAS REFORZANTES</option> <option value="AUXILIARES DE PROCESO">AUXILIARES DE PROCESO</option> <option value="ACTIVADORES">ACTIVADORES</option> <option value="AGENTES DE VULCANIZACIÓN">AGENTES DE VULCANIZACIÓN</option> <option value="ANTIDEGRADANTES">ANTIDEGRADANTES</option> <option value="AGENTES DE ADHESIÓN">AGENTES DE ADHESIÓN</option> <option value="TIRAS">TIRAS</option> <option value="EJES">EJES</option> <option value="EQUIPOS">EQUIPOS</option> <option value="Servicio de Playa">Servicio de Playa</option> <option value="CARCASAS">CARCASAS</option> </select> </td> </div> <div class="col"> <select name="familia[]" id="familia" class="form-control familia_list" placeholder="Familia"> <option value="" selected>Familia:</option> <option value="AGRICOLA">AGRICOLA</option> <option value="CAMION & OMNIBUS CONVENCIONAL">CAMION & OMNIBUS CONVENCIONAL</option> <option value="CAMION & OMNIBUS RADIAL">CAMION & OMNIBUS RADIAL</option> <option value="CAMIONETA CONVENCIONAL">CAMIONETA CONVENCIONAL</option> <option value="CAMIONETA RADIAL">CAMIONETA RADIAL</option> <option value="HPT">HPT</option> <option value="INDUSTRIAL">INDUSTRIAL</option> <option value="MOTO">MOTO</option> <option value="MOTOR SPORT">MOTOR SPORT</option> <option value="OTR (FUERA DE CARRETERA)">OTR (FUERA DE CARRETERA)</option> <option value="PASAJERO CONVENCIONAL">PASAJERO CONVENCIONAL</option> <option value="PASAJERO RADIAL">PASAJERO RADIAL</option> <option value="RADIAL 4X4">RADIAL 4X4</option> </select> </div> </div> <div class="row" style="margin-top: 5px;"> <div class="col"> <input type="number" name="stock[]" id="stock" class="form-control stock_list" placeholder="Stock"> </div> <div class="col"> <input type="text" name="precio[]" id="precio" class="form-control precio_list" placeholder="Precio" onkeypress="return globalValDosDecimales(event,this);"> </div> <div class="col"> <select name="medida[]" id="medida" class="form-control medida_list" placeholder="Medida"> <option value="" selected>Medida:</option> <option value="185/65R14">185/65R14</option> <option value="185/65R15">185/65R15</option> <option value="10.00/R15">10.00/R15</option> <option value="10.00/R16">10.00/R16</option> <option value="10.00/R16.5">10.00/R16.5</option> <option value="10.00/R16.5">10.00/R16.5</option> <option value="10.00/R20">10.00/R20</option> <option value="10.00/R22.5">10.00/R22.5</option> <option value="10.00/4.5R5">10.00/4.5R5</option> <option value="10.00/75R15.3">10.00/75R15.3</option> <option value="10.5/R15">10.5/R15</option> <option value="10.5/R20">10.5/R20</option> <option value="10.5/80R18">10.5/80R18</option> <option value="10.5/80R18">10.5/80R18</option> <option value="275/50R20">275/50R20</option> <option value="275/55R19">275/55R19</option> <option value="275/60R15">275/60R15</option> <option value="275/60R20">275/60R20</option> <option value="275/70R22.5">275/70R22.5</option> <option value="28/R26">28/R26</option> <option value="28/12R22">28/12R22</option> <option value="28/8.50R15">28/8.50R15</option> <option value="285/30R19">285/30R19</option> <option value="285/55R20">285/55R20</option> <option value="285/60R18">285/60R18</option> <option value="29.5/R29">29.5/R29</option> <option value="9/R">9/R</option> </select> </div> <div class="col"> <select name="estado[]" id="estado" class="form-control estado_list" placeholder="Estado"> <option value="" selected>Estado:</option> <option value="A">A</option> <option value="B">B</option> <option value="C">C</option> </select> </div> </div> <div class="row" style="margin-top: 5px;"> <div class="col"> <input type="text" name="imagen[]" id="imagen" class="form-control imagen_list" placeholder="Imagen"> </div> </div> <div class="row" style="margin-top: 5px;"> <div class="col"> <input type="text" name="descripcion[]" id="descripcion" class="form-control descripcion_list" placeholder="Descripcion"> </div> </div> <div class="row" style="margin-top: 5px;"> <div class="col"> <button name="btn_remove" id="'+contador+'" class="btn btn-danger btn_remove" style="float: right;"> X </button> </div> </div> </div>');
      
      return false;
  });

  $(document).on("click", ".btn_remove", function () {
    var button_id = $(this).attr("id");
    console.log("Borrado", button_id);
    $("#row" + button_id).remove();
    return false;
  });

  $(document).on("click", "a[data-id]", function(){
    var n_id  = this.dataset.id;
    var n_url = SolicitudesAjax.url;
    var n_non = SolicitudesAjax.seguridad;
    //console.log("RDX >", n_id);

    Swal.fire({
      title: 'Eliminar ?',
      text: "Desea eliminar el codigo "+n_id,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      if (result.isConfirmed) {        
        $.ajax({
          type: "POST",
          url: n_url,
          data:{
            action: "peticionEliminar",
            nonce: n_non,
            id: n_id
          },
          success:function(){
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



});

  