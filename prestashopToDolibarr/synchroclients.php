<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include('synchro1client.php');

// action if reset synchronization
if (Tools::isSubmit('action')) {
    $action=Tools::getValue('action');
    if ($action == "reset") {
		Configuration::updateValue('clients_last_synchro', "1970-01-01 00:00:00");
	}
}
    
$last_synchro = Configuration::get('clients_last_synchro');
echo "Synchronisation of clients begins for modification since ".$last_synchro."<br>";


$sql="select * from "._DB_PREFIX_."customer where date_upd > '".$last_synchro."'";
if ($results = Db::getInstance()->ExecuteS($sql))
    foreach ($results as $row)
    {
        $id_customer=$row['id_customer'];
        synchroClient($id_customer);
    }

echo "Synchronisation of clients done<br>";
$time = new DateTime('NOW');
Configuration::updateValue('clients_last_synchro',  $time->format("Y-m-d H:i:s"));

?>
