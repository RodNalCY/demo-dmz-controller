<?php
include_once("api/credentials.php");
include_once("api/cors.php");

// header("Refresh: 10");
// TABLA VARIACION LOCAL
global $wpdb;
$tbl_variacion        = "{$wpdb->prefix}variacion_precio";
$tbl_directorio       = "{$wpdb->prefix}variacion_directorio";
$global_ruta_actual   = __DIR__;
$global_barra_replace = str_replace('\\', "/", $global_ruta_actual);

//ELIMINAR FILE DE
if (isset($_POST['btnEliminar'])) {
    //print_r($_POST);
    $del_id        = $_POST['del_id'];
    $del_name_file = $_POST['del_name_file'];
    $del_ruta      = $_POST['del_ruta'];

    $f_delete =  $del_ruta ."/".$del_name_file;
    
    if (unlink($f_delete)) {
        //echo "Eliminado";
        $wpdb->delete($tbl_variacion, array('var_id' => $del_id));
    } else {
        //echo "No se Eliminado";
    }   

}

//GUARDAR VARIACION DE PRECIOS
if (isset($_POST['btnGuardarVariacion'])) {
    // print_r($_POST);
    $s_pro_Id   = $_POST["sVarId"];
    $s_SKU      = $_POST["sVarTxTSKU"];
    $s_Detalle  = $_POST["txtVNDetalle"];
    $s_PRegular = $_POST["idVPRegular"];
    $s_PVenta   = $_POST["idVPVenta"];
    $s_VPorcent = $_POST["idVPorcentaje"];

    $neo_directory      = $_POST['txtVNombreFile'];
    $old_directory      = $_POST['fileVNombreFile'];
    $id_directory       = $_POST['sIdDirectory'];
    $name_file          = "cron_" . $s_SKU . "_update_" . $s_pro_Id . ".php";
    $main_nm_directory  = "";
    $main_id_directory  = 0;


    if (isset($old_directory) && (isset($id_directory) || !empty($id_directory))) {

        $main_nm_directory = $old_directory;
        $main_id_directory = $id_directory;
    } else if (!empty($neo_directory)) {

        $main_nm_directory = $neo_directory;
        $virtual_ruta      = $global_barra_replace . "/cron" . "/" . $main_nm_directory;

        $dir_data = [
            'dir_name'      => $main_nm_directory,
            'dir_ruta'      => $virtual_ruta,
        ];

        $dir_save = $wpdb->insert($tbl_directorio, $dir_data);
        $main_id_directory = $wpdb->insert_id;
    }

    // Crear Directorio
    $directory = $global_barra_replace . "/cron" . "/" . $main_nm_directory;

    if (!file_exists($directory)) {
        mkdir($directory, 0777, true);
    }

    $dataLocal = [
        'var_id_producto'       => $s_pro_Id,
        'var_sku_producto'      => $s_SKU,
        'var_precio_regular'    => $s_PRegular,
        'var_precio_venta'      => $s_PVenta,
        'var_porcentaje'        => $s_VPorcent,
        'var_id_cron_directory' => $main_id_directory,
        'var_precio_final'      => $s_PVenta,
        'var_dir_name_file'     => $name_file
    ];

    $saved = $wpdb->insert($tbl_variacion, $dataLocal);
    if ($saved) {

        // Crear File .php
        $v_id = $wpdb->insert_id;
        $file_cron = $directory . "/" . $name_file;
        $archivo   = fopen($file_cron, "w+b");
        if ($archivo == false) {
            echo 'Error al crear el archivo';
        } else {
            fwrite($archivo, "<?php\r\n");
            fwrite($archivo, "include_once('../../api/credentials.php'); \r\n");
            fwrite($archivo, "include_once('../../api/cors.php'); \r\n");
            fwrite($archivo, "include('../../../../../../wp-load.php'); \r\n");
            fwrite($archivo, "global $" . "wpdb; \r\n");
            fwrite($archivo, '$tbl_variacion = "{$wpdb->prefix}variacion_precio";');
            fwrite($archivo, "\r\n" . "$" . "v_id                = $v_id; \r\n");
            fwrite($archivo, "$" . "v_id_producto       = $s_pro_Id;\r\n");
            fwrite($archivo, "$" . "query_variacion     = 'SELECT * FROM '. $" . "tbl_variacion .' WHERE var_id='.$" . "v_id; \r\n");
            fwrite($archivo, "$" . "list_result         = $" . "wpdb->get_results($" . "query_variacion, ARRAY_A); \r\n");
            fwrite($archivo, "$" . "v_porcentaje        = $" . "list_result[0]['var_porcentaje']; \r\n");
            fwrite($archivo, "$" . "v_precio_final      = $" . "list_result[0]['var_precio_final']; \r\n");
            fwrite($archivo, "$" . "v_precio_modificado = $" . "v_precio_final - 20; \r\n");
            fwrite($archivo, "$" . "data_local = ['var_precio_final'   => $" . "v_precio_modificado];\r\n");
            fwrite($archivo, '$data_id = ["var_id" => $v_id];');
            fwrite($archivo, "$" . "update = $" . "wpdb->update($" . "tbl_variacion, $" . "data_local, $" . "data_id);\r\n");
            fwrite($archivo, '$queryProcedure = "CALL updateTimeCron($v_id)";');
            fwrite($archivo, "\r\n" . "$" . "wpdb->get_results($" . "queryProcedure, ARRAY_A); \r\n");
            fwrite($archivo, "?>\r\n");
            fflush($archivo);
        }
        fclose($archivo);


        $data_api = [
            'price'                => $s_PVenta,
            'sale_price'           => $s_PVenta
        ];
        $woocommerce->put('products/' . $s_pro_Id, $data_api);
    }
}

