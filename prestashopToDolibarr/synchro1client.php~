<?php
$id_customer=$_GET['id_customer'];
ini_set('display_errors', 1);
error_reporting(E_ALL);
	include('../../config/config.inc.php');
	include('stringUtils.php');
	include('dolibarr/DolibarrApi.php');

	echo "synchronisation client : $id_customer<br>"; 
	// RECUPERATION AUTOMATIQUE DE DONNEES
	/*$date_update=date("Y-m-d");
	$heure_update=date("H:i:s");
	$date_update="$date_update $heure_update";
	$date_synchro=(strftime("%A %d %B %Y"));
	$date_synchro=mb_strtoupper($date_synchro);
	$heure_synchro=(strftime("%H:%M:%S"));*/
	//$lang = Db::getInstance()->GetValue("select value from ."_DB_PREFIX_".configuration where name='PS_LANG_DEFAULT'");
	//var_dump($lang);


	// RECUPERATION DES PARAMETRES
	$libelle_port=Configuration::get('libelle_port');
		$chaine=$libelle_port;    
		$chaine= accents_sans("$chaine");   
		$libelle_port=$chaine;
	$code_article_port=Configuration::get('code_article_port');
	$label=Configuration::get('prefix_ref_client');
		$chaine=$label;    
		$chaine= accents_sans("$chaine");   
		$label=$chaine;
	$option_image=Configuration::get('option_image');
	$decremente=Configuration::get('decremente');                            
	$memo_id=Configuration::get('memo_id');                   

	// retrieve client data
	$donnees_customer = Db::getInstance()->GetRow("select * from "._DB_PREFIX_."customer where id_customer='".$id_customer."'");
	var_dump($donnees_customer);
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
		/*$entreprise=$adresse['company'];
		$entreprise = str_replace( "ë", "e", $entreprise);$entreprise = str_replace( "Ë", "E", $entreprise);
		$chaine=$entreprise;    
		$chaine= accents_majuscules("$chaine");   
		$entreprise=$chaine;
		$prenom=$adresse['firstname'];
		$prenom = str_replace( "ë", "e", $prenom);$prenom = str_replace( "Ë", "E", $prenom);
		$chaine=$prenom;    
		$chaine= accents_majuscules("$chaine");
		$prenom=$chaine;
		$nom=$adresse['lastname'];
		$nom = str_replace( "ë", "e", $nom);$nom = str_replace( "Ë", "E", $nom);
		$chaine=$nom;    
		$chaine= accents_majuscules("$chaine");
		$nom=$chaine;*/
		/*if ($entreprise!="") {
			$societe=$entreprise;
			$chaine=$societe;    
			$chaine= accents_majuscules("$chaine");
			$societe=$chaine;
		} elseif ($entreprise=="") {
			$societe="$nom $prenom";
			$chaine=$societe;    
			$chaine= accents_majuscules("$chaine");
			$societe=$chaine;
		}*/
		$address1=$adresse['address1'];
		$address1= accents_majuscules("$address1");

		$address2=$adresse['address2'];
		$address2= accents_majuscules("$address2");
		
		$postcode=$adresse['postcode'];
		$city=$adresse['city'];   
		$city= accents_majuscules("$city");

		$id_country=$adresse['id_country'];
		if ($id_country == 8) {
			$country = 1;
		} else {
			$country = "";
		}

		$phone=$adresse['phone'];
		$phone = tel_cacateres("$phone");

		$mobile=$adresse['phone_mobile'];
		if ($mobile != null) {
			$phone = tel_cacateres("$mobile");
		}

		/*$vat_number=$adresse['vat_number'];
		$date=$adresse['date_add'];
		$date_compte_client=substr($date,2,5);
			$date_compte_client= str_replace( "-", "", $date_compte_client);
		$active=$adresse['active'];
		$deleted=$adresse['deleted'];                                       
		$alias=$adresse['alias'];
		$poste=$alias;
			$poste="($poste)";
			$chaine=$poste;    
			$chaine= accents_minuscules("$chaine");
			$poste=$chaine;
		$emetteur_paiement = "$entreprise $nom";*/


		// CHECK IF ALREADY EXISTS IN DOLIBARR
		$dolibarr = Dolibarr::getInstance();
		var_dump($dolibarr);
		$result = $dolibarr->userExists($id_customer);
		echo "<br>"; 
		var_dump($result["result"]->result_code);
		
		$client = new DolibarrThirdParty();
		$client->ref_ext = $id_customer;
		$client->customer_code = "FV-".$id_customer;
		$client->ref = $donnees_customer['firstname']." ".$donnees_customer['lastname'];
		$client->email = $mail;
		$client->phone = $phone;
		$client->address = $address1." ".$address2;
		$client->town = $city;
		$client->zip = $postcode;
		$client->country_id = $country;
		$client->date_modification = new DateTime('NOW');
			
		if ($result["result"]->result_code == "NOT_FOUND") {
			// CREATE NEW USER
			echo "Create new user<br>";
			$client->date_creation = new DateTime('NOW');
			$result = $dolibarr->createUser($client);
			if ($result["result"]->result_code == "KO") {
				echo "Erreur de synchronisation : ".$result["result"]->result_label;
			}
		} else {
			// UPDATE USER
			echo "update user<br>";
			$oldClient = $result["thirdparty"];
			$client->id = $oldClient->id;
			$result = $dolibarr->updateUser($client);
			if ($result["result"]->result_code == "KO") {
				echo "Erreur de synchronisation : ".$result["result"]->result_label;
			}
		}
		
		/*
		$req_id_user="select max(rowid) from ".$prefix_doli."user";
		$req_id_user=mysql_query($req_id_user);
		$id_user=mysql_result($req_id_user,0,"max(rowid)");
		$id_user=$id_user+1;
		$sql_recup_verif_user="select * from ".$prefix_doli."user where login='noreply@boutique.fr'";
		$result_verif_user = mysql_query($sql_recup_verif_user) or die($sql_recup_verif_user."<br />\n".mysql_error());
		$donnees_verif_user = mysql_fetch_array($result_verif_user);
		$verif_user=$donnees_verif_user['login'];
		if ($verif_user=='noreply@boutique.fr') {
			$info_erreur="Erreur de synchro sur : UPDATE USER DANS DOLIBARR - ID User : $rowid_user";//or die($info_erreur."<br />\n".mysql_error())
			$rowid_user=$donnees_verif_user['rowid'];
			mysql_query ("UPDATE ".$prefix_doli."user set login='noreply@boutique.fr',statut=1 where rowid=$rowid_user")
				or die($info_erreur."<br />\n".mysql_error());
				
	   } else {
			$info_erreur="Erreur de synchro sur : INSERT USER DANS DOLIBARR - ID User : $rowid_user";//or die($info_erreur."<br />\n".mysql_error())
			$rowid_user=$id_user;
			mysql_query ("INSERT INTO ".$prefix_doli."user (rowid,login,statut) VALUES ($rowid_user,'noreply@boutique.fr',1)")
				or die($info_erreur."<br />\n".mysql_error());
		}

		// DETERMINATION ID CLIENT DOLIBARR
		$req_id_client="select max(rowid) from ".$prefix_doli."societe";
		$req_id_client=mysql_query($req_id_client);
		$id_client=mysql_result($req_id_client,0,"max(rowid)");
		$id_client=$id_client+1;
		$sql_recup_verif_client="select * from ".$prefix_doli."societe where email='$mail'";
		$result_verif_client = mysql_query($sql_recup_verif_client) or die($sql_recup_verif_client."<br />\n".mysql_error());
		$donnees_verif_client = mysql_fetch_array($result_verif_client);
		$verif_client=$donnees_verif_client['email'];
		if ($verif_client==$mail)
			{
			$rowid_client=$donnees_verif_client['rowid'];
			}
		else
			{
			$rowid_client=$id_client;
			}

		// RECUPERATION DU MASQUE CODE CLIENT
		$sql_recup_format_code_client="select * from ".$prefix_doli."const where name='SOCIETE_CODECLIENT_ADDON'";
		$result_format_code_client = mysql_query($sql_recup_format_code_client) or die($sql_recup_format_code_client."<br />\n".mysql_error());
		$donnees_format_code_client = mysql_fetch_array($result_format_code_client);
		$format_code_client=$donnees_format_code_client['value'];
		if ($format_code_client=="mod_codeclient_monkey")
			{
			$prefixe_code_client="CU";
			}
		if ($format_code_client=="mod_codeclient_elephant")
			{
			$sql_recup_prefixe_code_client="select * from ".$prefix_doli."const where name='COMPANY_ELEPHANT_MASK_CUSTOMER'";
			$result_prefixe_code_client = mysql_query($sql_recup_prefixe_code_client) or die($sql_recup_prefixe_code_client."<br />\n".mysql_error());
			$donnees_prefixe_code_client = mysql_fetch_array($result_prefixe_code_client);
			$prefixe_code_client=$donnees_prefixe_code_client['value'];
			$nb_apres_coupe = strpos($prefixe_code_client,'{');
			$prefixe_code_client=substr($prefixe_code_client,0,$nb_apres_coupe);
			}
		if ($format_code_client=="mod_codeclient_leopard")
			{
			$prefixe_code_client="";
			}
		$code_client="000000000$rowid_client";
		$code_client=substr($code_client,-6);
		$ref_client="$prefixe_code_client$date_compte_client-";
		$code_client="$ref_client$code_client";

		// CREATION DE LA FICHE SOCIETE
		if ($version_dolibarr<"3.4")
			{                                                                                
			if (($verif_client==$mail) and ($boucle==0))
				{
				$info_erreur="Erreur de synchro sur : UPDATE FICHE SOCIETE ID : $id_customer - NOM : $societe --> Verifiez la fiche client sous PrestaShop";//or die($info_erreur."<br />\n".mysql_error())
				mysql_query ("UPDATE ".$prefix_doli."societe set tms='$date',datec='$date',datea='$date',nom='$societe',code_client='$code_client',address='$champadresse1 $champadresse2',cp='$codepostal',ville='$ville',fk_pays='$country_doli',tel='$tel',fk_typent='8',tva_intra='$vat_number',note='$note',client='1',fk_user_modif=$rowid_user where rowid=$rowid_client") 
					or die($info_erreur."<br />\n".mysql_error());
				}
			if (($verif_client!=$mail) and ($boucle==0))
				{
				$info_erreur="Erreur de synchro sur : INSERT FICHE SOCIETE ID : $id_customer - NOM : $societe --> Verifiez la fiche client sous PrestaShop";//or die($info_erreur."<br />\n".mysql_error())
				mysql_query ("INSERT INTO ".$prefix_doli."societe (rowid,tms,datec,datea,nom,code_client,address,cp,ville,fk_pays,tel,email,fk_typent,tva_intra,note,client,fk_user_creat,fk_user_modif) 
					VALUES ($rowid_client,'$date','$date','$date','$societe','$code_client','$champadresse1 $champadresse2','$codepostal','$ville','$country_doli','$tel','$mail','8','$vat_number','$note','1',$rowid_user,$rowid_user)") 
						or die($info_erreur."<br />\n".mysql_error());
				}
			if ($vat_number=="")
				{
				$info_erreur="Erreur de synchro sur : UPDATE VAT NUMBER du client - ID Client : $rowid_client";//or die($info_erreur."<br />\n".mysql_error())
				mysql_query ("UPDATE ".$prefix_doli."societe set tva_assuj='0' where rowid=$rowid_client")
					or die($info_erreur."<br />\n".mysql_error());
				}
			}    
		if ($version_dolibarr>="3.4")
			{
			if (($verif_client==$mail) and ($boucle==0))
				{
				$info_erreur="Erreur de synchro sur : UPDATE FICHE SOCIETE ID : $id_customer - NOM : $societe --> Verifiez la fiche client sous PrestaShop";//or die($info_erreur."<br />\n".mysql_error())
				mysql_query ("UPDATE ".$prefix_doli."societe set tms='$date',datec='$date',datea='$date',nom='$societe',code_client='$code_client',address='$champadresse1 $champadresse2',zip='$codepostal',town='$ville',fk_pays='$country_doli',phone='$tel',fk_typent='8',tva_intra='$vat_number',note_private='$note',client='1',fk_user_modif=$rowid_user where rowid=$rowid_client") 
					or die($info_erreur."<br />\n".mysql_error());
				}
			if (($verif_client!=$mail) and ($boucle==0))
				{
				$info_erreur="Erreur de synchro sur : INSERT FICHE SOCIETE ID : $id_customer - NOM : $societe --> Verifiez la fiche client sous PrestaShop";//or die($info_erreur."<br />\n".mysql_error())
				mysql_query ("INSERT INTO ".$prefix_doli."societe (rowid,tms,datec,datea,nom,code_client,address,zip,town,fk_pays,phone,email,fk_typent,tva_intra,note_private,client,fk_user_creat,fk_user_modif) 
					VALUES ($rowid_client,'$date','$date','$date','$societe','$code_client','$champadresse1 $champadresse2','$codepostal','$ville','$country_doli','$tel','$mail','8','$vat_number','$note','1',$rowid_user,$rowid_user)") 
						or die($info_erreur."<br />\n".mysql_error());
				}
			if ($vat_number=="")
				{
				$info_erreur="Erreur de synchro sur : UPDATE VAT NUMBER du client - ID Client : $rowid_client";//or die($info_erreur."<br />\n".mysql_error())
				mysql_query ("UPDATE ".$prefix_doli."societe set tva_assuj='0' where rowid=$rowid_client")
					or die($info_erreur."<br />\n".mysql_error());
				}
			}    
		
		// CREATION DE LA FICHE CONTACT
		$req_id_clientpeople="select max(rowid) from ".$prefix_doli."socpeople";
		$req_id_clientpeople=mysql_query($req_id_clientpeople);
		$id_clientpeople=mysql_result($req_id_clientpeople,0,"max(rowid)");
		$id_clientpeople=$id_clientpeople+1;
		$sql_recup_verif_clientpeople="select * from ".$prefix_doli."socpeople where email='$mail' and poste='$poste'";
		$result_verif_clientpeople = mysql_query($sql_recup_verif_clientpeople) or die($sql_recup_verif_clientpeople."<br />\n".mysql_error());
		$donnees_verif_clientpeople = mysql_fetch_array($result_verif_clientpeople);
		$verif_clientpeople=$donnees_verif_clientpeople['email'];
		if ($deleted=='1')
			{
			$nom="__ADRESSE SUPPRIMEE__";
			$prenom="";
			}
		if ($active!='1')
			{
			$nom="__ADRESSE DESACTIVEE__";
			$prenom="";
			}
		if ($version_dolibarr<"3.4")
			{
			if ($verif_clientpeople!='')
				{
				$info_erreur="Erreur de synchro sur : UPDATE FICHE CONTACT ID : $id_customer - NOM : $nom / Societe : $societe --> Verifiez la fiche client sous PrestaShop";//or die($info_erreur."<br />\n".mysql_error())
				$rowid_clientpeople=$donnees_verif_clientpeople['rowid'];
				mysql_query ("UPDATE ".$prefix_doli."socpeople set datec='$date',tms='$date',fk_soc='$rowid_client',civilite='$civilite',name='$nom',firstname='$prenom',address='$champadresse1 $champadresse2',cp='$codepostal',ville='$ville',fk_pays='$country_doli',birthday='$birthday',poste='$poste',phone='$tel',phone_mobile='$mobile',email='$mail',fk_user_modif=$rowid_user,note='$note' where rowid=$rowid_clientpeople") 
					or die($info_erreur."<br />\n".mysql_error());
				}
			if ($verif_clientpeople=='')
				{
				$info_erreur="Erreur de synchro sur : INSERT FICHE CONTACT ID : $id_customer - NOM : $nom / Societe : $societe --> Verifiez la fiche client sous PrestaShop";//or die($info_erreur."<br />\n".mysql_error())
				$rowid_clientpeople=$id_clientpeople;
				mysql_query ("INSERT INTO ".$prefix_doli."socpeople (rowid,datec,tms,fk_soc,civilite,name,firstname,address,cp,ville,fk_pays,birthday,poste,phone,phone_mobile,email,fk_user_creat,fk_user_modif,note) 
					VALUES ($rowid_clientpeople,'$date','$date',$rowid_client,'$civilite','$nom','$prenom','$champadresse1 $champadresse2','$codepostal','$ville','$country_doli','$birthday','$poste','$tel','$mobile','$mail',$rowid_user,$rowid_user,'$note')")
						or die($info_erreur."<br />\n".mysql_error());
				}
			}    
		if ($version_dolibarr>="3.4")
			{
			if ($verif_clientpeople!='')
				{
				$info_erreur="Erreur de synchro sur : UPDATE FICHE CONTACT ID : $id_customer - NOM : $nom / Societe : $societe --> Verifiez la fiche client sous PrestaShop";//or die($info_erreur."<br />\n".mysql_error())
				$rowid_clientpeople=$donnees_verif_clientpeople['rowid'];
				mysql_query ("UPDATE ".$prefix_doli."socpeople set datec='$date',tms='$date',fk_soc='$rowid_client',civilite='$civilite',lastname='$nom',firstname='$prenom',address='$champadresse1 $champadresse2',zip='$codepostal',town='$ville',fk_pays='$country_doli',birthday='$birthday',poste='$poste',phone='$tel',phone_mobile='$mobile',email='$mail',fk_user_modif=$rowid_user,note_private='$note' where rowid=$rowid_clientpeople") 
					or die($info_erreur."<br />\n".mysql_error());
				}
			if ($verif_clientpeople=='')
				{
				$info_erreur="Erreur de synchro sur : INSERT FICHE CONTACT ID : $id_customer - NOM : $nom / Societe : $societe --> Verifiez la fiche client sous PrestaShop";//or die($info_erreur."<br />\n".mysql_error())
				$rowid_clientpeople=$id_clientpeople;
				mysql_query ("INSERT INTO ".$prefix_doli."socpeople (rowid,datec,tms,fk_soc,civilite,lastname,firstname,address,zip,town,fk_pays,birthday,poste,phone,phone_mobile,email,fk_user_creat,fk_user_modif,note_private) 
					VALUES ($rowid_clientpeople,'$date','$date',$rowid_client,'$civilite','$nom','$prenom','$champadresse1 $champadresse2','$codepostal','$ville','$country_doli','$birthday','$poste','$tel','$mobile','$mail',$rowid_user,$rowid_user,'$note')")
						or die($info_erreur."<br />\n".mysql_error());
				}
			}
		// FIN CREATION DE LA FICHE CONTACT ***********************************************
		
		mysql_connect("$serveur_presta","$admin_presta","$mdp_presta");
		mysql_select_db("$base_presta");
		mysql_query("SET NAMES UTF8");
		$boucle=$boucle+1;*/


	// FIN RECUPERATION ADRESSES DU CLIENT *************************************************
		
	// DEFINITION DE L'AFFICHAGE *************************************************************
