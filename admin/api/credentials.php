<?php
require __DIR__ . '/vendor/autoload.php';
use Automattic\WooCommerce\Client;
$woocommerce = new Client(
'http://woocomers-llantaspe.dev/',  
'ck_1f85d6df2bec1cc7889e168dfc42f7b2cf6b2973', 
'cs_46a93f0a06e97ab31d2f14803dfa84ccd5b9d2a6', 
['version' => 'wc/v3', ] 
); 
?>