if (isset($_POST['btnActualizarVariacion'])) {
    print_r($_POST);
    $ve_Id       = $_POST["txtEdVId"];
    $ve_pro_Id   = $_POST["txtEdVProId"];
    $ve_dir_Id   = $_POST["txtEdFileVId"];
    $ve_VPortj   = $_POST["idEdVPorcentaje"];
    $ve_IdFile   = $_POST["sIdEdVNameFile"];
    $ve_VPVenta  = $_POST['idEdVPVenta'];

    $dataLocal = [
        'var_porcentaje'   => $ve_VPortj,
        'var_precio_venta' => $ve_VPVenta,
    ];

    $dataID = [
        'var_id' => $ve_Id,
    ];

    $update = $wpdb->update($tbl_variacion, $dataLocal, $dataID);

    if ($update) {
        $data = [
            'price'                => $ve_VPVenta,
            'sale_price'           => $ve_VPVenta
        ];
        $woocommerce->put('products/' . $ve_pro_Id, $data);
    }
}
//LISTAR VARIACION DE PRECIOS
$vProducts  = json_encode($woocommerce->get('products'));
$list_products_api = json_decode($vProducts);
//var_dump($list_products_api);
$array_list_select = [];

foreach ($list_products_api as $key => $val) {
    $sPriceSal    = "";
    $sPriceReg = $val->price;

    if ($val->sale_price != null) {
        $sPriceSal    = $val->sale_price;
    }

    if ($val->regular_price != null) {
        $sPriceReg = $val->regular_price;
    }

    $array_list_select[] = array(
        "vId"       => $val->id,
        "vSKU"      => $val->sku,
        "vName"     => $val->name,
        "vRegPrice" => $sPriceReg,
        "vSalPrice" => $sPriceSal
    );
}


//LISTAR PRODUCTOS DE VARIACION DE PRECIO
$query_variacion = "SELECT $tbl_variacion.*, $tbl_directorio.dir_id, $tbl_directorio.dir_name,  $tbl_directorio.dir_ruta FROM $tbl_variacion INNER JOIN $tbl_directorio ON $tbl_variacion.var_id_cron_directory = $tbl_directorio.dir_id";
$list_products_local = $wpdb->get_results($query_variacion, ARRAY_A);

$array_all_productos = [];

