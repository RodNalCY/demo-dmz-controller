<?php
// Query para obtener los productos de la BD
global $wpdb;

$tblProductos = "{$wpdb->prefix}dmz_productos";

// Para obtener el arreglo Y GUARDARLO
if (isset($_POST['btnGuardar'])) {
  //print_r($_POST);

  for ($i = 0; $i < count($_POST['sku']); $i++) {

    $f_sku   = $_POST['sku'][$i];
    $f_desc  = $_POST['descripcion'][$i];
    $f_stock = $_POST['stock'][$i];
    $f_img   = $_POST['imagen'][$i];
    $f_marc  = $_POST['marca'][$i];
    $f_lin   = $_POST['linea'][$i];
    $f_fam   = $_POST['familia'][$i];
    $f_state = $_POST['estado'][$i];
    $f_medid = $_POST['medida'][$i];
    $f_prec  = $_POST['precio'][$i];


    $data = [
      'IdProducto'         => $f_sku,
      'Descripcion'        => $f_desc,
      'Stock_Disponible'   => $f_stock,
      'Stock_Comprometido' => 0,
      'Stock_Transito'     => 0,
      'En_Remate'          => 0,
      'Slug'               => '',
      'Imagen'             => $f_img,
      'MarcaCodigo'        => $f_marc,
      'IdLinea'            => $f_lin,
      'IdFamilia'          => $f_fam,
      'Stock_Falla'        => 0,
      'Estado'             => $f_state,
      'Exclusion'          => 0,
      'IdMedida'           => $f_medid,
      'IdPrecio'           => $f_prec,
    ];

    $wpdb->insert($tblProductos, $data);
  }
}

// LISTAR DATOS
$query = "SELECT * FROM $tblProductos";
$list_products = $wpdb->get_results($query, ARRAY_A);

if (empty($list_products)) {
  $list_products = array();
}

?>

<div class="wrap">
  <div class="border bg-light text-dark" style="padding: 10px 20px 15px;">

    <?php
    echo '<h1 class="wp-heading-inline">' . get_admin_page_title() . '</h1>';
    ?>
    <!--<a href="<?php //echo plugin_dir_url(__FILE__).'list_productos.php'; 
                  ?>" target="_blank" class="page-title-action">Añadir</a> -->
    <a id="btnNuevoProducto" class="page-title-action">Añadir</a>
    <a id="btnImportar" class="page-title-action">Importar</a>
    <a id="btnExportar" class="page-title-action">Exportar</a>

    <!--<select name="action" id="bulk-action-selector-top" style="margin-top: -10px; font-size: 14px;">
      <option value="0">Importar</option>
      <option value="dmz">DMZ</option>
      <option value="woo">Woocomers</option>
    </select>   -->

  </div>
  <br>
  <table class="wp-list-table widefat fixed striped pages">
    <thead class="table-dark" style="background-color: #3c434a;">
      <th style="color: #FFF;">SKU</th>
      <th style="color: #FFF;">MARCA</th>
      <th style="color: #FFF;">LINEA</th>
      <th style="color: #FFF;">FAMILIA</th>
      <th style="color: #FFF;">MEDIDA</th>
      <th style="color: #FFF;">STOCK</th>
      <th style="color: #FFF;">PRECIO</th>
      <th style="color: #FFF;">DESCRIPCIÓN</th>
      <th style="color: #FFF;">FECHA</th>
      <th style="color: #FFF;">ACCIONES</th>
    </thead>
    <tbody id="the-list">
      <?php
      foreach ($list_products as $key => $value) {
        $vId      = $value['IdProducto'];
        $vDesc    = $value['Descripcion'];
        $vStock   = $value['Stock_Disponible'];
        $vImagen  = $value['Imagen'];
        $vMarca   = $value['MarcaCodigo'];
        $vLinea   = $value['IdLinea'];
        $vFamilia = $value['IdFamilia'];
        $vEstatus = $value['Estado'];
        $vMedida  = $value['IdMedida'];
        $vPrecio  = $value['IdPrecio'];
        $vFecha   = $value['created_at'];

        echo "
                   <tr>
                       <td> $vId </td>
                       <td> $vMarca </td>     
                       <td> $vLinea </td>
                       <td> $vFamilia </td>
                       <td> $vMedida </td>   
                       <td> $vStock </td> 
                       <td> $vPrecio </td>                                           
                       <td> $vDesc </td>
                       <td> $vFecha </td>
                       <td>
                       <center>                       
                       <a type='button' class='btn btn-outline-info btn-sm'><i class='fa-solid fa-eye'></i></a>
                       <a data-edt_id='$vId' class='btn btn-outline-warning btn-sm'><i class='fa-solid fa-pen'></i></a>  
                       <a data-id='$vId' class='btn btn-outline-danger btn-sm'><i class='fa-solid fa-trash'></i></a>
                       </center>
                       </td>
                    </tr>";
      }

      ?>


    </tbody>

  </table>
