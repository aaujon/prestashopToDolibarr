<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include('../../config/config.inc.php');
include('stringUtils.php');
include('dolibarr/DolibarrApi.php');

function synchroClient($id_customer)
{
	echo "<br/>Synchronisation client : $id_customer<br>"; 

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
	} elseif ($id_gender==3) {
		$civilite="MME";
	} else {
		$civilite="MR";
	}
	$mail=$donnees_customer['email'];
	echo "Email : $mail<br>";

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
	//$client->phone = $phone;
	//$client->address = $address1." ".$address2;
	//$client->town = $city;
	//$client->zip = $postcode;
	//$client->country_id = $country;
	$client->date_modification = new DateTime('NOW');
	$client->url = _PS_BASE_URL_.__PS_BASE_URI__;

	if ($exists["result"]->result_code == 'NOT_FOUND')
    {
		// Create new user
		echo "Create new user :";
		$result = $dolibarr->createUser($client);
		
		if ($result["result"]->result_code != 'OK')
        {
			echo "FAILED<br>Erreur de synchronisation : ".$result["result"]->result_label;
		}
		else
		{
			echo "OK<br>";
		}
		var_dump($result);
	} else
    {
		// Update user
		echo "update user : ";
		$oldClient = $exists["thirdparty"];
		$client->id = $oldClient->id;
		echo $client->id . "";
		$result = $dolibarr->updateUser($client);
		if ($result["result"]->result_code != 'OK')
        {
			echo "FAILED<br>Erreur de synchronisation : ".$result["result"]->result_label;
		}
		else
		{
			echo "OK<br>";
		}
		var_dump($result);

	}
	
	if ($result["result"]->result_code == 'OK')
	{
		// synchronize client addresses
		if ($addresses = Db::getInstance()->ExecuteS("select * from "._DB_PREFIX_."address where id_customer='".$id_customer."'"))
		{
			foreach ($addresses as $address)
			{
				echo "<br/> Synchronize address : ";
				$contact = new DolibarrContact();
				$contact->ref_ext= $address['id_address'];
				$contact->socid = $result["id"];
				$contact->lastname = $address['lastname'];
				$contact->firstname = $address['firstname'];
				$address1=$address['address1'];
				$address1= accents_majuscules("$address1");
				$address2=$address['address2'];
				$address2= accents_majuscules("$address2");
				$contact->address = $address1.' '.$address2;		
				$contact->zip =$address['postcode'];
				$contact->town = accents_majuscules($address['city']);
				$contact->note = $address['other'];
				
				//TODO improve country correspondance
				$id_country=$address['id_country'];
				if ($id_country == 8) {
					$country = 1; // for FRANCE
				} else {
					$country = "";
				}
				
				$contact->country_id = $country;
				
				$phone = $address['phone'];
				$phone = tel_cacateres("$phone");

				$mobile=$address['phone_mobile'];
				$mobile = tel_cacateres("$mobile");

				$contact->phone_perso = $phone;
				$contact->phone_mobile = $mobile;
				$contact->email = $mail;
				$contact->birthday = $birthday;
				$contact->civility_id = $civilite;
				//public $phone_pro;
				//public $fax;
						
				//public $code;
				
				/*public $default_lang;
				public $no_email;
				public $ref_facturation;
				public $ref_contrat;
				public $ref_commande;
				public $ref_propal;
				public $user_id;
				public $user_login;*/
				echo "Get Contact :".$contact->ref_ext."<br>";
				
				$result = $dolibarr->getContact($contact->ref_ext);
				var_dump($result);
				if ($result["result"]->result_code == 'NOT_FOUND')
				{
					// Create address
					echo "<br>create address <br>";
					$result = $dolibarr->createContact($contact);
					var_dump($result);
					if ($result["result"]->result_code != 'OK')
					{
						echo "Erreur de synchronisation address : ".$result["result"]->result_label;
					}
				} else
				{
					// Update address
					echo "<br>update address <br>";
					$contact->id = $result["contact"]->id; // we can't update contact using it's ref_ext so we use id
					
					$result = $dolibarr->updateContact($contact);
                    var_dump($result);
					if ($result["result"]->result_code != 'OK')
					{
						echo "Erreur de synchronisation address : ".$result["result"]->result_label;
					}
				}
			}
		}
	}
}

if (Tools::isSubmit('id_customer'))
{
    $id_customer=Tools::getValue('id_customer');
    synchroClient($id_customer);
}
?>