foreach ($list_products_api as $key1) {
    //echo $key1->id."<br>";
    foreach ($list_products_local as $key2) {
        //echo $key2["var_id_producto"]."<br>";
        if ($key1->id == $key2["var_id_producto"]) {
            $array_all_productos[] = array(
                "varProId"       => $key1->id,
                "varSKU"         => $key1->sku,
                "varName"        => $key1->name,
                "varDescrip"     => $key1->description,
                "varDestacado"   => $key1->featured,
                "varMultiple"    => $key1->variations,
                "varPrecio"      => $key1->price,
                "varPRegular"    => $key1->regular_price,
                "varPVenta"      => $key1->sale_price,
                "varPermaLink"   => $key1->permalink,
                "varId"          => $key2["var_id"],
                "varPFinal"      => $key2["var_precio_final"],
                "varPorcentaje"  => $key2["var_porcentaje"],
                "varTiempo"      => $key2["var_tiempo"],
                "varDirId"       => $key2["dir_id"],
                "varDirName"     => $key2["dir_name"],
                "varDirRuta"     => $key2["dir_ruta"],
                "varNameFile"    => $key2["var_dir_name_file"],
                "varFecha"       => $key2["created_at"],
            );
        }
    }
}


//LISTAR DIRECTORIOS 
$query_directory  = "SELECT * FROM $tbl_directorio";
$list_directories = $wpdb->get_results($query_directory, ARRAY_A);

?>

<script>
    var s_global_array_select = [];
    var s_global_array_var_match_all = [];
    var s_global_array_api = [];
    var s_global_array_local = [];
    var array_productos_no_add = [];
    var html_select_lista = "";

    var s_global_array_directories = [];

    jQuery(document).ready(function($) {

        s_global_array_select = <?php echo json_encode($array_list_select); ?>;

        s_global_array_api = <?php echo json_encode($list_products_api); ?>;
        console.log("API > ", s_global_array_api);

        s_global_array_local = <?php echo json_encode($list_products_local); ?>;
        console.log("LOCAL > ", s_global_array_local);

        s_global_array_var_match_all = <?php echo json_encode($array_all_productos); ?>;
        console.log("INTERSECT > ", s_global_array_var_match_all);

        s_global_array_directories = <?php echo json_encode($list_directories); ?>;
        console.log("Directories > ", s_global_array_directories);

        array_productos_no_add = <?php echo json_encode($list_products_api); ?>;

        ///////////////////////////////////////////////////////////        

        s_global_array_local.forEach((el, ind, arr) => {
            array_productos_no_add = array_productos_no_add.filter(function(item) {
                return item.id != el.var_id_producto;
            });
        });

        html_select_lista = '<option value="" selected>Seleccione:</option>';
        array_productos_no_add.forEach((el, ind, arr) => {
            var id = el.id;
            var sku = el.sku;
            html_select_lista = html_select_lista + "<option value=" + id + ">" + sku + "</option>";
        });

        $(".html_code").html(html_select_lista);

        //////////////////////////////////////////////////////////

        $('#sVarId').on('change', function() {
            array_productos_no_add.forEach(element => {
                if (this.value == element['id']) {
                    console.log("RDX > " + this.value + " | " + element['id']);

                    let sale_precio = 0;

                    if (element['price'] == "") {
                        sale_precio = element['sale_price'];
                    } else {
                        sale_precio = element['price'];
                    }

                    $("#idVPRegular").val(element['regular_price']);
                    $("#txtVNDetalle").val(element['name']);
                    $("#idVPVenta").val(element['sale_price']);
                    $("#sVarTxTSKU").val(element['sku']);
                }
            });
        });

        //////////////////////////////////////////////////////////
        $('#sIdDirectory').on('change', function() {
            s_global_array_directories.forEach(element => {
                if (this.value == element['dir_id']) {
                    console.log("DIR ID > " + this.value + " | " + element['dir_id']);
                    $("#fileVNombreFile").val(element['dir_name']);
                }
            });
        });

    });
</script>

