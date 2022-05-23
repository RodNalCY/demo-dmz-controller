<?php

include_once("api/credentials.php");
include_once("api/cors.php");

// GUARDAR UNO NUEVO
if (isset($_POST['btnGuardarCupon'])) {
  //print_r($_POST);
  $c_cupon   = $_POST['cupon'];
  $c_descr   = $_POST['descripcion'];
  $c_type    = $_POST['tipo'];
  $c_mont    = $_POST['monto'];
  $c_fExp    = $_POST['fexpire'];
  $c_swFr    = $_POST['SWEnvioFree'];

  $c_gMin    = $_POST['idGastonMin'];
  $c_gMax    = $_POST['idGastonMax'];
  $c_swInd   = $_POST['idSWUsoInd'];
  $c_swExOf  = $_POST['idSWExcluirOfertas'];
  $c_limUso  = $_POST['idLimiteUso'];
  $c_limUser = $_POST['idLimiteUsuario'];

  $v_envioFree = false;
  $v_usoIndiv  = false;
  $v_exArticul = false;

  if (isset($c_swFr) && $c_swFr == "on") {
    $v_envioFree = true;
  }
  if (isset($c_swInd) && $c_swInd == "on") {
    $v_usoIndiv = true;
  }
  if (isset($c_swExOf) && $c_swExOf == "on") {
    $v_exArticul = true;
  }

  $data = [
    'code'                 => $c_cupon,
    'description'          => $c_descr,
    'discount_type'        => $c_type,
    'amount'               => $c_mont,
    'date_expires'         => $c_fExp,
    'free_shipping'        => $v_envioFree,
    'minimum_amount'       => $c_gMin,
    'maximum_amount'       => $c_gMax,
    'individual_use'       => $v_usoIndiv,
    'exclude_sale_items'   => $v_exArticul,
    'usage_limit'          => $c_limUso,
    'usage_limit_per_user' => $c_limUser,
  ];

  $woocommerce->post('coupons', $data);
}

// ACTUALIZAR DATA CUPON
if (isset($_POST['btnUpdateCupon'])) {
  //print_r($_POST);
  $a_Id    = $_POST['edTXTIdCupon'];
  $a_cupon = $_POST['edTxTCodCupon'];
  $a_descr = $_POST['edTxTDescripcion'];
  $a_type  = $_POST['edSlTipoDescuento'];
  $a_mont  = $_POST['edTxTMontoCupon'];
  $a_fExp  = $_POST['edTxTFExpire'];
  $a_swFr  = $_POST['fdIdSWEnvioFree'];

  $a_envioFree = false;
  if (isset($a_swFr) && $a_swFr == "on") {
    $a_envioFree = true;
  }

  $data = [
    'code'          => $a_cupon,
    'description'   => $a_descr,
    'discount_type' => $a_type,
    'amount'        => $a_mont,
    'date_expires'  => $a_fExp,
    'free_shipping' => $a_envioFree,
  ];

  $woocommerce->put('coupons/' . $a_Id, $data);
}

//LISTAR CUPONES
$enCupons = json_encode($woocommerce->get('coupons'));
$lsCupons  = json_decode($enCupons);
//var_dump($lsCupons);
?>

<script>
  var s_global_array = [];
  jQuery(document).ready(function($) {
    s_global_array = <?php echo json_encode($lsCupons); ?>;
    console.log("CUPONES > ", s_global_array);
  });
</script>


