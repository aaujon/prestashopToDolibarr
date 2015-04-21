<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include('synchro1order.php');

// action if reset synchronization
if (Tools::isSubmit('action')) {
    $action=Tools::getValue('action');
    if ($action == "reset") {
		Configuration::updateValue('orders_last_synchro', "1970-01-01 00:00:00");
	}
}

$last_synchro = Configuration::get('orders_last_synchro');
echo "Synchronisation of orders begins for modification since ".$last_synchro."<br>";


$sql="select * from "._DB_PREFIX_."orders where date_upd > '".$last_synchro."'";
$anErrorOccured = false;
if ($results = Db::getInstance()->ExecuteS($sql))
    foreach ($results as $row)
    {
        $id_order=$row['id_order'];
        echo "Synchronize order : $id_order";
        $isOk =synchroOrder($id_order);
        if (!$isOk) {
			$anErrorOccured = true;
			echo "Error<br/>";
		} else {
			echo "OK<br/>";
		}
		
    }

echo "Synchronisation of orders done<br>";
if ($anErrorOccured)
{
	echo "Some synchronisation failed, check log<br>";
}
else 
{
	$time = new DateTime('NOW');
	Configuration::updateValue('orders_last_synchro',  $time->format("Y-m-d H:i:s"));
}

?>
