<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include('synchro1product.php');

echo "Synchronisation of products begins<br>";

$sql="select * from "._DB_PREFIX_."product where true";
$results = Db::getInstance()->ExecuteS($sql);
foreach ($results as $row)
{
    $id_product=$row['id_product'];
    echo "Synchronize product : $id_product";
    synchroProduct($id_product);
}

echo "Synchronisation of products done<br>";

?>