</div>

<!-- Modal Nuevo-->
<div class="modal fade mt-5" id="mdNuevoProducto" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Nuevo Producto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="post">
        <div class="modal-body">

          <div class="mb-3 row">
            <div class="col-sm-12" id="formDataContainer">
              <div>
                <center>
                  <h5>N° 1</h5>
                </center>
                <div class="row">
                  <div class="col">
                    <input type="text" name="sku[]" id="sku" class="form-control sku_list" placeholder="SKU">
                  </div>
                  <div class="col">
                    <select name="marca[]" id="marca" class="form-control marca_list" placeholder="Marca">
                      <option value="" selected>Marca:</option>
                      <option value="YOKOHAMA">YOKOHAMA</option>
                      <option value="ROADSTONE">ROADSTONE</option>
                      <option value="NEXEN">NEXEN</option>
                      <option value="APOLLO">APOLLO</option>
                      <option value="OVATION">OVATION</option>
                      <option value="D.HAPPINESS">D.HAPPINESS</option>
                      <option value="JINYU">JINYU</option>
                      <option value="MAZZINI">MAZZINI</option>
                      <option value="DOUBLE STAR">DOUBLE STAR</option>
                      <option value="WANLI">WANLI</option>
                      <option value="ROCKSTONE">ROCKSTONE</option>
                      <option value="LEAO">LEAO</option>
                      <option value="SOLIDEAL">SOLIDEAL</option>
                      <option value="HABILEAD">HABILEAD</option>
                      <option value="BKT">BKT</option>
                      <option value="BEAUTRAK">BEAUTRAK</option>
                      <option value="ORNET">ORNET</option>
                      <option value="DOUBLECAMEL">DOUBLECAMEL</option>
                      <option value="TECHKING">TECHKING</option>
                      <option value="ARMOUR">ARMOUR</option>
                      <option value="ANNAITE">ANNAITE</option>
                      <option value="ACTIVE">ACTIVE</option>
                      <option value="MAXION">MAXION</option>
                      <option value="KENNO">KENNO</option>
                      <option value="INDUSTRIAL EL SOL">INDUSTRIAL EL SOL</option>
                      <option value="VIPAL">VIPAL</option>
                    </select>
                  </div>
                </div>
                <div class="row" style="margin-top: 5px;">
                  <div class="col">
                    <select name="linea[]" id="linea" class="form-control linea_list" placeholder="Linea">
                      <option value="" selected>Linea:</option>
                      <option value="LLANTAS">LLANTAS</option>
                      <option value="AROS">AROS</option>
                      <option value="BANDAS">BANDAS</option>
                      <option value="CAMARAS Y PROTECTORES">CAMARAS Y PROTECTORES</option>
                      <option value="ARTICULOS PROMOCIONALES">ARTICULOS PROMOCIONALES</option>
                      <option value="CONSUMIBLES">CONSUMIBLES</option>
                      <option value="HERRAMIENTA,VALVULA, ACCESORIO">HERRAMIENTA,VALVULA, ACCESORIO</option>
                      <option value="PARCHES">PARCHES</option>
                      <option value="GOMA COJIN">GOMA COJIN</option>
                      <option value="SERVICIOS DE REENCAUCHE">SERVICIOS DE REENCAUCHE</option>
                      <option value="CAUCHOS">CAUCHOS</option>
                      <option value="PLOMOS SERVICIO DE PLAYA">PLOMOS SERVICIO DE PLAYA</option>
                      <option value="NEGROS DE HUMO">NEGROS DE HUMO</option>
                      <option value="CARGAS REFORZANTES">CARGAS REFORZANTES</option>
                      <option value="AUXILIARES DE PROCESO">AUXILIARES DE PROCESO</option>
                      <option value="ACTIVADORES">ACTIVADORES</option>
                      <option value="AGENTES DE VULCANIZACIÓN">AGENTES DE VULCANIZACIÓN</option>
                      <option value="ANTIDEGRADANTES">ANTIDEGRADANTES</option>
                      <option value="AGENTES DE ADHESIÓN">AGENTES DE ADHESIÓN</option>
                      <option value="TIRAS">TIRAS</option>
                      <option value="EJES">EJES</option>
                      <option value="EQUIPOS">EQUIPOS</option>
                      <option value="Servicio de Playa">Servicio de Playa</option>
                      <option value="CARCASAS">CARCASAS</option>
                    </select>
                    </td>
                  </div>
                  <div class="col">
                    <select name="familia[]" id="familia" class="form-control familia_list" placeholder="Familia">
                      <option value="" selected>Familia:</option>
                      <option value="AGRICOLA">AGRICOLA</option>
                      <option value="CAMION & OMNIBUS CONVENCIONAL">CAMION & OMNIBUS CONVENCIONAL</option>
                      <option value="CAMION & OMNIBUS RADIAL">CAMION & OMNIBUS RADIAL</option>
                      <option value="CAMIONETA CONVENCIONAL">CAMIONETA CONVENCIONAL</option>
                      <option value="CAMIONETA RADIAL">CAMIONETA RADIAL</option>
                      <option value="HPT">HPT</option>
                      <option value="INDUSTRIAL">INDUSTRIAL</option>
                      <option value="MOTO">MOTO</option>
                      <option value="MOTOR SPORT">MOTOR SPORT</option>
                      <option value="OTR (FUERA DE CARRETERA)">OTR (FUERA DE CARRETERA)</option>
                      <option value="PASAJERO CONVENCIONAL">PASAJERO CONVENCIONAL</option>
                      <option value="PASAJERO RADIAL">PASAJERO RADIAL</option>
                      <option value="RADIAL 4X4">RADIAL 4X4</option>
                    </select>
                  </div>
                </div>
                <div class="row" style="margin-top: 5px;">
                  <div class="col">
                    <input type="number" name="stock[]" id="stock" class="form-control stock_list" placeholder="Stock">
                  </div>
                  <div class="col">
                    <input type="text" name="precio[]" id="precio" class="form-control precio_list"  onkeypress="return globalValDosDecimales(event,this);" placeholder="Precio">
                  </div>
                  <div class="col">
                    <select name="medida[]" id="medida" class="form-control medida_list" placeholder="Medida">
                      <option value="" selected>Medida:</option>
                      <option value="185/65R14">185/65R14</option>
                      <option value="185/65R15">185/65R15</option>
                      <option value="10.00/R15">10.00/R15</option>
                      <option value="10.00/R16">10.00/R16</option>
                      <option value="10.00/R16.5">10.00/R16.5</option>
                      <option value="10.00/R16.5">10.00/R16.5</option>
                      <option value="10.00/R20">10.00/R20</option>
                      <option value="10.00/R22.5">10.00/R22.5</option>
                      <option value="10.00/4.5R5">10.00/4.5R5</option>
                      <option value="10.00/75R15.3">10.00/75R15.3</option>
                      <option value="10.5/R15">10.5/R15</option>
                      <option value="10.5/R20">10.5/R20</option>
                      <option value="10.5/80R18">10.5/80R18</option>
                      <option value="10.5/80R18">10.5/80R18</option>
                      <option value="275/50R20">275/50R20</option>
                      <option value="275/55R19">275/55R19</option>
                      <option value="275/60R15">275/60R15</option>
                      <option value="275/60R20">275/60R20</option>
                      <option value="275/70R22.5">275/70R22.5</option>
                      <option value="28/R26">28/R26</option>
                      <option value="28/12R22">28/12R22</option>
                      <option value="28/8.50R15">28/8.50R15</option>
                      <option value="285/30R19">285/30R19</option>
                      <option value="285/55R20">285/55R20</option>
                      <option value="285/60R18">285/60R18</option>
                      <option value="29.5/R29">29.5/R29</option>
                      <option value="9/R">9/R</option>
                    </select>
                  </div>
                  <div class="col">
                    <select name="estado[]" id="estado" class="form-control estado_list" placeholder="Estado">
                      <option value="" selected>Estado:</option>
                      <option value="A">A</option>
                      <option value="B">B</option>
                      <option value="C">C</option>
                    </select>
                  </div>
                </div>
                <div class="row" style="margin-top: 5px;">
                  <div class="col">
                    <input type="text" name="imagen[]" id="imagen" class="form-control imagen_list" placeholder="Imagen">
                  </div>
                </div>
                <div class="row" style="margin-top: 5px;">
                  <div class="col">
                    <input type="text" name="descripcion[]" id="descripcion" class="form-control descripcion_list" placeholder="Descripcion">
                  </div>
                </div>
                <div class="row" style="margin-top: 5px;">
                  <div class="col">
                    <button name="add" id="add" class="btn btn-success" style="float: right;">Añadir +</button>
                  </div>
                </div>
              </div>
              <!--<td>
                    <select name="precio[]" id="precio" class="form-control precio_list" style="margin-left: 10px;">
                      <option value="100" selected>100</option>
                      <option value="200">200</option>
                      <option value="300">300</option>
                      <option value="400">400</option>
                      <option value="500">500</option>
                    </select>
                  </td>-->
            </div>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary" name="btnGuardar" id="btnGuardar">Guardar</button>
        </div>
      </form>
    </div>
  </div>
  <br>
  <br>