<div class="wrap">
    <div class="border bg-light text-dark" style="padding: 10px 20px 15px;">
        <?php
        echo '<h1 class="wp-heading-inline">' . get_admin_page_title() . '</h1>';
        ?>
        <a id="btnNuevaVariacion" class="page-title-action">Añadir</a>
        <a id="" class="page-title-action">Importar</a>
        <a id="btnExportaVariacion" class="page-title-action">Exportar</a>
    </div>
    <br>
    <table class="wp-list-table widefat fixed striped pages" id="tblListVariables">
        <thead class="table-dark" style="background-color: #3c434a;">
            <tr>
                <th rowspan="2" style="color: #FFF;" data-priority="1">CODIGO</th>
                <th rowspan="2" style="color: #FFF; text-align: center;">NOMBRE</th>
                <th rowspan="2" style="color: #FFF; text-align: center;"><i class="fa-solid fa-star"></i></th>
                <!-- <th rowspan="2" style="color: #FFF; text-align: center;"><i class="fa-brands fa-buffer fa-lg"></i></th> -->
                <th colspan="3" style="color: #FFF; text-align: center;">Variables de Precios</th>
                <th colspan="2" style="color: #FFF; text-align: center;">Variables de Variación</th>
                <th rowspan="2" style="color: #FFF; text-align: center;">FECHA</th>
                <th rowspan="2" style="color: #FFF; text-align: center;">ACCIONES</th>
            </tr>
            <tr>
                <th style="color: #FFF; text-align: center; background-color: #28B463;">PRECIO REGULAR</th>
                <th style="color: #FFF; text-align: center; background-color: #EC7063;">PRECIO VENTA</th>
                <th style="color: #FFF; text-align: center; background-color: #F1C40F;">PRECIO FINAL</th>

                <th style="color: #FFF; text-align: center;"> PORCENTAJE </th>
                <th style="color: #FFF; text-align: center;">TIEMPO</th>
            </tr>
        </thead>
        <tbody id="the-list">
            <?php
            if (count($array_all_productos) > 0) {
                foreach ($array_all_productos as $key => $value) {
                    $vrId           = $value['varId'];
                    $vrSKU          = $value['varSKU'];
                    $vrDetalle      = $value['varName'];
                    $vrDestacado    = $value['varDestacado'];
                    $vrVariaciones  = $value['varMultiple'];
                    $vrPrecio       = $value['varPrecio'];
                    $vrPreRegular   = $value['varPRegular'];
                    $vrPreSale      = $value['varPVenta'];
                    $vrPreFinal     = $value['varPFinal'];
                    $vrFModif       = date('H:i d-m-Y', strtotime($value['varFecha']));
                    $vrTiempo       = $value['varTiempo'];
                    $vrHLink        = $value['varPermaLink'];
                    $vrPorVariacion = $value['varPorcentaje'];

                    $vrDirId       = $value['varDirId'];
                    $vrDirNameFile = $value['varNameFile'];
                    $vrDirName     = $value['varDirName'];
                    $vrDirRuta     = $value['varDirRuta'];
                    // $vrTiempo       = $value['varTiempo'];
                    // $vrTipoTiempo   = $value['varTypeTime'];

                    $htmlStarDestacado = "<i class='fa-solid fa-star'></i>";
                    if ($vrDestacado) {
                        $htmlStarDestacado = "<i class='fa-solid fa-star' style='color: rgb(241, 196, 15);'></i>";
                    }

                    $htmlVariaciones   = "<i class='fa-solid fa-xmark' style='color: rgb(236, 112, 99);'></i>";
                    if (count($vrVariaciones) > 0) {
                        $htmlVariaciones   = "<i class='fa-solid fa-check' style='color: rgb(241, 196, 15);'></i>";
                    }

                    $strvPriceSale =  $vrPreSale;
                    if ($strvPriceSale == null) {
                        $strvPriceSale = $vrPrecio;
                    }

                    $strvPriceRegular = "-";
                    if ($vrPreRegular != null) {
                        $strvPriceRegular = $vrPreRegular;
                    }

                    $htmlTiempo = "-";
                    if ($vrTiempo != null) {
                        $htmlTiempo = date('H:i d-m-Y', strtotime($vrTiempo));
                    }

                    echo "
                   <tr>
                       <td> $vrSKU </td>
                       <td> $vrDetalle </td>     
                       <td style='text-align: center;'> $htmlStarDestacado </td>
                       
                       <td style='text-align: center;'> $strvPriceRegular </td>
                       <td style='text-align: center;'> $strvPriceSale </td>
                       <td style='text-align: center;'> $vrPreFinal </td> 
                       <td style='text-align: center;'> $vrPorVariacion </td> 
                       <td style='text-align: center;'> $htmlTiempo </td> 
                       <td style='text-align: center;'> $vrFModif </td> 
                       <td>
                       <center>
                        <a href='$vrHLink' target='_blank' class='btn btn-outline-info btn-sm'><i class='fa-solid fa-eye'></i></a>
                        <a data-edt_var_id='$vrId' class='btn btn-outline-warning btn-sm'><i class='fa-solid fa-pen'></i></a>
                        <a data-del_var_id='$vrId' class='btn btn-outline-danger btn-sm'><i class='fa-solid fa-xmark'></i></a>
                       </center> 
                       </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='9'><center> No existen datos. </center></td></tr>";
            }
            ?>
        </tbody>
    </table>

    <!---MODAL NUEVO DESCUENTO------>
    <div class="modal fade mt-5" id="mdNuevaVariacion" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Nueva Variación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="sVarTxTSKU" name="sVarTxTSKU">
                        <div class="row border rounded" style="margin-left: 5px; margin-right: 5px;">
                            <div id="" class="form-text">Para añadir un nuevo descuento busque el producto por su codigo.</div>
                            <div class="mb-3 row">
                                <label for="sVarId" class="col-md-4 col-form-label mt-3">Buscar x Codigo</label>
                                <div class="col-md-8 mt-3">

                                    <select id="sVarId" name="sVarId" class="html_code form-control" style="max-width: 100% !important;">

                                        <?php
                                        /*foreach ($array_list_select as $key => $val) {
                                            echo '<option value="' . $val["vId"] . '">' . $val["vSKU"] . '</option>';
                                        }*/
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="fVNDetalle" class="col-md-4 col-form-label">Nombre</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="txtVNDetalle" name="txtVNDetalle">
                                </div>
                            </div>
                        </div>

                        <div class="row border rounded mt-3" style="margin-left: 5px; margin-right: 5px;">
                            <div class="col-md-4 mb-3">
                                <label for="fVRegular">P. Real</label>
                                <input type="text" class="form-control" id="idVPRegular" name="idVPRegular" onkeypress="return globalValDosDecimales(event,this);" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="fVDescuento">P. Venta</label>
                                <input type="text" class="form-control" id="idVPVenta" name="idVPVenta" onkeypress="return globalValDosDecimales(event,this);" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="fVPorcentaje">% Varicación</label>
                                <input type="number" class="form-control" id="idVPorcentaje" name="idVPorcentaje" required>
                            </div>
                        </div>

                        <div class="row border rounded mt-3" style="margin-left: 5px; margin-right: 5px;">
                            <div id="" class="form-text">Cree el directorio de los 'Trabajos en CRON' para aplicar la variación (segundos, minutos, horas & dias).</div>
                            <div class="mb-3 row mt-3">
                                <input type="hidden" id="fileVNombreFile" name="fileVNombreFile">
                                <label for="sIdDirectory" class="col-md-4 col-form-label" id="lblColor"><i class="fa-solid fa-folder-open"></i> Directorio</label>
                                <div class="col-md-7">
                                    <select id="sIdDirectory" name="sIdDirectory" class="form-control" style="max-width: 100% !important;">
                                        <option value="" selected>Seleccione:</option>
                                        <?php
                                        foreach ($list_directories as $key => $v) {
                                            echo '<option value="' . $v["dir_id"] . '">' . $v["dir_name"] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-md-1">
                                    <button type="button" id="btnNeoDirectory" class="btn btn-success btn-sm"><i class="fa-solid fa-plus"></i></button>
                                </div>
                            </div>

                            <div class="mb-3 row mt-3 containAddName" style="display: none;">
                                <label for="txtVNombreFile" class="col-md-4 col-form-label"><i class="fa-solid fa-folder-open"></i> Nombre </label>
                                <div class="col-md-7">
                                    <input type="text" class="form-control" id="txtVNombreFile" name="txtVNombreFile">
                                </div>
                                <div class="col-md-1">
                                    <button type="button" id="btnCerrarDirectory" class="btn btn-danger btn-sm"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                            </div>

                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary" name="btnGuardarVariacion">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!---MODAL EDITAR VARIACION------>
    <div class="modal fade mt-5" id="mdEditarVariacion" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Editar Variación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <input type="hidden" class="form-control" id="txtEdVId" name="txtEdVId">
                    <input type="hidden" class="form-control" id="txtEdVProId" name="txtEdVProId">
                    <input type="hidden" class="form-control" id="txtEdFileVId" name="txtEdFileVId">
                    <div class="modal-body">
                        <div class="row border rounded" style="margin-left: 5px; margin-right: 5px;">
                            <div id="" class="form-text">Para añadir un nuevo descuento busque el producto por su codigo.</div>
                            <div class="mb-3 row">
                                <label for="frVCodigo" class="col-md-4 col-form-label mt-3">Buscar x Codigo</label>
                                <div class="col-md-8 mt-3">
                                    <input type="text" class="form-control" id="txtEdVCodigo" name="txtEdVCodigo" disabled>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="frVDetalle" class="col-md-4 col-form-label">Nombre</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="txtEdVNDetalle" name="txtEdVNDetalle" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="row border rounded mt-3" style="margin-left: 5px; margin-right: 5px;">
                            <div class="col-md-3 mb-3">
                                <label for="frVRegular">P. Real</label>
                                <input type="text" class="form-control" id="idEdVPRegular" name="idEdVPRegular" onkeypress="return globalValDosDecimales(event,this);" required disabled>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="frVDescuento">P. Venta</label>
                                <input type="text" class="form-control" id="idEdVPVenta" name="idEdVPVenta" onkeypress="return globalValDosDecimales(event,this);" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="frVFinal">P. Final</label>
                                <input type="text" class="form-control" id="idEdPFinal" name="idEdPFinal" onkeypress="return globalValDosDecimales(event,this);" required disabled>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="frVPorcentaje">% Varicación</label>
                                <input type="number" class="form-control" id="idEdVPorcentaje" name="idEdVPorcentaje" required>
                            </div>
                        </div>

                        <div class="row border rounded mt-3" style="margin-left: 5px; margin-right: 5px;">
                            <div class="mb-3 row mt-3 containAddName">
                                <div class="col-md-5">
                                    <label for="sIdEdVNameFile" class="col-form-label"><i class="fa-solid fa-folder-tree"></i></label>
                                    <select id="sIdEdVNameFile" name="sIdEdVNameFile" class="form-control" style="max-width: 100% !important;" disabled>
                                        <option value="" selected>Seleccione:</option>
                                        <?php
                                        foreach ($list_directories as $key => $v) {
                                            echo '<option value="' . $v["dir_id"] . '">' . $v["dir_name"] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="idEdVUbicacionFile" class="col-form-label"><i class="fa-solid fa-folder-open"></i></label>
                                    <input type="text" class="form-control" id="idEdVUbicacionFile" name="idEdVUbicacionFile" disabled>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" id="btnEditDirectoryFile" class="btn btn-success btn-sm" style="margin-top: 40px;"><i class="fa-solid fa-pencil"></i></button>
                                    <button type="button" id="btnXEditDirectoryFile" class="btn btn-danger btn-sm" style="margin-top: 40px; display: none;"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                            </div>
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary" name="btnActualizarVariacion" id="btnActualizarVariacion">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



</div>