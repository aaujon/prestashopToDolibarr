<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include('synchro1client.php');

$last_syncro = Configuration::get('clients_last_synchro');
echo "Synchronisation of clients begins for modification since ".$last_synchro."<br>";


$sql="select * from "._DB_PREFIX_."customer where date_upd > '".$last_synchro."'";
if ($results = Db::getInstance()->ExecuteS($sql))
    foreach ($results as $row)
    {
        $id_customer=$row['id_customer'];
        echo "Synchronize client : $id_customer";
        synchroClient($id_customer);
    }

echo "Synchronisation of clients done<br>";
Configuration::updateValue('clients_last_synchro',  (new DateTime('NOW'))->format("Y-m-d H:i:s"));

?>