</div>

<!---MODAL IMPORTAR---->
<div class="modal fade mt-5" id="mdImportar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Importar</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-4 col-lg-4">
            <div class="card card-body bg-success" style="padding: 8px !important;">
              <h1 class="font-light text-white text-center"><?php echo "0"; ?></h1>
              <p class="text-white text-center">Productos en la DMZ segun selección.</p>
            </div>
          </div>

          <div class="col-md-4 col-lg-4">
            <div class="card card-body bg-warning" style="padding: 8px !important;">
              <h1 class="font-light text-white text-center"><?php echo "0"; ?></h1>
              <p class="text-white text-center">Productos subidos en la tienda.</p>
            </div>
          </div>

          <div class="col-md-4 col-lg-4">
            <div class="card card-body bg-danger" style="padding: 8px !important;">
              <h1 class="font-light text-white text-center"><?php echo "0"; ?></h1>
              <p class="text-white text-center">Productos que faltan subir.</p>
            </div>
          </div>
        </div>

        <div class="row mt-2">
          <div class="col-md-12">
            <div class="d-grid gap-2">
              <button class="btn btn-secondary" type="button">CARGAR PRODUCTOS</button>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <br><br>
      </div>
    </div>
  </div>
</div>


<!---MODAL EXPORTAR---->
<div class="modal fade mt-5" id="mdExportar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Exportar</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">

        <div class="d-grid gap-2 col-6 mx-auto" role="group">
          <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            Exportar
          </button>
          <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
            <li><a class="dropdown-item" href="#">Excel (.xls)</a></li>
            <li><a class="dropdown-item" href="#">CSV (.csv)</a></li>
          </ul>
        </div>

      </div>
      <div class="modal-footer">
        <br><br>
      </div>
    </div>
  </div>
</div>