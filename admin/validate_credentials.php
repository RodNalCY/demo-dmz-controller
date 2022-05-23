<?php

/// VERIFICAR
global $wpdb;
$tblWooAPI = "{$wpdb->prefix}woocommerce_api_keys";
$query     = "SELECT * FROM $tblWooAPI";
$verify    = $wpdb->get_results($query, ARRAY_A);

$tblWooAPILocal = "{$wpdb->prefix}woocommerce_api_local";
$queryLocal     = "SELECT * FROM $tblWooAPILocal";
$verifyLocal    = $wpdb->get_results($queryLocal, ARRAY_A);


//////////////////////////////////////////////////
$t1 = json_encode($verifyLocal);
$t2 = json_decode($t1);

$t3 = json_encode($verify);
$t4 = json_decode($t3);

$validar1  = count($t2);
$validar2  = 0;
$msg_global  = "";
$msg_error_1 = '<div class="alert alert-danger" role="alert" style="padding: 5px 0px 5px 65px;"> Por favor, genere la API Key de Woocomers! </div>';
$msg_error_2 = '<div class="alert alert-danger" role="alert" style="padding: 5px 0px 5px 65px;"> Si existe la API Key, ingrese por favor ! </div>';

foreach ($t2 as $l) {
    foreach ($t4 as $w) {
        if ($l->consumer_secret == $w->consumer_secret) {
            $validar2 = 1;
        }
    }
}
//echo $resultado;
/////////////////////////////////////////////////

// Declarar la ruta
if (isset($_POST['btnActivarWoocommers'])) {
    // print_r($_POST);

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
        $nombre   = "woocommerce_api_keys";
        $domain   = $_POST['id_Dominio'];
        $c_key    = substr($_POST['id_CKey'], 0, 7);
        $c_secret = $_POST['id_CSecret'];

        $data = [
            'nombre'          => $nombre,
            'dominio'         => $domain,
            'consumer_key'    => $c_key,
            'consumer_secret' => $c_secret,
          ];


        $wpdb->insert($tblWooAPILocal, $data);

        echo '<script>
            Swal.fire({
            title: "Actualice",
            html: "Recargue la pagina porfavor!",
            icon: "success",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Si, recargar!",
          }).then((result) => { if (result.isConfirmed) { location.reload(); }}); </script> ';
    }
    // Cerrar el archivo:
    fclose($archivo);    
}

?>

<script>
    // var s_global_array_local = [];
    // var s_global_array_woocm = [];
    // jQuery(document).ready(function($) {

    //     s_global_array_local = <?php //echo json_encode($verifyLocal); 
                                    ?>;
    //     s_global_array_woocm = <?php //echo json_encode($verify); 
                                    ?>;

    //     console.log("LOCAL > ", s_global_array_local);
    //     console.log("WOOCM > ", s_global_array_woocm);
    //     var result = "No";

    //     s_global_array_local.forEach(function(l_secret) {
    //         s_global_array_woocm.forEach(function(w_secret) {               

    //             if(l_secret.consumer_secret == w_secret.consumer_secret){
    //                 console.log(w_secret.consumer_secret);
    //                 result = "SIUUUUUUUUUUUU";
    //             }

    //         });
    //     });

    //     console.log(result);


    // });
</script>