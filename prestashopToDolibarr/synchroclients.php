<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include('synchro1client.php');

$validated = Configuration::get('validated');
if ($validated == 0) {
	echo "PrestashopToDolibarr module is not properly configured (probably server url, login or password). Glease go to module configuration page to fix it.";
	return;
}

if (version_compare(Configuration::get('dolibarr_version'), '3.6.3') == -1) {
		echo "<br />Your version of Dolibarr can't synchronize clients properly. Please consider updating Dolibarr to 3.6.3 or higher to have a full synchronisation.";
		return;
}

// action if reset synchronization
if (Tools::isSubmit('action')) {
    $action=Tools::getValue('action');
    if ($action == "reset") {
		Configuration::updateValue('clients_last_synchro', "1970-01-01 00:00:00");
	}
}
    
$last_synchro = Configuration::get('clients_last_synchro');
echo "Synchronisation of clients begins for modification since ".$last_synchro."<br>";

$failed_number = 0;

$sql="select id_customer from "._DB_PREFIX_."customer where date_upd > '".$last_synchro."'";
if ($results = Db::getInstance()->ExecuteS($sql))
{
    foreach ($results as $row)
    {
        $id_customer=$row['id_customer'];
        $hasSucceded = synchroClient($id_customer);
        if (!$hasSucceded)
        {
			$failed_number++;
		}
    }
}

echo "Synchronisation of clients done : ". $failed_number . "error(s)<br>";
$time = new DateTime('NOW');
Configuration::updateValue('clients_last_synchro',  $time->format("Y-m-d H:i:s"));

?>