/*
	$echo ='COMPTE CLIENT OUVERT LE : '.$date.'';
	$echo ='';
	$echo =''.$echo.'Le '.$date_synchro.' à '.$heure_synchro.'\n';
	$echo =''.$echo.'\n';
	$echo =''.$echo.'[ SYNCHRONISATION REUSSIE ]\n';
	$echo =''.$echo.'\n';
	$echo =''.$echo.'---------------------------------------------------\n';
	$echo =''.$echo.'La fiche client ID : '.$rowid_client.'\n';
	$echo =''.$echo.'Nom du client : '.$societe.'\n';
	$echo =''.$echo.'\n';
	$echo =''.$echo.'eMail : '.$mail.'\n';
	$echo =''.$echo.'---------------------------------------------------\n';
	$echo =''.$echo.'COMPTE CLIENT OUVERT LE : '.$date.'\n';
	$echo =''.$echo.'\n';
	$echo =''.$echo.'^^^^^^^^^^^^^^^^^^^^\n';
	$echo =''.$echo.'Info Configuration :\n';
	$echo =''.$echo.'PrestaShop : '.$version_presta.' / Dolibarr : '.$version_dolibarr.' \n';
	$echo =''.$echo.'^^^^^^^^^^^^^^^^^^^^\n';
	$echo =''.$echo.'\n';
	// FIN DEFINITION DE L'AFFICHAGE *************************************************************

	// AFFICHAGE ****************************************************
	echo "<script language='JavaScript'>alert('$echo')</script>";   
	echo '<SCRIPT>javascript:window.close()</SCRIPT>';
// FIN AFFICHAGE ****************************************************
*/
?>
