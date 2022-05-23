<?php

include_once("api/credentials.php");
include_once("api/cors.php");


// AÑADIR NUEVO DESCUENTO
if (isset($_POST['btnGuardarDescuento'])) {
    //print_r($_POST);
    $d_id         = $_POST["txtCodigo"];
    $d_detalle    = $_POST["txtNDetalle"];
    $d_pRegular   = $_POST["idPRegular"];
    $d_pDescuento = $_POST["idPDescuento"];
    $d_fInicio    = $_POST["idFInicio"];
    $d_fFin       = $_POST["idFFin"];

    $data = [
        'price'             => $d_pRegular,
        'regular_price'     => $d_pRegular,
        'sale_price'        => $d_pDescuento,
        'date_on_sale_from' => $d_fInicio,
        'date_on_sale_to'   => $d_fFin
    ];

    print_r($woocommerce->put('products/' . $d_id, $data));
}
// EDITAR DESCUENTO
if (isset($_POST['btnActualizarDescuento'])) {
    //print_r($_POST);
    $ed_Id         = $_POST["txtId"];
    $ed_pRegular   = $_POST["idEdPRegular"];
    $ed_pDescuento = $_POST["idEdPDescuento"];
    $ed_FInicio    = $_POST["idEdFInicio"];
    $ed_FFin       = $_POST["idEdFFin"];

    $data = [
        'price'             => $ed_pRegular,
        'regular_price'     => $ed_pRegular,
        'sale_price'        => $ed_pDescuento,
        'date_on_sale_from' => $ed_FInicio,
        'date_on_sale_to'   => $ed_FFin
    ];

    $woocommerce->put('products/' . $ed_Id, $data);
}
// LISTAR PRODUCTOS PARA LOS DESCUENTOS
$enProducts = json_encode($woocommerce->get('products'));
$lsProducts = json_decode($enProducts);
//var_dump($lsProducts);
$array = [];

foreach ($lsProducts as $key => $val) {
    $sPriceSal    = "";
    $sPriceReg = $val->price;

    if ($val->sale_price != null) {
        $sPriceSal    = $val->sale_price;
    }

    if ($val->regular_price != null) {
        $sPriceReg = $val->regular_price;
    }

    $array[] = array(
        "aId"       => $val->id,
        "aSKU"      => $val->sku,
        "aName"     => $val->name,
        "aRegPrice" => $sPriceReg,
        "aSalPrice" => $sPriceSal,
        "aFeIncio" => $val->date_on_sale_from,
        "aFeFinal" => $val->date_on_sale_to,
    );
}

?>

<script>
    var s_global_array = [];

    jQuery(document).ready(function($) {

        s_global_array = <?php echo json_encode($array); ?>;
        console.log(s_global_array);

        $('#txtCodigo').on('change', function() {
            s_global_array.forEach(element => {
                if (this.value == element['aId']) {
                    //console.log("RDX > " + this.value + " | " + element['aId']);
                    $("#txtNDetalle").val(element['aName']);
                    $("#idPRegular").val(element['aRegPrice']);
                    $("#idPDescuento").val(element['aSalPrice']);
                }
            });

            if (this.value == "") {
                $("#txtNDetalle").val("");
                $("#idPRegular").val("");
                $("#idPDescuento").val("");
            }

        });

    });
