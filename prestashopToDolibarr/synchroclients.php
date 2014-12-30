<?php
include('synchro1client.php');

echo "Synchronisation of clients begins<br>";

$sql="select * from "._DB_PREFIX_."customer where true";
$results = Db::getInstance()->ExecuteS($sql);
foreach ($results as $row)
{
    $id_customer=$row['id_customer'];
    echo "Synchronize client : $id_customer";
    synchroClient($id_customer);
}

echo "Synchronisation of clients done<br>";

?>
