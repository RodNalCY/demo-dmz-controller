<?php

/**
 * Plugin Name:       Demo - DMZ
 * Plugin URI:        https://example.com/plugins/demo-dmz/
 * Description:       Demo para gestionar la DMZ.
 * Version:           0.0.1
 * Author:            RodNal CY
 * License:           GPL v2 or later
 */
// Funcion
// Funciones del Plugin 
// Ejecutamos Script de Borrar


function Activar()
{
    global $wpdb;
    ///////////////////////////////////////////////////////////////////////////////////////////////
    // Query de Crear Tabla Productos
    $sql_tb_productos = "CREATE TABLE {$wpdb->prefix}dmz_productos (
        `IdProducto` int(8) UNSIGNED ZEROFILL NOT NULL,
        `Descripcion` varchar(255) DEFAULT '',
        `Stock_Disponible` int(11) NOT NULL DEFAULT 0,
        `Stock_Comprometido` int(11) NOT NULL DEFAULT 0,
        `Stock_Transito` int(11) NOT NULL DEFAULT 0,
        `En_Remate` tinyint(1) NOT NULL DEFAULT 0,
        `Slug` varchar(45) DEFAULT '',
        `Imagen` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_cs DEFAULT NULL,
        `MarcaCodigo` varchar(45) NOT NULL DEFAULT '',
        `IdLinea` varchar(45) NOT NULL DEFAULT '',
        `IdFamilia` varchar(45) NOT NULL DEFAULT '',
        `Stock_Falla` int(11) NOT NULL DEFAULT 0,
        `Estado` varchar(1) NOT NULL DEFAULT 'A',
        `Exclusion` tinyint(1) DEFAULT 0,
        `IdMedida` varchar(45) NOT NULL,
        `IdPrecio` varchar(45) NOT NULL,
        `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
      ) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

    $wpdb->query($sql_tb_productos);
    ///////////////////////////////////////////////////////////////////////////////////////////////
    $sql_tb_variacion = "CREATE TABLE {$wpdb->prefix}variacion_precio (
        `var_id` INT NOT NULL AUTO_INCREMENT,
        `var_id_producto` INT NULL,
        `var_sku_producto` VARCHAR(100) NULL,
        `var_precio_regular` DECIMAL(8,2) NULL,
        `var_precio_venta` DECIMAL(8,2) NULL,
        `var_precio_final` DECIMAL(8,2) NULL,
        `var_porcentaje` DECIMAL(8,2) NULL,
        `var_tiempo` timestamp NULL,
        `var_tipo_tiempo` CHAR(10) NULL,
        `var_id_cron_directory` INT NULL,        
        `var_dir_name_file` VARCHAR(100) NULL,
        `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`var_id`));";
    $wpdb->query($sql_tb_variacion);
    ///////////////////////////////////////////////////////////////////////////////////////////////
    $sql_tb_directory = "CREATE TABLE {$wpdb->prefix}variacion_directorio (
        `dir_id` INT NOT NULL AUTO_INCREMENT,
        `dir_name`   VARCHAR(100) NULL,
        `dir_ruta`   VARCHAR(1000) NULL,
        `cretead_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`dir_id`));";
    $wpdb->query($sql_tb_directory);
    ///////////////////////////////////////////////////////////////////////////////////////////////
    // $triggerIfExits = "drop trigger if exists updateTime;";    
    // $wpdb->query($triggerIfExits);

    // $trigger = "CREATE TRIGGER updateTime 
    //             BEFORE UPDATE ON wp_variacion_precio FOR EACH ROW 
    //             BEGIN 
    //             SET new.var_tiempo = NOW();
    //             END ;";    
    // $wpdb->query($trigger);
    ///////////////////////////////////////////////////////////////////////////////////////////////
    $procedureIfExits = "DROP PROCEDURE IF EXISTS updateTimeCron;";    
    $wpdb->query($procedureIfExits);

    $sql_procedure_time = "CREATE PROCEDURE updateTimeCron(IN v_id INT)
                            BEGIN
                                UPDATE {$wpdb->prefix}variacion_precio SET `var_tiempo` = NOW() WHERE (var_id = v_id);
                            END ; "; 
    $wpdb->query($sql_procedure_time);
    ///////////////////////////////////////////////////////////////////////////////////////////////
    $consumer_key       = 'ck_' . wc_rand_hash();
    $consumer_secret    = 'cs_' . wc_rand_hash();
    $secret_key_hash    = wc_api_hash($consumer_key);
    $seven_truncate_key = substr($consumer_key, -7); 
   
    $host_url        =  "https://".$_SERVER["HTTP_HOST"]."/";  // Solo Desarrollo http:
    $ruta_actual     = __DIR__;
    $barraReplace    = str_replace('\\', "/", $ruta_actual);
    $file_credential = $barraReplace . "/admin/api/credentials.php";

    $archivo = fopen($file_credential, "w+b");
    
    
    if ($archivo == false) {
        echo '<script> Swal.fire({ icon: "error", title: "Error!", text: "La activación salio mal !",showConfirmButton: false, timer: 3000,});</script>';
    } else {
        // Escribir en el archivo:
        fwrite($archivo, "<?php\r\n");
        fwrite($archivo, "require __DIR__ . '/vendor/autoload.php';\r\n");
        fwrite($archivo, "use Automattic\WooCommerce\Client;\r\n");
        fwrite($archivo, "$" . "woocommerce = new Client(\r\n");
        fwrite($archivo, "'$host_url',  \r\n");
        fwrite($archivo, "'$consumer_key', \r\n");
        fwrite($archivo, "'$consumer_secret', \r\n");
        fwrite($archivo, "['version' => 'wc/v3', ] \r\n");
        fwrite($archivo, "); \r\n");
        fwrite($archivo, "?>");
        // Fuerza a que se escriban los datos pendientes en el buffer:
        fflush($archivo);        

        $sql_get_admin    = "SELECT ID FROM {$wpdb->prefix}users WHERE user_nicename='admin' LIMIT 1";
        $sql_admin_result = $wpdb->get_results($sql_get_admin, ARRAY_A);
        $sql_ID = $sql_admin_result[0]['ID'];

        $sql_tb_woo_insert_api_key = "INSERT INTO {$wpdb->prefix}woocommerce_api_keys(
            `user_id`, `description`, `permissions`, `consumer_key`, `consumer_secret`, `truncated_key`) 
            VALUES ('$sql_ID', 'Plugin DMZ Controller 1.0', 'read_write', '$secret_key_hash', '$consumer_secret', '$seven_truncate_key');";

        $wpdb->query($sql_tb_woo_insert_api_key);
    }
    // Cerrar el archivo:
    fclose($archivo);    
    ///////////////////////////////////////////////////////////////////////////////////////////////  

    // if( ! wp_next_scheduled( 'dcms_my_cron_hook' ) ) {
    //     wp_schedule_event( current_time( 'timestamp' ), '5seconds', 'dcms_my_cron_hook' );
    // }

}

function Desactivar()
{
    global $wpdb;
    $sql_delete_tb_productos = "DROP TABLE {$wpdb->prefix}dmz_productos";
    $wpdb->query($sql_delete_tb_productos);

    $sql_delete_tb_variacion = "DROP TABLE {$wpdb->prefix}variacion_precio";
    $wpdb->query($sql_delete_tb_variacion);
    
    $sql_delete_tb_directorio = "DROP TABLE {$wpdb->prefix}variacion_directorio";
    $wpdb->query($sql_delete_tb_directorio);

    $sql_delete_procedure = "DROP PROCEDURE IF EXISTS updateTimeCron;";    
    $wpdb->query($sql_delete_procedure);

    // $trigger_delete = "drop trigger if exists updateTime;";
    // $wpdb->query($trigger_delete);

    // wp_clear_scheduled_hook( 'dcms_my_cron_hook' );
}
function Eliminar()
{
}

//echo "PLUGIN DMZ";

register_activation_hook(__FILE__, 'Activar');
register_deactivation_hook(__FILE__, 'Desactivar');

// Crear Menu y Submenus Options
add_action('admin_menu', 'CrearMenu');
function CrearMenu()
{
    // Main Menu
    add_menu_page(
        'DMZ Dashboard', //Titulo de la Pagina del Menu
        'DMZ', // Titilo del Menu
        'manage_options', //Compatibilidad
        plugin_dir_path(__FILE__) . 'admin/dashboard.php', // Slug Main Menu
        null, // Funcion del contenido    
        plugin_dir_url(__FILE__) . 'admin/img/2.png',
        2 //priority - Position
    );

    //Productos Submenu
    // add_submenu_page(
    //     plugin_dir_path(__FILE__) . 'admin/dashboard.php', // Slug Padre
    //     'Mis Productos Local', //Titulo de la Pagina
    //     'Productos Local', //Titulo del Menu
    //     'manage_options',
    //     plugin_dir_path(__FILE__) . 'admin/list_local.php',
    //     null
    // );

    add_submenu_page(
        plugin_dir_path(__FILE__) . 'admin/dashboard.php', // Slug Padre
        'Mis Productos', //Titulo de la Pagina
        'Productos', //Titulo del Menu
        'manage_options',
        plugin_dir_path(__FILE__) . 'admin/list_productos.php',
        null
    );


    // Cupones Submenu
    add_submenu_page(
        plugin_dir_path(__FILE__) . 'admin/dashboard.php', // Slug Padre
        'Mis Cupones', //Titulo de la Pagina
        'Cupones', //Titulo del Menu
        'manage_options',
        plugin_dir_path(__FILE__) . 'admin/list_cupones.php',
        null
    );

    // Cupones Submenu
    add_submenu_page(
        plugin_dir_path(__FILE__) . 'admin/dashboard.php', // Slug Padre
        'Mis Descuentos', //Titulo de la Pagina
        'Descuentos', //Titulo del Menu
        'manage_options',
        plugin_dir_path(__FILE__) . 'admin/list_descuento.php',
        null
    );

    // Cupones Submenu
    add_submenu_page(
        plugin_dir_path(__FILE__) . 'admin/dashboard.php', // Slug Padre
        'Variación de Precios', //Titulo de la Pagina
        'Variación de Precio', //Titulo del Menu
        'manage_options',
        plugin_dir_path(__FILE__) . 'admin/list_variacion_precios.php',
        null
    );
    add_submenu_page(
        plugin_dir_path(__FILE__) . 'admin/dashboard.php', // Slug Padre
        'Update API Key', //Titulo de la Pagina
        'Actualizar API Key', //Titulo del Menu
        'manage_options',
        plugin_dir_path(__FILE__) . 'admin/validate_update.php',
        null
    );

 
    // Categorias Submenu
    //add_submenu_page(
    //    'gt-dmz', //Menu Padre
    //    'Mis Categorias', //Titulo de la Pagina
    //    'Categorias', //Titulo del Menu
    //    'manage_options',
    //    'gt-menu-categorias',
    //    'menuCategorias'
    //);
    // Ajustes Submenu
    //add_submenu_page(
    //    'gt-dmz', //Menu Padre
    //    'Mis Ajustes', //Titulo de la Pagina
    //    'Ajustes', //Titulo del Menu
    //    'manage_options',
    //    'gt-menu-ajustes',
    //    'menuAjustes'
    //);
}

// function MostrarContenido(){
//    echo "<h1>Administrador de la DMZ</h1>";
// }

// function menuProductos(){
//     echo "<h1>Menu de Productos</h1>";
// }

// function menuCategorias(){
//     echo "<h1>Menu de Categorias</h1>";
// }

// function menuAjustes(){
//     echo "<h1>Menu de Ajustes</h1>";
// } 

// AÑADIR BOOTSTRAP JS
function AddBootstrapJS($hook)
{
    // OJO : Delimitamos el uso bootstrap a nuestro plugin
    //echo "<script> console.log('$hook') </script>"; 
    // if($hook != "demo-dmz-controller/admin/my-archivo.php"){
    //     return;
    // }
    // Añadimos JS Bootstrap
    wp_enqueue_script('bootstrapJS', plugins_url('admin/js/bootstrap.js', __FILE__), array('jquery'));
}
add_action('admin_enqueue_scripts', 'AddBootstrapJS');

// AÑADIR BOOTSTRAP CSS
function AddBootstrapCSS($hook)
{
    // OJO : Delimitamos el uso bootstrap a nuestro plugin
    // if($hook != "demo-dmz-controller/admin/my-archivo.php"){
    //     return;
    // }
    // Añadimos CSS Bootstrap
    wp_enqueue_style('bootstrapJS', plugins_url('admin/css/bootstrap.min.css', __FILE__));
}
add_action('admin_enqueue_scripts', 'AddBootstrapCSS');

// AÑADIR SWEETALERT
function AddSweetalertJS($hook)
{
    // OJO : Delimitamos el uso bootstrap a nuestro plugin
    // if($hook != "demo-dmz-controller/admin/my-archivo.php"){
    //     return;
    // }
    // Añadimos CSS Bootstrap
    wp_enqueue_script('sweetalertJS', plugins_url('admin/js/sweetalert2.all.min.js', __FILE__));
}
add_action('admin_enqueue_scripts', 'AddSweetalertJS');

// AÑADIR POPPER
function AddPopperJS($hook)
{
    // OJO : Delimitamos el uso bootstrap a nuestro plugin
    // if($hook != "demo-dmz-controller/admin/my-archivo.php"){
    //     return;
    // }
    // Añadimos CSS Bootstrap
    wp_enqueue_script('popperJS', plugins_url('admin/js/popper.min.js', __FILE__));
}
add_action('admin_enqueue_scripts', 'AddPopperJS');

// AÑADIR Bootstrap Bundle
function AddBootsBundleJS($hook)
{
    // OJO : Delimitamos el uso bootstrap a nuestro plugin
    // if($hook != "demo-dmz-controller/admin/my-archivo.php"){
    //     return;
    // }
    // Añadimos CSS Bootstrap
    wp_enqueue_script('bootstrapBundleJS', plugins_url('admin/js/bootstrap.bundle.min.js', __FILE__));
}
add_action('admin_enqueue_scripts', 'AddBootsBundleJS');

// AÑADIR AddFontAwesomeJS 
function AddFontAwesomeJS($hook)
{
    // OJO : Delimitamos el uso bootstrap a nuestro plugin
    // if($hook != "demo-dmz-controller/admin/my-archivo.php"){
    //     return;
    // }
    // Añadimos CSS Bootstrap
    wp_enqueue_script('fontAwesomeJS', plugins_url('admin/js/fontawesome.js', __FILE__));
}
add_action('admin_enqueue_scripts', 'AddFontAwesomeJS');

// AÑADIR SCRIPT LOCAL PRODUCTO
function AddLocalJS($hook)
{

    if ($hook != "demo-dmz-controller/admin/list_local.php") {
        return;
    }
    // Añadimos JS
    wp_enqueue_script('JsLocal', plugins_url('admin/js/local/local.js', __FILE__), array('jquery'));
    wp_localize_script('JsLocal', 'SolicitudesAjax', [
        'url' => admin_url('admin-ajax.php'),
        'seguridad' => wp_create_nonce('seg')
    ]);
}
add_action('admin_enqueue_scripts', 'AddLocalJS');


function AddLocalProductJS($hook)
{

    if ($hook != "demo-dmz-controller/admin/list_productos.php") {
        return;
    }
    // Añadimos JS
    wp_enqueue_script('JsLocalProduct', plugins_url('admin/js/local/products.js', __FILE__), array('jquery'));
    wp_localize_script('JsLocalProduct', 'SolicitudesAjax', [
        'url' => admin_url('admin-ajax.php'),
        'seguridad' => wp_create_nonce('seg')
    ]);
}
add_action('admin_enqueue_scripts', 'AddLocalProductJS');



// AÑADIR SCRIPT LOCAL CUPON
function AddLocalCuponsJS($hook)
{

    if ($hook != "demo-dmz-controller/admin/list_cupones.php") {
        return;
    }
    // Añadimos JS
    wp_enqueue_script('JsLocalCupons', plugins_url('admin/js/local/cupons.js', __FILE__), array('jquery'));
    wp_localize_script('JsLocalCupons', 'SolicitudesAjax', [
        'url' => admin_url('admin-ajax.php'),
        'seguridad' => wp_create_nonce('seg')
    ]);
}
add_action('admin_enqueue_scripts', 'AddLocalCuponsJS');

// AÑADIR SCRIPT LOCAL CUPON
function AddLocalDescuentoJS($hook)
{

    if ($hook != "demo-dmz-controller/admin/list_descuento.php") {
        return;
    }
    // Añadimos JS
    wp_enqueue_script('JsLocalDescuento', plugins_url('admin/js/local/descuento.js', __FILE__), array('jquery'));
}
add_action('admin_enqueue_scripts', 'AddLocalDescuentoJS');

// AÑADIR SCRIPT LOCAL CUPON
function AddLocalVariacionPrecioJS($hook)
{

    if ($hook != "demo-dmz-controller/admin/list_variacion_precios.php") {
        return;
    }
    // Añadimos JS
    wp_enqueue_script('JsLocalVariacion', plugins_url('admin/js/local/variacion.js', __FILE__), array('jquery'));
    wp_localize_script('JsLocalVariacion', 'SolicitudesAjax', [
        'url' => admin_url('admin-ajax.php'),
        'seguridad' => wp_create_nonce('seg')
    ]);
}
add_action('admin_enqueue_scripts', 'AddLocalVariacionPrecioJS');

// AÑADIR SCRIPT LOCAL CUPON
function AddLocalGlobalJS($hook)
{
    // Añadimos JS
    wp_enqueue_script('JsLocalGlobal', plugins_url('admin/js/local/global.js', __FILE__), array('jquery'));
}
add_action('admin_enqueue_scripts', 'AddLocalGlobalJS');



// AÑADIR CSS DATATABLES
function AddDataTablesCSS($hook)
{
    wp_enqueue_style('DataTableCSS', plugins_url('admin/css/dataTables.bootstrap4.min.css', __FILE__));
}
add_action('admin_enqueue_scripts', 'AddDataTablesCSS');
// AÑADIR JS DATATABLES
function AddDataTableJsJQuery($hook)
{
    // Añadimos JS
    wp_enqueue_script('JsDTableJQuery', plugins_url('admin/js/jquery.dataTables.min.js', __FILE__));
}
add_action('admin_enqueue_scripts', 'AddDataTableJsJQuery');

function AddDataTableJsBootstrap($hook)
{
    // Añadimos JS
    wp_enqueue_script('JsDTableBootstrap', plugins_url('admin/js/dataTables.bootstrap4.min.js', __FILE__));
}
add_action('admin_enqueue_scripts', 'AddDataTableJsBootstrap');

function AddDataTableJsResponsive($hook)
{
    // Añadimos JS
    wp_enqueue_script('JsDTableResponsive', plugins_url('admin/js/dataTables.responsive.min.js', __FILE__));
}
add_action('admin_enqueue_scripts', 'AddDataTableJsResponsive');

function AddInfinityScrollCSS($hook)
{
    wp_enqueue_style('InfinityScrollCSS', plugins_url('admin/css/scroll-infinity.css', __FILE__));
}
add_action('admin_enqueue_scripts', 'AddInfinityScrollCSS');

function AddGoogleCharts($hook)
{
    // Añadimos JS
    wp_enqueue_script('JsGoogleCharts', plugins_url('admin/js/loader.js', __FILE__));
}
add_action('admin_enqueue_scripts', 'AddGoogleCharts');

function AddLocalJSDashboard($hook)
{

    if ($hook != "demo-dmz-controller/admin/dashboard.php") {
        return;
    }

    // Añadimos JS
    wp_enqueue_script('JsLocalDashboard', plugins_url('admin/js/local/dashboard.js', __FILE__), array('jquery'));
    wp_localize_script('JsLocalDashboard', 'SolicitudesAjax', [
        'url' => admin_url('admin-ajax.php'),
        'seguridad' => wp_create_nonce('seg')
    ]);
}
add_action('admin_enqueue_scripts', 'AddLocalJSDashboard');

// Eliminar Producto ID
function EliminarProductoID()
{
    // Verificar si el token es compatible
    $nonce = $_POST['nonce'];
    if (!wp_verify_nonce($nonce, 'seg')) {
        die("No tiene permiso para ejecutar Ajax - Token Incorrecto");
    }

    // Ejecutamos Script de Borrar
    global $wpdb;

    $d_id = $_POST['id'];
    $tblProductos = "{$wpdb->prefix}dmz_productos";
    $wpdb->delete($tblProductos, array('IdProducto' => $d_id));
    return true;
}
add_action('wp_ajax_peticionEliminar', 'EliminarProductoID');


function peticionEliminarCupon()
{
    // Verificar si el token es compatible
    include_once("admin/api/credentials.php");
    include_once("admin/api/cors.php");

    $nonce = $_POST['nonce'];
    if (!wp_verify_nonce($nonce, 'seg')) {
        die("No tiene permiso para ejecutar Ajax - Token Incorrecto");
    }

    $c_Id = $_POST['id'];
    $woocommerce->delete('coupons/' . $c_Id, ['force' => true]);

    return true;
}
add_action('wp_ajax_peticionEliminarCupon', 'peticionEliminarCupon');


function peticionEliminarProducto()
{
    // Verificar si el token es compatible
    include_once("admin/api/credentials.php");
    include_once("admin/api/cors.php");

    $nonce = $_POST['nonce'];
    if (!wp_verify_nonce($nonce, 'seg')) {
        die("No tiene permiso para ejecutar Ajax - Token Incorrecto");
    }

    $p_Id = $_POST['id'];
    $woocommerce->delete('products/' . $p_Id, ['force' => true]);

    return true;
}
add_action('wp_ajax_peticionEliminarProducto', 'peticionEliminarProducto');


function peticionActivarWoocommers()
{
}
add_action('wp_ajax_peticionActivarWoocommers', 'peticionActivarWoocommers');


function AddUpdateAPILocaJS($hook)
{

    if ($hook != "demo-dmz-controller/admin/validate_update.php") {
        return;
    }
    // Añadimos JS
    wp_enqueue_script('JsLocalAPIJS', plugins_url('admin/js/local/validate_api.js', __FILE__), array('jquery'));
    wp_localize_script('JsLocalAPIJS', 'SolicitudesAjax', [
        'url' => admin_url('admin-ajax.php'),
        'seguridad' => wp_create_nonce('seg')
    ]);
}
add_action('admin_enqueue_scripts', 'AddUpdateAPILocaJS');


function peticionModalAPIUpdate()
{
    // Verificar si el token es compatible    
    $nonce = $_POST['nonce'];
    if (!wp_verify_nonce($nonce, 'seg')) {
        die("No tiene permiso para ejecutar Ajax - Token Incorrecto");
    }
    include 'validate_html.php';
}
add_action('wp_ajax_peticionModalAPIUpdate', 'peticionModalAPIUpdate');


// funciones para añadir el multiselect
// function bootstrapMultiselectJS($hook)
// {

//     // if ($hook != "demo-dmz-controller/admin/validate_update.php") {
//     //     return;
//     // }
//     // Añadimos JS
//     wp_enqueue_script('JSBootsMultiSelect', plugins_url('admin/js/BsMultiSelect.min.js', __FILE__));
// }
// add_action('admin_enqueue_scripts', 'bootstrapMultiselectJS');

// function bootstrapMinJS($hook)
// {

//     // if ($hook != "demo-dmz-controller/admin/validate_update.php") {
//     //     return;
//     // }
//     // Añadimos JS
//     wp_enqueue_script('JSMinBootstrap', plugins_url('admin/js/bootstrap.min.js', __FILE__), array('jquery'));
// }
// add_action('admin_enqueue_scripts', 'bootstrapMinJS');


// Activación del Plugin
// register_activation_hook( __FILE__, 'dcms_plugin_activation' );
// function dcms_plugin_activation() {
//     if( ! wp_next_scheduled( 'dcms_my_cron_hook' ) ) {
//         wp_schedule_event( current_time( 'timestamp' ), '5seconds', 'dcms_my_cron_hook' );
//     }
// }

// Desactivación del plugin
// register_deactivation_hook( __FILE__, 'dcms_plugin_desativation' );
// function dcms_plugin_desativation() {
//     wp_clear_scheduled_hook( 'dcms_my_cron_hook' );
// }

// Acción personalizada
// add_action( 'dcms_my_cron_hook', 'dcms_my_process' );
// function dcms_my_process() {
// 	error_log('Mi evento se ejecutó: '.Date("h:i:sa"));
// }


// //Registro de intervalos 
// add_filter( 'cron_schedules', 'dcms_my_custom_schedule');
// function dcms_my_custom_schedule( $schedules ) {
//      $schedules['5seconds'] = array(
//         'interval' => 5,
//         'display' =>__('5 seconds','dcms_lang_domain')
//      );
//      return $schedules;
// }

//FUNCION PARA LIBERAR TODOS LOS PRODUCTOS DE WOOCOMERS
function maximum_api_filter($query_params) {
    $query_params['per_page']['maximum'] = 10000;
    $query_params['per_page']['default'] = 500;
    return $query_params;
}
add_filter('rest_product_collection_params', 'maximum_api_filter', 10, 1 );


function peticionEliminarVariacion()
{
    global $wpdb;
    $tbl_variacion = "{$wpdb->prefix}variacion_precio";

    $nonce = $_POST['nonce'];
    if (!wp_verify_nonce($nonce, 'seg')) {
        die("No tiene permiso para ejecutar Ajax - Token Incorrecto");
    }

    $d_Id   = $_POST['id'];
    $d_file = $_POST['file'];

    if (unlink($d_file)) {
        //echo "Eliminado";
        $wpdb->delete($tbl_variacion, array('var_id' => $d_Id));
    } else {
        //echo "No se Eliminado";
    }   
    return true;
}
add_action('wp_ajax_peticionEliminarVariacion', 'peticionEliminarVariacion');