<div class="wrap">
  <div class="border bg-light text-dark" style="padding: 10px 20px 15px;">
    <?php
    echo '<h1 class="wp-heading-inline">' . get_admin_page_title() . '</h1>';
    ?>
    <a id="btnNuevoCupon" class="page-title-action">Añadir</a>
    <a id="" class="page-title-action">Importar</a>
    <a id="btnExportaCupon" class="page-title-action">Exportar</a>
  </div>

  <br>
  <table class="wp-list-table widefat fixed striped pages" id="tblListCupons">
    <thead class="table-dark" style="background-color: #3c434a;">
      <th style="color: #FFF;" data-priority="1">ID</th>
      <th style="color: #FFF;">CODIGO</th>
      <th style="color: #FFF;">TIPO</th>
      <th style="color: #FFF;">CANTIDAD</th>
      <th style="color: #FFF;">DESCRIPCIÓN</th>
      <th style="color: #FFF;">Limite de Uso</th>
      <th style="color: #FFF;">F. Expira</th>
      <th style="color: #FFF; text-align: center;">ACCIONES</th>
    </thead>
    <tbody id="the-list">
      <?php
      if (count($lsCupons) > 0) {
        foreach ($lsCupons as $key => $value) {
          $vId       = $value->id;
          $vCodigo   = $value->code;
          $vType     = $value->discount_type;
          $vCantidad = $value->amount;
          $vDescript = $value->description;
          $vUsoLimit = $value->usage_limit;
          $vFExpire  = $value->date_expires;
          $vHLink    = $value->permalink;

          $strLimite = "∞";
          $strType   = "-";
          $strExpire = "-";

          if (!empty($vUsoLimit)) {
            $strLimite = $value->usage_limit;
          }

          if ($vType == "percent") {
            $strType = "Porcentual (%)";
          } else if ($vType == "fixed_cart") {
            $strType = "Carro Fijo";
          } else {
            $strType = "Producto Fijo";
          }

          if ($vFExpire != null) {
            $strExpire =  date('d-m-Y', strtotime($value->date_expires));
          }

          echo "
                   <tr>
                       <td> $vId </td>
                       <td> $vCodigo </td>
                       <td> $strType </td>     
                       <td> $vCantidad </td>
                       <td> $vDescript </td>
                       <td> 0 /  $strLimite </td>
                       <td> $strExpire </td> 
                       <td>
                       <center>
                            <a data-ver_id='$vId' class='btn btn-outline-info btn-sm'><i class='fa-solid fa-eye'></i></a>
                            <a data-edt_id='$vId' class='btn btn-outline-warning btn-sm'><i class='fa-solid fa-pen'></i></a>  
                            <a data-del_id='$vId' class='btn btn-outline-danger btn-sm'><i class='fa-solid fa-trash'></i></a> 
                       </center>
                       </td>
                    </tr>";
        }
      }else{
        echo "<tr><td colspan='8'><center> No existen datos. </center></td></tr>";
      }
      ?>
    </tbody>

  </table>
  <br>
  <br>
  <!---MODAL NUEVO CUPON---->
  <div class="modal fade mt-5" id="mdNuevoCupon" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Nuevo Cupon</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST">
          <div class="modal-body">


            <div class="row border rounded" style="margin-left: 5px; margin-right: 5px;">
              <div class="mb-3">
                <div class="row">
                  <div class="col-md-8">
                    <label for="txtCupon" class="form-label text-uppercase" style="font-weight: bold; color: #50575e;">Cupon</label>
                    <input type="text" class="form-control" id="idCupon" name="cupon">
                  </div>
                  <div class="col-md-4">
                    <button type="button" class="btn btn-outline-danger btn-sm form-control" id="btnGenerarCode" style="float: right; margin-top: 33px;">Generar Code</button>
                  </div>
                </div>
                <div id="txtcupon" class="form-text">Para generar un codigo utiliza el boton o escribelo.</div>
              </div>
              <div class="mb-3">
                <label for="txtDescripcion" class="form-label text-uppercase" style="font-weight: bold; color: #50575e;">Descripción</label>
                <input type="text" class="form-control" id="txtDescripcion" name="descripcion">
              </div>
            </div>

            <div class="row border rounded mt-3" style="margin: 5px;">

              <div class="mb-3 row">
                <label for="txtDescuento" class="col-md-4 col-form-label mt-3 text-uppercase" style="font-weight: bold; color: #50575e;">Tipo de Decuento</label>
                <div class="col-md-8 mt-3">
                  <select id="txtTipoDescuento" class="form-control" style="max-width: 100% !important;" name="tipo">
                    <option value="" selected>Seleccione:</option>
                    <option value="percent">Porcentual (%)</option>
                    <option value="fixed_cart">Descuento x Carro Fijo</option>
                    <option value="fixed_product">Descuento x Producto Fijo</option>
                  </select>
                </div>
              </div>

              <div class="mb-3 row">
                <label for="txtMontoCupon" class="col-md-4 col-form-label text-uppercase" style="font-weight: bold; color: #50575e;">Monto del Cupon</label>
                <div class="col-md-8">
                  <input type="text" class="form-control" id="txtMontoCupon" name="monto" onkeypress="return globalValDosDecimales(event,this);">
                </div>
              </div>

              <div class="mb-3 row">
                <label for="txtFExpire" class="col-md-4 col-form-label text-uppercase" style="font-weight: bold; color: #50575e;">Fecha de Expiración</label>
                <div class="col-md-8">
                  <input type="date" class="form-control" id="txtFExpire" name="fexpire">
                </div>
              </div>

              <div class="mb-3 row">
                <div class="custom-control custom-switch">
                  <input type="checkbox" class="custom-control-input" id="idSWEnvioFree" name="SWEnvioFree">
                  <label class="custom-control-label font-weight-light" for="idSWEnvioFree" style="display: contents !important; color: #50575e;">Marque esta casilla si el cupón otorga envío gratuito. Debe habilitarse un método de envío gratuito en su zona de envío y configurarse para que requiera "un cupón de envío gratuito válido" (consulte la configuración "Requiere envío gratuito").</label>
                </div>
              </div>
            </div>

            <div class="row border rounded mt-3" style="margin: 5px;">
              <div class="mb-3">
                <div class="row">
                  <div class="col-md-6 mt-3">
                    <label for="fGMin" class="form-label text-uppercase" style="font-weight: bold; color: #50575e;">Gasto Minimo</label>
                    <input type="text" class="form-control" id="idGastonMin" name="idGastonMin">
                  </div>
                  <div class="col-md-6 mt-3">
                    <label for="fGMax" class="form-label text-uppercase" style="font-weight: bold; color: #50575e;">Gasto Maximo</label>
                    <input type="text" class="form-control" id="idGastonMax" name="idGastonMax">
                  </div>
                </div>
              </div>

              <div class="mb-3 mt-3">
                <div class="row">
                  <div class="col-md-6">
                    <label for="fUsoInd" class="form-label text-uppercase" style="font-weight: bold; color: #50575e;">Uso Individual</label>
                    <div class="custom-control custom-switch">
                      <input type="checkbox" class="custom-control-input" id="idSWUsoInd" name="idSWUsoInd">
                      <label class="custom-control-label font-weight-light" for="idSWUsoInd" style="display: contents !important; color: #50575e;">Marque esta casilla si el cupón no se puede utilizar junto con otros cupones.</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <label for="fExOfertas" class="form-label text-uppercase" style="font-weight: bold; color: #50575e;">Excluir articulos de Ofertas</label>
                    <div class="custom-control custom-switch">
                      <input type="checkbox" class="custom-control-input" id="idSWExcluirOfertas" name="idSWExcluirOfertas">
                      <label class="custom-control-label font-weight-light" for="fExcluirOfertas" style="display: contents !important; color: #50575e;">Marque esta casilla si el cupón no debe aplicarse a artículos en oferta. Los cupones por artículo solo funcionarán si el artículo no está en oferta. Los cupones por carrito solo funcionarán si hay artículos en el carrito que no están en oferta.</label>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="row border rounded mt-3" style="margin: 5px;">
              <div class="mb-3">
                <div class="row">
                  <div class="col-md-6 mt-3">
                    <label for="fLUso" class="form-label text-uppercase" style="font-weight: bold; color: #50575e;">Límite de uso x cupón</label>
                    <input type="text" class="form-control" id="idLimiteUso" name="idLimiteUso">
                  </div>
                  <div class="col-md-6 mt-3">
                    <label for="fLUsr" class="form-label text-uppercase" style="font-weight: bold; color: #50575e;">Límite de uso por usuario</label>
                    <input type="text" class="form-control" id="idLimiteUsuario" name="idLimiteUsuario">
                  </div>
                </div>
              </div>
            </div>


          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn btn-primary" name="btnGuardarCupon" id="btnGuardarCupon">Guardar</button>
          </div>
        </form>
      </div>
      <br><br>
    </div>

  </div>

  <!---MODAL EDITAR CUPON---->
  <div class="modal fade mt-5" id="mdEditarCupon" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Editar Cupon</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST">
          <div class="modal-body">

            <input type='hidden' name="edTXTIdCupon" id="edTXTIdCupon" />
            <div class="row border rounded" style="margin-left: 5px; margin-right: 5px;">
              <div class="mb-3">
                <div class="row">
                  <div class="col-md-8">
                    <label for="feCupon" class="form-label">Cupon</label>
                    <input type="text" class="form-control" id="edTxTCodCupon" name="edTxTCodCupon">
                  </div>
                  <div class="col-md-4">
                    <button type="button" class="btn btn-outline-danger btn-sm form-control" id="btnEditGenerarCode" style="float: right; margin-top: 33px;">Cambiar Code</button>
                  </div>
                </div>
                <div id="" class="form-text">Para generar un codigo utiliza el boton o escribelo.</div>
              </div>
              <div class="mb-3">
                <label for="fdDescripcion" class="form-label">Descripción</label>
                <input type="text" class="form-control" id="edTxTDescripcion" name="edTxTDescripcion">
              </div>
            </div>

            <div class="row border rounded mt-3" style="margin: 5px;">

              <div class="mb-3 row">
                <label for="fdDescuento" class="col-md-4 col-form-label mt-3">Tipo de Decuento</label>
                <div class="col-md-8 mt-3">
                  <select id="edSlTipoDescuento" class="form-control" style="max-width: 100% !important;" name="edSlTipoDescuento">
                    <option value="" selected>Seleccione:</option>
                    <option value="percent">Porcentual (%)</option>
                    <option value="fixed_cart">Descuento x Carro Fijo</option>
                    <option value="fixed_product">Descuento x Producto Fijo</option>
                  </select>
                </div>
              </div>

              <div class="mb-3 row">
                <label for="fdMontoCupon" class="col-md-4 col-form-label">Monto del Cupon</label>
                <div class="col-md-8">
                  <input type="text" class="form-control" id="edTxTMontoCupon" name="edTxTMontoCupon" onkeypress="return globalValDosDecimales(event,this);">
                </div>
              </div>

              <div class="mb-3 row">
                <label for="fdExpire" class="col-md-4 col-form-label">Fecha de Expiración</label>
                <div class="col-md-8">
                  <input type="date" class="form-control" id="edTxTFExpire" name="edTxTFExpire">
                </div>
              </div>

              <div class="mb-3 row">
                <div class="custom-control custom-switch">
                  <input type="checkbox" class="custom-control-input fdIdSWEnvioFree" id="fdIdSWEnvioFree" name="fdIdSWEnvioFree">
                  <label class="custom-control-label font-weight-light" for="idSWEnvioFree" style="display: contents !important;">Marque esta casilla si el cupón otorga envío gratuito. Debe habilitarse un método de envío gratuito en su zona de envío y configurarse para que requiera "un cupón de envío gratuito válido" (consulte la configuración "Requiere envío gratuito").</label>
                </div>
              </div>



            </div>


          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn btn-primary" name="btnUpdateCupon" id="btnUpdateCupon">Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>



</div>