</script>
<div class="wrap">
    <div class="border bg-light text-dark" style="padding: 10px 20px 15px;">
        <?php
        echo '<h1 class="wp-heading-inline">' . get_admin_page_title() . '</h1>';
        ?>
        <a id="btnNuevoDescuento" class="page-title-action">Añadir</a>
        <a id="" class="page-title-action">Importar</a>
        <a id="btnExportaDescuento" class="page-title-action">Exportar</a>
    </div>
    <br>
    <table class="wp-list-table widefat fixed striped pages" id="tblListDescuento">
        <thead class="table-dark" style="background-color: #3c434a;">
            <tr>
                <th rowspan="2" style="color: #FFF;" data-priority="1">SKU</th>
                <th rowspan="2" style="color: #FFF;">NOMBRE</th>
                <th rowspan="2" style="color: #FFF;">STOCK</th>
                <th colspan="2" style="text-align: center; color: #FFF;">PRECIO</th>
                <th colspan="2" style="text-align: center; color: #FFF;">Fechas</th>
                <th rowspan="2" style="color: #FFF; text-align: center;">ACCIONES</th>
            </tr>
            <tr>
                <th style="text-align: center; color: #FFF; background-color: #28B463;">P. Regular</th>
                <th style="text-align: center; color: #FFF; background-color: #EC7063;">P. Venta</th>
                <th style="text-align: center; color: #FFF;">F. Inicio</th>
                <th style="text-align: center; color: #FFF;">F. Fin</th>
            </tr>
        </thead>
        <tbody id="the-list">
            <?php
            if (count($lsProducts) > 0) {
                foreach ($lsProducts as $key => $value) {
                    $vId         = $value->id;
                    $vSKU        = $value->sku;
                    $vDetalle    = $value->name;
                    $vStock      = $value->stock_quantity;
                    $vPrecio     = $value->price;
                    $vPreRegular = $value->regular_price;
                    $vPreSale    = $value->sale_price;
                    $vFModif     = $value->date_modified;
                    $vHLink      = $value->permalink;
                    $vDateFrom   = $value->date_on_sale_from;
                    $vDateTo     = $value->date_on_sale_to;


                    $strPriceSale    = "-";
                    $strPriceRegular = $vPrecio;
                    $strStock        = "∞";
                    $strDateFrom     = "-";
                    $strDateTo       = "-";

                    if ($vPreSale != null) {
                        $strPriceSale    = $vPreSale;
                    }

                    if ($vPreRegular != null) {
                        $strPriceRegular = $vPreRegular;
                    }

                    if ($vStock != null) {
                        $strStock = $vStock;
                    }

                    if ($vDateFrom != null && $vDateTo != null) {
                        $strDateFrom = date('d-m-Y', strtotime($value->date_on_sale_from));
                        $strDateTo   = date('d-m-Y', strtotime($value->date_on_sale_to));
                    }


                    echo "
                   <tr>
                       <td> $vSKU </td>
                       <td> $vDetalle </td>     
                       <td style='text-align: center;'> $strStock </td>
                       <td style='text-align: center;'> $strPriceRegular </td>
                       <td style='text-align: center;'> $strPriceSale </td>
                       <td style='text-align: center;'> $strDateFrom </td> 
                       <td style='text-align: center;'> $strDateTo </td> 
                       <td>
                       <center>
                        <a href='$vHLink' target='_blank' class='btn btn-outline-info btn-sm'><i class='fa-solid fa-eye'></i></a>
                        <a data-ed_dsc_id='$vId' class='btn btn-outline-warning btn-sm'><i class='fa-solid fa-pen'></i></a>  
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
    <!---MODAL NUEVO DESCUENTO------>
    <div class="modal fade mt-5" id="mdNuevoDescuento" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Nuevo Descuento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="row border rounded" style="margin-left: 5px; margin-right: 5px;">
                            <div id="" class="form-text">Para añadir un nuevo descuento busque el producto por su codigo.</div>
                            <div class="mb-3 row">
                                <label for="txtCodigo" class="col-md-4 col-form-label mt-3">Buscar x Codigo</label>
                                <div class="col-md-8 mt-3">
                                    <select id="txtCodigo" name="txtCodigo" class="form-control" style="max-width: 100% !important;">
                                        <option value="" selected>Seleccione:</option>
                                        <?php
                                        foreach ($array as $key => $val) {
                                            echo '<option value="' . $val["aId"] . '">' . $val["aSKU"] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="txtNDetalle" class="col-md-4 col-form-label">Nombre</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="txtNDetalle" name="txtNDetalle">
                                </div>
                            </div>
                        </div>


                        <div class="row border rounded mt-3" style="margin-left: 5px; margin-right: 5px;">
                            <div class="col-md-6 mb-3">
                                <label for="fRegular">P. Regular</label>
                                <input type="text" class="form-control" id="idPRegular" name="idPRegular" onkeypress="return globalValDosDecimales(event,this);" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="fDescuento">P. Descuento</label>
                                <input type="text" class="form-control" id="idPDescuento" name="idPDescuento" onkeypress="return globalValDosDecimales(event,this);" required>
                            </div>
                        </div>

                        <div class="row border rounded mt-3" style="margin-left: 5px; margin-right: 5px;">
                            <div class="col-md-6 mb-3">
                                <label for="fInicio">F. Inicio</label>
                                <input type="date" class="form-control" id="idFInicio" name="idFInicio" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="fFin">F. Fin</label>
                                <input type="date" class="form-control" id="idFFin" name="idFFin" required>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary" name="btnGuardarDescuento">Guardar</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <!---MODAL EDITAR DESCUENTO------>

    <div class="modal fade mt-5" id="mdEditarDescuento" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Nuevo Descuento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <input type="hidden" class="form-control" id="txtId" name="txtId">
                    <div class="modal-body">
                        <div class="row border rounded" style="margin-left: 5px; margin-right: 5px;">
                            <div id="" class="form-text">Para añadir un nuevo descuento busque el producto por su codigo.</div>
                            <div class="mb-3 row">
                                <label for="frCodigo" class="col-md-4 col-form-label mt-3">Buscar x Codigo</label>
                                <div class="col-md-8 mt-3">
                                    <input type="text" class="form-control" id="txtEdCodigo" name="txtEdCodigo" disabled>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="frNDetalle" class="col-md-4 col-form-label">Nombre</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="txtEdDetalle" name="txtEdDetalle" disabled>
                                </div>
                            </div>
                        </div>


                        <div class="row border rounded mt-3" style="margin-left: 5px; margin-right: 5px;">
                            <div class="col-md-6 mb-3">
                                <label for="frRegular">P. Regular</label>
                                <input type="text" class="form-control" id="idEdPRegular" name="idEdPRegular" onkeypress="return globalValDosDecimales(event,this);" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="frDescuento">P. Descuento</label>
                                <input type="text" class="form-control" id="idEdPDescuento" name="idEdPDescuento" onkeypress="return globalValDosDecimales(event,this);" required>
                            </div>
                        </div>

                        <div class="row border rounded mt-3" style="margin-left: 5px; margin-right: 5px;">
                            <div class="col-md-6 mb-3">
                                <label for="frInicio">F. Inicio</label>
                                <input type="date" class="form-control" id="idEdFInicio" name="idEdFInicio">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="frFin">F. Fin</label>
                                <input type="date" class="form-control" id="idEdFFin" name="idEdFFin">
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary" name="btnActualizarDescuento">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>

    </div>


</div>


