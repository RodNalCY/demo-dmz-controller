<?php
include 'validate_credentials.php';
global $wpdb;

if (isset($_POST['btnCreateAPIWoocommers'])) {
    //print_r($_POST);

    $IdUsr      = $_POST['txtUsuario'];
    $txtDomain  = $_POST['id_CreDominio'];
    $txtPermiso = $_POST['txtPermiso'];
    $txtDescrip = $_POST['txtDescripcion'];

    $consumer_key       = 'ck_' . wc_rand_hash();
    $consumer_secret    = 'cs_' . wc_rand_hash();
    $secret_key_hash    = wc_api_hash($consumer_key);
    $seven_truncate_key = substr($consumer_key, -7);

    $ruta_actual     = __DIR__;
    $barraReplace    = str_replace('\\', "/", $ruta_actual);
    $file_credential = $barraReplace . "/api/credentials.php";
    // Abrir el archivo, creándolo si no existe:
    $archivo = fopen($file_credential, "w+b");

    if ($archivo == false) {
        echo '<script> Swal.fire({ icon: "error", title: "Error!", text: "La activación salio mal !",showConfirmButton: false, timer: 3000,});</script>';
    } else {
        // Escribir en el archivo:
        fwrite($archivo, "<?php\r\n");
        fwrite($archivo, "require __DIR__ . '/vendor/autoload.php';\r\n");
        fwrite($archivo, "use Automattic\WooCommerce\Client;\r\n");
        fwrite($archivo, "$" . "woocommerce = new Client(\r\n");
        fwrite($archivo, "'$txtDomain/',  \r\n");
        fwrite($archivo, "'$consumer_key', \r\n");
        fwrite($archivo, "'$consumer_secret', \r\n");
        fwrite($archivo, "['version' => 'wc/v3', ] \r\n");
        fwrite($archivo, "); \r\n");
        fwrite($archivo, "?>");
        // Fuerza a que se escriban los datos pendientes en el buffer:
        fflush($archivo);

        // Guardar en la Base de datos
        $sql_tb_woo_insert_api_key = "INSERT INTO {$wpdb->prefix}woocommerce_api_keys(
            `user_id`, `description`, `permissions`, `consumer_key`, `consumer_secret`, `truncated_key`) 
            VALUES ('$IdUsr', '$txtDescrip', '$txtPermiso', '$secret_key_hash', '$consumer_secret', '$seven_truncate_key');";

        $wpdb->query($sql_tb_woo_insert_api_key);
        //MENSAJE SUCCESS
        echo '<script> Swal.fire({ icon: "success", title: "Perfecto!", text: "Listo. Ahora ya puede usar la DMZ",showConfirmButton: false, timer: 3000,});</script>';
    }
    // Cerrar el archivo:
    fclose($archivo);
}

if (isset($_POST['btnUpdateWoocommers'])) {
    // Obtenemos los parametros
    $url     = $_POST['id_Dominio'];
    $ckey    = $_POST['id_CKey'];
    $csecret = $_POST['id_CSecret'];

    $ruta_actual     = __DIR__;
    $barraReplace    = str_replace('\\', "/", $ruta_actual);
    $file_credential = $barraReplace . "/api/credentials.php";
    // Abrir el archivo, creándolo si no existe:
    $archivo = fopen($file_credential, "w+b");

    if ($archivo == false) {
        echo '<script> Swal.fire({ icon: "error", title: "Error!", text: "La activación salio mal !",showConfirmButton: false, timer: 3000,});</script>';
    } else {
        // Escribir en el archivo:
        fwrite($archivo, "<?php\r\n");
        fwrite($archivo, "require __DIR__ . '/vendor/autoload.php';\r\n");
        fwrite($archivo, "use Automattic\WooCommerce\Client;\r\n");
        fwrite($archivo, "$" . "woocommerce = new Client(\r\n");
        fwrite($archivo, "'$url/',  \r\n");
        fwrite($archivo, "'$ckey', \r\n");
        fwrite($archivo, "'$csecret', \r\n");
        fwrite($archivo, "['version' => 'wc/v3', ] \r\n");
        fwrite($archivo, "); \r\n");
        fwrite($archivo, "?>");
        // Fuerza a que se escriban los datos pendientes en el buffer:
        fflush($archivo);

        // Guardar en la Base de datos
        echo '<script> Swal.fire({ icon: "success", title: "Perfecto!", text: "Listo. Ahora ya puede usar la DMZ",showConfirmButton: false, timer: 3000,});</script>';
    }
    // Cerrar el archivo:
    fclose($archivo);
}

