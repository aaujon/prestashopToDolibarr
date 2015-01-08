<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include('synchro1product.php');

$last_synchro = Configuration::get('products_last_synchro');
echo "Synchronisation of products begins for modification since ".$last_synchro."<br>";


$sql="select * from "._DB_PREFIX_."product where date_upd > '".$last_synchro."'";
if ($results = Db::getInstance()->ExecuteS($sql))
    foreach ($results as $row)
    {
        $id_product=$row['id_product'];
        echo "Synchronize product : $id_product";
        synchroProduct($id_product);
    }

echo "Synchronisation of products done<br>";
Configuration::updateValue('products_last_synchro',  (new DateTime('NOW'))->format("Y-m-d H:i:s"));

?>
