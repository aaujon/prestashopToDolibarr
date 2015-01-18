<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include('synchro1order.php');

$last_synchro = Configuration::get('orders_last_synchro');
echo "Synchronisation of orders begins for modification since ".$last_synchro."<br>";


$sql="select * from "._DB_PREFIX_."orders where date_upd > '".$last_synchro."'";
if ($results = Db::getInstance()->ExecuteS($sql))
    foreach ($results as $row)
    {
        $id_order=$row['id_order'];
        echo "Synchronize product : $id_order";
        synchroOrder($id_order);
    }

echo "Synchronisation of orders done<br>";
Configuration::updateValue('orders_last_synchro',  (new DateTime('NOW'))->format("Y-m-d H:i:s"));

?>