$tb_woo_local = "{$wpdb->prefix}woocommerce_api_keys";
$query_select = "SELECT * FROM $tb_woo_local";
$list_result  = $wpdb->get_results($query_select, ARRAY_A);

if (empty($list_result)) {
    $list_result = array();
}

$tb_woo_users     = "{$wpdb->prefix}users";
$query_select_usr = "SELECT * FROM $tb_woo_users";
$list_result_usr  = $wpdb->get_results($query_select_usr, ARRAY_A);

if (empty($list_result_usr)) {
    $list_result_usr = array();
}


?>

<style>
    .color-text-woo {
        font-weight: bold;
        color: #50575e;
    }
</style>
<div class="wrap">


    <div class="border bg-light text-dark" style="padding: 10px 20px 15px;">
        <?php
        echo '<h1 class="wp-heading-inline">' . get_admin_page_title() . '</h1>';
        ?>
        <a id="idAPICreate" class="page-title-action">Crear API Key</a>
        <a id="idAPIUpdate" class="page-title-action">Ingresar API Key</a>
    </div>


    <br>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <table class="wp-list-table widefat fixed striped pages" id="tblListUpdateAPI">
                    <thead class="table-dark" style="background-color: #3c434a;">

                        <th style="color: #FFF; text-align: center;" data-priority="1" width="2%">ID</th>
                        <th style="color: #FFF;">DESC</th>
                        <th style="color: #FFF;">PERMISOS</th>
                        <th style="color: #FFF;">CONSUMER KEY</th>
                        <th style="text-align: center; color: #FFF;">CONSUMER SECRET</th>
                        <th style="text-align: center; color: #FFF;">Fecha</th>
                        <th style="color: #FFF; text-align: center;" width="5%">ACCIONES</th>

                    </thead>
                    <tbody id="the-list">
                        <?php
                        foreach ($list_result as $key => $value) {

                            $apiId       = $value['key_id'];
                            $apiDesc     = $value['description'];
                            $apiPermisos = $value['permissions'];
                            $apiCK       = ". . . . . . .  " . $value['truncated_key'];
                            $apiCS       = substr($value['consumer_secret'], 0, 7) . " . . . . . . .";
                            $apiFecha    = $value['last_access'];

                            echo "
                                <tr>
                                    <td style='text-align: center;'> $apiId </td>
                                    <td> $apiDesc </td>     
                                    <td> $apiPermisos </td>
                                    <td> $apiCK </td>
                                    <td> $apiCS </td>
                                    <td  style='text-align: center;'> $apiFecha </td> 
                                    <td>
                                    <center>
                                    <a class='btn btn-outline-info btn-sm'><i class='fa-solid fa-eye'></i></a>
                                    </td>
                                 </tr>";
                        } ?>
                    </tbody>

                </table>

            </div>
        </div>
    </div>


    <!-- Modal UPDATE-->

    <div class="modal fade mt-5" id="mdEditarAPI" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">

        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post">
                    <div class="modal-body">
                        <div class="alert alert-primary" role="alert" style="padding: 15px 10px 0px 10px;">
                            <p>Copia y pega las claves generadas desde Woocommers. <a id="btnTutorial" class="pe-auto" style="float: right; cursor: pointer;"> Ver como </a> </p>
                        </div>
                        <div id="ImgContenedorDemo" style="padding: 10px; background-color: whitesmoke; border-radius: 5px; display: none;">
                            <button type="button" id="btnClosed" class="btn btn-outline-danger btn-sm mb-2" style="float: right;"><i class="fa-solid fa-x fa-sm"></i></button>

                            <video id="myVideo" src="<?php echo plugin_dir_url(__FILE__) . '/img/local/video-tutorial.mp4'; ?>" controls type="video/mp4" loop="loop"> Vídeo no es soportado... </video>
                        </div>

                        <div class="row mt-2" style="padding: 0px 10px 0px 10px;">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="frCK" class="form-label text-uppercase color-text-woo">Dominio </label>
                                    <input type="text" class="form-control" id="id_Dominio" name="id_Dominio" placeholder="www.midominio.com" required style="border-color: #0d96fd; box-shadow: 0 0 0 0.2rem rgb(13 110 253 / 45%);">
                                    <div id="txtcupon" class="form-text">Verifique si el dominio es correcto.</div>
                                </div>
                            </div>
                        </div>

                        <div class="row" style="padding: 0px 10px 0px 10px;">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="frCK" class="form-label text-uppercase color-text-woo">Consumer key </label>
                                    <input type="text" class="form-control" id="id_CKey" name="id_CKey" placeholder="ck_xxxxxxxxxxxxxxxxxxxxxxxxxxxx" required>
                                    <div id="txtcupon" class="form-text">Ingrese el Consumer key.</div>
                                </div>
                            </div>
                        </div>

                        <div class="row" style="padding: 0px 10px 0px 10px;">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="frCS" class="form-label text-uppercase color-text-woo">Consumer secret </label>
                                    <input type="text" class="form-control" id="id_CSecret" name="id_CSecret" placeholder="cs_xxxxxxxxxxxxxxxxxxxxxxxxxxxx" required>
                                    <div id="txtcupon" class="form-text">Ingrese el Consumer secret.</div>
                                </div>
                            </div>
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" name="btnUpdateWoocommers">Activar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!------MODAL CREAR API------->
    <div class="modal fade mt-5" id="mdCrearAPI" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">

        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post">
                    <div class="modal-body">
                        <div class="alert alert-primary" role="alert" style="padding: 15px 10px 0px 15px;">
                            <p>Complete y valide el formulario para crear automaticamente el API - Key </p>
                        </div>

                        <div class="row mt-2" style="padding: 0px 10px 0px 10px;">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="frDomain" class="form-label text-uppercase color-text-woo">Dominio </label>
                                    <input type="text" class="form-control" id="id_CreDominio" name="id_CreDominio" placeholder="www.midominio.com" required>
                                    <div id="txtcupon" class="form-text">Verifique si el dominio es correcto.</div>
                                </div>
                            </div>
                        </div>

                        <div class="row" style="padding: 0px 10px 0px 10px;">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="frCS" class="form-label text-uppercase color-text-woo">Usuario </label>
                                    <select class="form-control" style="max-width: 100% !important;" name="txtUsuario">
                                        <option value="" selected>Seleccione:</option>
                                        <?php
                                        foreach ($list_result_usr as $key => $value) {
                                            $usr_id    = $value['ID'];
                                            $usr_nice  = $value['user_nicename'];
                                            $usr_email = $value['user_email'];

                                            echo "<option value='$usr_id'>$usr_nice / $usr_email</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row" style="padding: 0px 10px 0px 10px;">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="frCS" class="form-label text-uppercase color-text-woo"> Permiso (s)</label>
                                    <select class="form-control" style="max-width: 100% !important;" name="txtPermiso">
                                        <option value="" selected>Seleccione:</option>
                                        <option value="read_write">Lectura & Escritura</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row" style="padding: 0px 10px 0px 10px;">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="frDescripcion" class="form-label text-uppercase color-text-woo">Descripción </label>
                                    <textarea name="txtDescripcion" class="form-control" placeholder="Ingrese la descripción"></textarea>
                                </div>
                            </div>
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" name="btnCreateAPIWoocommers">Activar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>