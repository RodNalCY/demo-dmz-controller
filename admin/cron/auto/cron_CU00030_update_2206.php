<?php
include_once('../../api/credentials.php'); 
include_once('../../api/cors.php'); 
include('../../../../../../wp-load.php'); 
global $wpdb; 
$tbl_variacion = "{$wpdb->prefix}variacion_precio";
$v_id                = 4; 
$v_id_producto       = 2206;
$query_variacion     = 'SELECT * FROM '. $tbl_variacion .' WHERE var_id='.$v_id; 
$list_result         = $wpdb->get_results($query_variacion, ARRAY_A); 
$v_porcentaje        = $list_result[0]['var_porcentaje']; 
$v_precio_final      = $list_result[0]['var_precio_final']; 
$v_precio_modificado = $v_precio_final - 20; 
$data_local = ['var_precio_final'   => $v_precio_modificado];
$data_id = ["var_id" => $v_id];$update = $wpdb->update($tbl_variacion, $data_local, $data_id);
$queryProcedure = "CALL updateTimeCron($v_id)";
$wpdb->get_results($queryProcedure, ARRAY_A); 
?>
