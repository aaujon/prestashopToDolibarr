<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include('synchro1product.php');


$validated = Configuration::get('validated');
if ($validated == 0) {
	echo "PrestashopToDolibarr module is not properly configured (probably server url, login or password). Glease go to module configuration page to fix it.";
	return;
}

// action if reset synchronization
if (Tools::isSubmit('action')) {
    $action=Tools::getValue('action');
    if ($action == "reset") {
		Configuration::updateValue('products_last_synchro', "1970-01-01 00:00:00");
	}
}

$last_synchro = Configuration::get('products_last_synchro');
echo "Synchronisation of products begins for modification since ".$last_synchro."<br>";

$failed_number = 0;

$sql="select id_product from "._DB_PREFIX_."product where date_upd > '".$last_synchro."'";
if ($results = Db::getInstance()->ExecuteS($sql))
{
    foreach ($results as $row)
    {
        $id_product=$row['id_product'];
        $hasSucceded = synchroProduct($id_product);
        if (!$hasSucceded)
        {
			$failed_number++;
		}
        
    }
}

echo "Synchronisation of products done : ". $failed_number . "error(s)<br>";
$time = new DateTime('NOW');
Configuration::updateValue('products_last_synchro',  $time->format("Y-m-d H:i:s"));
?>
