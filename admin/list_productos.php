<?php

include_once("api/credentials.php");
include_once("api/cors.php");
$localHost = "https://" . $_SERVER["HTTP_HOST"];  // Solo Desarrollo http

if (isset($_POST['btnActualizarProductos'])) {
    //print_r($_POST);
    echo "<br>";

    $v_idProducto  = $_POST['idProdId'];
    $imagenVerify  = $_FILES['idEdtPImagen']['name'];
    //$ImgRutaUrl    = str_replace("https://", "http://",  $_POST['idShowImageLink']); // Solo Desarrollo
    $ImgRutaUrl = $_POST['idShowImageLink'];


    if (isset($imagenVerify) && $imagenVerify != "") {
        // //////////////////////////////////SUBIR ARCHIVO///////////////////////////////////////////        
        $pool = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $cod  = substr(str_shuffle(str_repeat($pool, 5)), 0, 7);

        $rutaActual = getcwd();
        $rutaBarra  = str_replace('\\', "/", $rutaActual);
        $uploaddir  = str_replace('wp-admin', "wp-content/uploads/2022/03/", $rutaBarra);

        $fileName   = $_FILES['idEdtPImagen']['name'];
        $extension  = $cod . "." . pathinfo($fileName, PATHINFO_EXTENSION);
        $uploadfile = $uploaddir . basename($extension);

        if (move_uploaded_file($_FILES['idEdtPImagen']['tmp_name'], $uploadfile)) {
            //echo "El archivo es válido y fue cargado exitosamente.\n";
            //

            $ImgRutaUrl = $localHost . "/wp-content/uploads/2022/03/" . $extension;
        } else {
            $ImgRutaUrl = "error: verifique";
        }
        // ////////////////////////////////////////////////////////////////////////////////////////////
    }
    
    $v_swDestacado = $_POST['idEdtPDestacado'];
    $v_destacado   = false;
    if (isset($v_swDestacado) && $v_swDestacado == "on") {
        $v_destacado = true;
    }

    $data = [
        'name'              => $_POST['idEdtPNombre'],
        'type'              => 'simple',
        'price'             => $_POST['idEdtPriceRegular'],
        'regular_price'     => $_POST['idEdtPriceRegular'],
        'sale_price'        => $_POST['idEdtPriceVenta'],
        'description'       => $_POST['idEdtPDescLarga'],
        'short_description' => $_POST['idEdtPDescCorta'],
        'stock_quantity'    => $_POST['idEdtStock'],
        'manage_stock'      => true,
        'featured'          => $v_destacado,
        'categories' => [
            [
                'id' =>  $_POST['idEdtCategorias'] //Mazzini
            ]
        ],
        'images' => [
            [
                'src' => $ImgRutaUrl
            ]
        ]
    ];

    $woocommerce->put('products/' . $v_idProducto, $data);
}

if (isset($_POST['btnGuardarProductos'])) {
    //print_r($_POST) . "<br>";
    // //////////////////////////////////SUBIR ARCHIVO///////////////////////////////////////////
    $ImgRutaUrl = "";
    $pool = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $cod  = substr(str_shuffle(str_repeat($pool, 5)), 0, 7);

    $rutaActual = getcwd();
    $rutaBarra  = str_replace('\\', "/", $rutaActual);
    $uploaddir  = str_replace('wp-admin', "wp-content/uploads/2022/03/", $rutaBarra);

    $fileName   = $_FILES['idPImagen']['name'];
    $extension  = $cod . "." . pathinfo($fileName, PATHINFO_EXTENSION);
    $uploadfile = $uploaddir . basename($extension);

    if (move_uploaded_file($_FILES['idPImagen']['tmp_name'], $uploadfile)) {
        //echo "El archivo es válido y fue cargado exitosamente.\n";
        $ImgRutaUrl = $localHost . "/wp-content/uploads/2022/03/" . $extension;
    } else {
        $ImgRutaUrl = "error: verifique";
    }
    ///echo $ImgRutaUrl;
    // ////////////////////////////////////////////////////////////////////////////////////////////
    $c_swDestacado = $_POST['idPDestacado'];
    $v_destacado   = false;
    if (isset($c_swDestacado) && $c_swDestacado == "on") {
        $v_destacado = true;
    }

    $data = [
        'sku'               => $_POST['idPSKU'],
        'name'              => $_POST['idPNombre'],
        'type'              => 'simple',
        'price'             => $_POST['idPriceRegular'],
        'regular_price'     => $_POST['idPriceRegular'],
        'sale_price'        => $_POST['idPriceVenta'],
        'description'       => $_POST['idPDescLarga'],
        'short_description' => $_POST['idPDescCorta'],
        'stock_quantity'    => $_POST['idProdStock'],
        'manage_stock'      => true,
        'featured'          => $v_destacado,
        'categories' => [
            [
                'id' =>  $_POST['idCategorias'] //Mazzini
            ]
        ],
        'images' => [
            [
                'src' => $ImgRutaUrl
            ]
        ]
    ];

    $woocommerce->post('products', $data);
}

