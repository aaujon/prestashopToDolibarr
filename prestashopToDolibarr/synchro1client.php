<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include('../../config/config.inc.php');
include('stringUtils.php');
include('dolibarr/DolibarrApi.php');

function synchroClient($id_customer)
{
	echo "Synchronisation client : $id_customer<br>"; 

	// retrieve params
	$prefix_ref_client=Configuration::get('prefix_ref_client');
	$prefix_ref_client = accents_sans("$prefix_ref_client");   
	$client_status=Configuration::get('client_status');                 

	// retrieve client data
	$donnees_customer = Db::getInstance()->GetRow("select * from "._DB_PREFIX_."customer where id_customer='".$id_customer."'");
	//var_dump($donnees_customer);
	$id_gender = $donnees_customer['id_gender'];
	$note = $donnees_customer['note'];   
	$note = accents_minuscules("$note");

	$birthday=$donnees_customer['birthday'];
	if ($id_gender==9) {
		$civilite="";
	} elseif ($id_gender==1) {
		$civilite="MR";
	} elseif ($id_gender==2) {
		$civilite="MME";
	}
	$mail=$donnees_customer['email'];
	echo "Email : $mail<br>";

	// Retrieve client address
	$adresse = Db::getInstance()->GetRow("select * from "._DB_PREFIX_."address where id_customer='".$id_customer."'");
		$address1=$adresse['address1'];
		$address1= accents_majuscules("$address1");

		$address2=$adresse['address2'];
		$address2= accents_majuscules("$address2");
		
		$postcode=$adresse['postcode'];
		$city=$adresse['city'];   
		$city= accents_majuscules("$city");

		$id_country=$adresse['id_country'];
		//TODO improve country correspondance
		if ($id_country == 8) {
			$country = 1; // for FRANCE
		} else {
			$country = "";
		}

		$phone=$adresse['phone'];
		$phone = tel_cacateres("$phone");

		$mobile=$adresse['phone_mobile'];
		if ($mobile != null) {
			// only keep one phone number, mobile is prefered
			$phone = tel_cacateres("$mobile");
		}

	$dolibarr = Dolibarr::getInstance();

	// Check if already exists in Dolibarr
	$exists = $dolibarr->getUser("PSUSER-".$id_customer);
		
	$client = new DolibarrThirdParty();
	$client->ref_ext = "PSUSER-".$id_customer;
	if ($prefix_ref_client == "") {
		$client->customer_code = -1;
	} else {
		$client->customer_code = $prefix_ref_client.$id_customer;
	}

    $client->status = $client_status;
	$client->ref = $donnees_customer['firstname']." ".$donnees_customer['lastname'];
	$client->email = $mail;
	$client->phone = $phone;
	$client->address = $address1." ".$address2;
	$client->town = $city;
	$client->zip = $postcode;
	$client->country_id = $country;
	$client->date_modification = new DateTime('NOW');

	if ($exists["result"]->result_code == 'NOT_FOUND')
    {
		// Create new user
		echo "Create new user : <br>";
		$result = $dolibarr->createUser($client);
		var_dump($result);
		if ($result["result"]->result_code == 'KO')
        {
			echo "Erreur de synchronisation : ".$result["result"]->result_label;
		}
	} else
    {
		// Update user
		echo "update user<br>";
		$oldClient = $exists["thirdparty"];
		$client->id = $oldClient->id;
		$result = $dolibarr->updateUser($client);
		if ($result["result"]->result_code == 'KO')
        {
			echo "Erreur de synchronisation : ".$result["result"]->result_label;
		}
	}	
}

if (Tools::isSubmit('id_customer'))
{
    $id_customer=Tools::getValue('id_customer');
    synchroClient($id_customer);
}
?>