// Listar Productos
$enProducts = json_encode($woocommerce->get('products'));
$lsProducts = json_decode($enProducts);

$enCategorias = json_encode($woocommerce->get('products/categories'));
$lsCategorias = json_decode($enCategorias);


?>

<script>
    var s_global_array = [];
    jQuery(document).ready(function($) {
        s_global_array = <?php echo json_encode($lsProducts); ?>;
        console.log("PRODUCTS > ", s_global_array);
    });
</script>
<style>
    .colr-text {
        font-weight: bold;
        color: #50575e;
        text-transform: uppercase;
    }
</style>

<div class="wrap">

    <div class="border bg-light text-dark" style="padding: 10px 20px 15px;">
        <?php
        echo '<h1 class="wp-heading-inline">' . get_admin_page_title() . '</h1>';
        ?>
        <a id="btnNuevoProducto" class="page-title-action">Añadir</a>
        <a id="" class="page-title-action">Importar</a>
        <a id="btnExportaProducto" class="page-title-action">Exportar</a>
    </div>
    <br>
    <table class="wp-list-table widefat fixed striped pages" id="tblListProductos">
        <thead class="table-dark" style="background-color: #3c434a;">
            <tr>
                <th style="color: #FFF; text-align: center;" rowspan="2" data-priority="1"> SKU </th>
                <th style="color: #FFF; text-align: center;" rowspan="2"><i class="fa-solid fa-image"></i></th>
                <th style="color: #FFF; text-align: center;" rowspan="2">Nombre</th>
                <th style="color: #FFF; text-align: center;" rowspan="2">Stock</th>
                <th style="color: #FFF; text-align: center;" colspan="2">Precio</th>
                <th style="color: #FFF; text-align: center;" rowspan="2">Categorias</th>
                <th style="color: #FFF; text-align: center;" rowspan="2">
                    <span class="star-popover" tabindex="0" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="Productos destacados.">
                        <i class="fa-solid fa-star"></i>
                    </span>
                </th>
                <th style="color: #FFF; text-align: center;" rowspan="2">
                    <span class="buffer-variation" tabindex="0" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="Variaciones del producto.">
                        <i class="fa-brands fa-buffer fa-lg"></i>
                    </span>
                </th>
                <!-- <th style="color: #FFF; text-align: center;" rowspan="2">
                    <span class="clock-variation-price" tabindex="0" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="Variaciones de precios.">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </span>
                </th> -->
                <th style="color: #FFF; text-align: center;" rowspan="2">Fecha</th>
                <th style="color: #FFF; text-align: center;" rowspan="2">ACCIONES</th>
            </tr>
            <tr>
                <th style="color: #FFF; text-align: center; background-color: #28B463;">P. REAL</th>
                <th style="color: #FFF; text-align: center; background-color: #EC7063;">P. VENTA</th>
            </tr>
        </thead>
        <tbody id="the-list">
            <?php
            if (count($lsProducts) > 0) {
                foreach ($lsProducts as $key => $value) {
                    $proId          = $value->id;
                    $proSKU         = $value->sku;
                    $proStock       = $value->stock_quantity;
                    $proDetalle     = $value->name;
                    $proPrecio      = $value->price;
                    $proPreRegular  = $value->regular_price;
                    $proPreSale     = $value->sale_price;
                    $proFModif      = date('d-m-Y', strtotime($value->date_modified));
                    $proHLink       = $value->permalink;
                    $proDest        = $value->featured;
                    $proVariaciones = $value->variations;
                    $proArryImagens = $value->images;
                    $proArryCateg   = $value->categories;

                    $strLinkImagen = $proArryImagens[0]->src;
                    $strCategoria  = " - ";

                    $strProPriceRegular = $proPrecio;
                    $strProPriceSale    = "-";
                    $htmlStarDestacado  = "<i class='fa-solid fa-star'></i>";
                    $htmlVariaciones    = "<i class='fa-solid fa-xmark' style='color: rgb(236, 112, 99);'></i>";
                    $strStockCuantity   = "<i class='fa-solid fa-infinity fa-xs'></i>";

                    if ($proPreRegular != null) {
                        $strProPriceRegular = $proPreRegular;
                    }

                    if ($proPreSale != null) {
                        $strProPriceSale = $proPreSale;
                    }

                    if ($proDest) {
                        $htmlStarDestacado = "<i class='fa-solid fa-star' style='color: rgb(241, 196, 15);'></i>";
                    }

                    if (count($proVariaciones) > 0) {
                        $htmlVariaciones   = "<i class='fa-solid fa-check' style='color: rgb(241, 196, 15);'></i>";
                    }

                    if (count($proArryCateg) > 0) {
                        $strCategoria = "";
                        foreach ($proArryCateg as $key => $cat) {
                            $strCategoria = $strCategoria . "<br>" . $cat->name;
                        }
                    }

                    if ($proStock != null) {
                        $strStockCuantity = $proStock;
                    }

                    echo "
                   <tr>
                       <td> $proSKU </td>
                       <td> <center> <img width='50' height='50' src='$strLinkImagen' class=attachment-thumbnail size-thumbnail' alt='' loading='lazy' sizes='(max-width: 50px) 100vw, 150px'> </center> </td>
                       <td> $proDetalle </td>     
                       <td style='text-align: center;'> $strStockCuantity </td>                        
                       <td style='text-align: center;'> $strProPriceRegular </td>
                       <td style='text-align: center;'> $strProPriceSale </td>
                       <td style='text-align: center;'> $strCategoria </td>
                       <td style='text-align: center;'> $htmlStarDestacado </td>
                       <td style='text-align: center;'> $htmlVariaciones  </td>                      
                       <td style='text-align: center;'> $proFModif </td> 
                       <td>
                       <center>
                        <a href='$proHLink' target='_blank' class='btn btn-outline-info btn-sm'><i class='fa-solid fa-eye'></i></a>
                        <a data-edt_prod_id='$proId' class='btn btn-outline-warning btn-sm'><i class='fa-solid fa-pen'></i></a>  
                        <a data-del_prod_id='$proId' class='btn btn-outline-danger btn-sm'><i class='fa-solid fa-trash'></i></a> 
                       </center>
                       </td>
                    </tr>";
                }
            } else {
                echo "<td colspan='12'><center> No existen datos. </center></td>";
            }
            ?>
        </tbody>
    </table>

    <!-----------MODAL NUEVO PRODUCTO---------------->
    <div class="modal fade mt-5" id="mdNuevoProducto" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Nuevo Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">

                    <div class="modal-body">

                        <div class="row border rounded" style="margin: 5px;">
                            <div class="mb-3 mt-2">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="frSKU" class="form-label colr-text">SKU</label>
                                        <input type="text" class="form-control" id="idPSKU" name="idPSKU">
                                    </div>
                                    <div class="col-md-7">
                                        <label for="frNombre" class="form-label colr-text">Nombre</label>
                                        <input type="text" class="form-control" id="idPNombre" name="idPNombre">
                                    </div>
                                    <div class="col-md-1" style="text-align: center;">
                                        <label for="frNombre" class="form-label colr-text"><i id="fa-nwIdDestacado" class="fa-solid fa-star fa-xl"></i></label>
                                        <input type="checkbox" class="custom-control-input" id="idPDestacado" name="idPDestacado" style="margin-top: 5px;">

                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 mt-2">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="frDescCorta" class="form-label colr-text">DESCRIPCIÓN CORTA</label>
                                        <input type="text" class="form-control" id="idPDescCorta" name="idPDescCorta">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3 mt-2">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="frDescLarga" class="form-label colr-text">DESCRIPCIÓN LARGA</label>
                                        <textarea type="text" class="form-control" id="idPDescLarga" name="idPDescLarga"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row border rounded mt-3" style="margin: 5px;">
                            <div class="mb-3 mt-2">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="frFile" class="form-label colr-text">Imagen (es)</label>
                                        <input class="form-control" type="file" style="padding: 4px 5px 4px 5px;" id="idPImagen" name="idPImagen" onchange="previsualizarNew(event);">

                                        <div class="form-group" id="newImgContenido" style="display: none;">
                                            <div class="text-center">
                                                <img src="" alt="imagen" id="idNwShowImage" height="220" width="220" class="img-thumbnail mt-2">
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-6">
                                        <label for="frCateg" class="form-label colr-text">Categorias</label>
                                        <select class="form-select" aria-label="Default select example" id="idCategorias" name="idCategorias">
                                            <option selected>Seleccione la categoria (as)</option>
                                            <?php
                                            if (count($lsCategorias) > 0) {
                                                foreach ($lsCategorias as $key => $value) {
                                                    $cId   = $value->id;
                                                    $cName = $value->name;
                                                    echo "<option value='$cId'>$cName</option>";
                                                }
                                            } else {
                                                echo "<option value=''>No hay categorias</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--<div class="row border rounded mt-3" style="margin: 5px;">
                            <div class="mb-3 mt-2">
                                <div class="row">
                                    <div class="col-sm-10">
                                        <select  class="form-select" aria-label="Default select example" name="States1" id="edit-states1-id" data-placeholder="Placeholder..." multiple="multiple" style="display: none">
                                            <option value="AL">Alabama</option>
                                            <option value="AK" disabled>Alaska</option>
                                            <option value="AZ">Arizona</option>
                                            <option value="AR">Arkansas</option>
                                            <option selected value="CA">California</option>
                                            <option value="CO">Colorado</option>
                                            <option value="CT">Connecticut</option>
                                            <option value="GA">Georgia</option>
                                            <option value="HI" hidden selected>Hawaii Hidden</option>
                                            <option value="ID" hidden>Idaho Hidden</option>
                                            <option value="IL">Illinois</option>
                                            <option value="IN">Indiana</option>
                                            <option value="ND">North Dakota</option>
                                            <option value="OH">Ohio</option>
                                            <option value="OK">Oklahoma</option>
                                            <option value="OR">Oregon</option>
                                            <option selected value="PA">Pennsylvania</option>
                                            <option value="RI">Rhode Island</option>
                                            <option value="SC">South Carolina</option>
                                            <option value="SD">South Dakota</option>
                                            <option value="TN">Tennessee</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>-->

                        <div class="row border rounded mt-3" style="margin: 5px;">
                            <div class="mb-3 mt-2">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="frStock" class="form-label colr-text">STOCK</label>
                                        <input type="number" class="form-control" id="idProdStock" name="idProdStock" min="1">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="frPReg" class="form-label colr-text">P. Regular</label>
                                        <input type="text" class="form-control" id="idPriceRegular" name="idPriceRegular" onkeypress="return globalValDosDecimales(event,this);">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="frPVent" class="form-label colr-text">P. Venta</label>
                                        <input type="text" class="form-control" id="idPriceVenta" name="idPriceVenta" onkeypress="return globalValDosDecimales(event,this);">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary" name="btnGuardarProductos" id="btnGuardarProductos">Guardar</button>
                    </div>
                </form>
            </div>
            <br><br>
        </div>
    </div>



    <!-----------MODAL EDITAR PRODUCTO---------------->
    <div class="modal fade mt-5" id="mdEditarProducto" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Editar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" class="form-control" id="idProdId" name="idProdId">
                        <div class="row border rounded" style="margin: 5px;">
                            <div class="mb-3 mt-2">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="frEdSKU" class="form-label colr-text">SKU</label>
                                        <input type="text" class="form-control" id="idEdtPSKU" name="idEdtPSKU" disabled>
                                    </div>
                                    <div class="col-md-7">
                                        <label for="frNombre" class="form-label colr-text">Nombre</label>
                                        <input type="text" class="form-control" id="idEdtPNombre" name="idEdtPNombre">
                                    </div>
                                    <div class="col-md-1" style="text-align: center;">
                                        <label for="frEdNombre" class="form-label colr-text"><i id="fa-EdDestacado" class="fa-solid fa-star fa-xl"></i></label>
                                        <input type="checkbox" class="custom-control-input" id="idEdtPDestacado" name="idEdtPDestacado" style="margin-top: 5px;">

                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 mt-2">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="frEdDescCorta" class="form-label colr-text">DESCRIPCIÓN CORTA</label>
                                        <input type="text" class="form-control" id="idEdtPDescCorta" name="idEdtPDescCorta">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3 mt-2">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="frEdDescLarga" class="form-label colr-text">DESCRIPCIÓN LARGA</label>
                                        <textarea type="text" class="form-control" id="idEdtPDescLarga" name="idEdtPDescLarga"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row border rounded mt-3" style="margin: 5px;">
                            <div class="mb-3 mt-2">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="frEdFile" class="form-label colr-text">Imagen (es)</label>
                                        <input class="form-control" type="file" style="padding: 4px 5px 4px 5px;" id="idEdtPImagen" name="idEdtPImagen" onchange="previsualizar_edit(event);">

                                        <div class="form-group" id="editImgContenido" style="display: none;">
                                            <div class="text-center">
                                                <img src="" alt="imagen" id="idEdShowImage" height="220" width="220" class="img-thumbnail mt-2">
                                            </div>
                                        </div>
                                        <input type="hidden" class="form-control" id="idShowImageLink" name="idShowImageLink">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="frEdCateg" class="form-label colr-text">Categorias</label>
                                        <select class="form-select" aria-label="Default select example" id="idEdtCategorias" name="idEdtCategorias">
                                            <option selected>Seleccione la categoria (as)</option>
                                            <?php
                                            if (count($lsCategorias) > 0) {
                                                foreach ($lsCategorias as $key => $value) {
                                                    $cId   = $value->id;
                                                    $cName = $value->name;
                                                    echo "<option value='$cId'>$cName</option>";
                                                }
                                            } else {
                                                echo "<option value=''>No hay categorias</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row border rounded mt-3" style="margin: 5px;">
                            <div class="mb-3 mt-2">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="frEdStock" class="form-label colr-text">STOCK</label>
                                        <input type="number" class="form-control" id="idEdtStock" name="idEdtStock" min="1">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="frEdPReg" class="form-label colr-text">P. Regular</label>
                                        <input type="text" class="form-control" id="idEdtPriceRegular" name="idEdtPriceRegular" onkeypress="return globalValDosDecimales(event,this);">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="frEdPVent" class="form-label colr-text">P. Venta</label>
                                        <input type="text" class="form-control" id="idEdtPriceVenta" name="idEdtPriceVenta" onkeypress="return globalValDosDecimales(event,this);">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary" name="btnActualizarProductos" id="btnActualizarProductos">Guardar</button>
                    </div>
                </form>
            </div>
            <br><br>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/@dashboardcode/bsmultiselect@1.1.18/dist/js/BsMultiSelect.min.js"></script>