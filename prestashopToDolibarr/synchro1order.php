<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include('../../config/config.inc.php');
include('stringUtils.php');
include('dolibarr/DolibarrApi.php');

function synchroOrder($id_order)
{
	echo "Synchronisation order : $id_order<br>"; 

	$prefix_ref_client=Configuration::get('prefix_ref_client');
	$prefix_ref_client = accents_sans("$prefix_ref_client");
	
	// DEFINITION DE DONNEES *****************************************
	$total_article=0;
	$rang=0;
	$entity=1;
	$fk_cond_reglement=6;
	$fk_mode_reglement=6;
	$source=1;
	$fk_cond_reglement_commande=1;
	$type_doli=2;
	$active=1;
	// FIN DEFINITION DE DONNEES *****************************************

	// RECUPERATION DONNEES DE LA COMMANDE ************************************
	$order = Db::getInstance()->GetRow("select * from "._DB_PREFIX_."orders where id_order='".$id_order."'");
	$id_customer=$order['id_customer'];
	$date_order=$order['date_add'];
	//$ref_client_doli="$label$id_order";
	$total=$order['total_paid'];
	$total = sprintf("%.2f",$total);
	//$total_paid_real_TTC=$order['total_paid_real'];
	//$total_paid_real_TTC=sprintf("%.2f",$total_paid_real_TTC);

	$total_net=$order['total_paid_tax_excl'];
	//$total_a_payer_HT=sprintf("%.2f",$total_a_payer_HT);
	$total_vat = $total - $total_net;
	$total_vat=sprintf("%.2f",$total_vat);

	// CALCUL DU PORT *********************************** 
	$total_shipping_TTC = $order['total_shipping_tax_incl'];
	$total_shipping_TTC=sprintf("%.2f",$total_shipping_TTC);
	$total_shipping_HT = $order['total_shipping_tax_excl'];
	$total_shipping_HT=sprintf("%.2f",$total_shipping_HT);
	$carrier_tax_rate = $order['carrier_tax_rate'];
	$carrier_tax_rate=sprintf("%.2f",$carrier_tax_rate);
	// FIN CALCUL DU PORT ***********************************

	$total_shipping_TVA = $total_shipping_TTC - $total_shipping_HT;
	$total_shipping_TVA=sprintf("%.2f",$total_shipping_TVA);
	$type_paiement = $order['payment'];
	$type_paiement = accents_minuscules("$type_paiement");   
	$valid=$order['valid'];
	
	if ($valid == 0) {
		echo "order is not valid. skip it.";
		return;
	}

	// FIN RECUPERATION DONNEES DE LA COMMANDE ************************************

	// get order status
	$create_invoice = false;
	$statut_propal=4;       //** Propal validée signée
	$order_status=0;     // draft by default
	$commande_facturee=0;   //** Commande NON facturée
	$invoice_status=0;      //** Facture en brouillon
	$paye=0;                //** Facture non payée
	$facture=0;             //** Commande Facturée --> Passe la commande en traitée quand statut='Livré'
	
	// handle order status
	$state=$order['current_state'];
	switch ($state) {
		//** Paiement par chèque ou virement
		case 0:
		case 10:
			$order_status = 0; // draft
			break;
		// Paiement accepted
		case 2:
			$create_invoice = true;
			$order_status = 1;           // validated
			//$commande_facturee=1;         //** Commande facturée
			//$invoice_status=2;            //** Facture en paiement validé
			//$paye=1;                      //** Facture en payée
			break;
		//** En cours de préparation
		case 3:
			$order_status = 1; // due to current dolibarr limitation using webservices
			//$order_status = 2;           //** Commande en Envoi en cours (mais pas encore expédiée)
			break;
		// in delivery
		case 4: 
			$order_status = 1; // due to current dolibarr limitation using webservices
			//$order_status = 3;           //** Commande en Délivrée (Expédition effectuée)
			break;
		// delivered
		case 5:
		case 35:
		case 37:
			$order_status = 1; // due to current dolibarr limitation using webservices
			//$order_status=3;           //** Commande en Délivrée (Et la commande est en : Facturée donc Commande passe en : Traitée)
			break;
		// cancelled or refund
		case 6:
		case 7:
			$order_status = 0; // due to current dolibarr limitation using webservices
			//$order_status='-1';        // canceled
			break;
		// paiement error or not validated
		case 8: 
			$order_status=0;           // draft
			break;
	}
/*
	// RECUPERATION ADRESSES DU CLIENT *************************************************
	$sql_adresse="select * from ".$prefix_presta."address where id_customer='".$id_customer."'";
	$result_adresse = mysql_query($sql_adresse) or die($sql_adresse."<br />\n".mysql_error());
	while ($adresse = mysql_fetch_array($result_adresse))
	{
		$entreprise=$adresse['company'];
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
			$nom=$chaine;
		if ($entreprise!="")
		{
			$societe=$entreprise;
				$chaine=$societe;    
				$chaine= accents_majuscules("$chaine");
				$societe=$chaine;
		}
		if ($entreprise=="")
		{
			$societe="$nom $prenom";
				$chaine=$societe;    
				$chaine= accents_majuscules("$chaine");
				$societe=$chaine;
		}
		$champadresse1=$adresse['address1'] ;
			$chaine=$champadresse1;    
			$chaine= accents_majuscules("$chaine");
			$champadresse1=$chaine;
		$champadresse2=$adresse['address2'];
			$chaine=$champadresse2;    
			$chaine= accents_majuscules("$chaine");
			$champadresse2=$chaine;
		$codepostal=$adresse['postcode'];
		$ville=$adresse['city'];
			$chaine=$ville;    
			$chaine= accents_majuscules("$chaine");
			$ville=$chaine;
		$country=$adresse['country'];
			$chaine=$country;    
			$chaine= accents_majuscules("$chaine");    
			$country=$chaine;
		$id_country=$adresse['id_country'];
			$donnees_country = Db::getInstance()->GetRow("select * from ".$prefix_presta."country where id_country='".$id_country."'");
			$iso_code_country=$donnees_country['iso_code'];
		$tel=$adresse['phone'];
			$chaine=$tel;    
			$chaine= tel_cacateres("$chaine");
			$tel=$chaine;
		$mobile=$adresse['phone_mobile'];
			$chaine=$tel;    
			$chaine= tel_cacateres("$chaine");
			$tel=$chaine;
		$vat_number=$adresse['vat_number'];
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
		$emetteur_paiement = "$entreprise $nom";
	}*/
	/*
	// Insertion TYPE DE PAIEMENT ***********************************************************
	$sql_recup_verif_mode_paiement="select * from ".$prefix_doli."c_paiement where libelle='Paiement en ligne'";
	$result_verif_mode_paiement = mysql_query($sql_recup_verif_mode_paiement) or die($sql_recup_verif_mode_paiement."<br />\n".mysql_error());
	$donnees_verif_mode_paiement = mysql_fetch_array($result_verif_mode_paiement);
	$verif_mode_paiement=$donnees_verif_mode_paiement['libelle'];
	if($verif_mode_paiement=="")
		{
		$info_erreur="Erreur de synchro sur : INSERT TYPE DE PAIEMENT ID : $id_mode_paiement - NOM : Paiement en ligne";//or die($info_erreur."<br />\n".mysql_error())
		$req_id_mode_paiement="select max(id) from ".$prefix_doli."c_paiement";
		$req_id_mode_paiement=mysql_query($req_id_mode_paiement);
		$id_mode_paiement=mysql_result($req_id_mode_paiement,0,"max(rowid)");
		$id_mode_paiement=$id_mode_paiement+1;
		mysql_query ("INSERT INTO ".$prefix_doli."c_paiement (id,code,libelle,type,active) 
			VALUES ($id_mode_paiement,'VAD','Paiement en ligne',2,1)")
				or die($info_erreur."<br />\n".mysql_error());
		}
	if($verif_mode_paiement=="Paiement en ligne")
		{
		$info_erreur="Erreur de synchro sur : UPDATE TYPE DE PAIEMENT ID : $id_mode_paiement - NOM : Paiement en ligne";//or die($info_erreur."<br />\n".mysql_error())
		$id_mode_paiement=$donnees_verif_mode_paiement['id'];
		mysql_query ("UPDATE ".$prefix_doli."c_paiement set code='VAD',libelle='Paiement en ligne',type=2,active=1 where id=$id_mode_paiement")
			or die($info_erreur."<br />\n".mysql_error());
		}
	$info_erreur="Erreur de synchro sur : COMPTE BANQUE NOM : Site Internet";//or die($info_erreur."<br />\n".mysql_error())
	mysql_query ("INSERT INTO ".$prefix_doli."bank_account (ref,label,entity,courant,rappro,currency_code) 
		VALUES ('Site','Site Internet',1,1,1,'EUR')") 
			Or mysql_query ("UPDATE ".$prefix_doli."bank_account set label='Site Internet',courant=1,rappro=1,currency_code='EUR' where ref='Site'")
				or die($info_erreur."<br />\n".mysql_error());
	$sql_recup_verif_bank_account="select * from ".$prefix_doli."bank_account where ref='Site'";
	$result_verif_bank_account = mysql_query($sql_recup_verif_bank_account) or die($sql_recup_verif_bank_account."<br />\n".mysql_error());
	$donnees_verif_bank_account = mysql_fetch_array($result_verif_bank_account);
	$verif_bank_account=$donnees_verif_bank_account['rowid'];
	if($verif_bank_account=="")
		{
		$id_bank_account=1;
		}
	if($verif_bank_account!="")
		{
		$id_bank_account=$donnees_verif_bank_account['rowid'];
		}
	// FIN Insertion TYPE DE PAIEMENT ***********************************************************

	// Insertion TYPE DE BANQUE ***********************************************************
	$sql_recup_verif_bank_categ="select * from ".$prefix_doli."bank_categ where label='Vente de produits sur Site Internet'";
	$result_verif_bank_categ = mysql_query($sql_recup_verif_bank_categ) or die($sql_recup_verif_bank_categ."<br />\n".mysql_error());
	$donnees_verif_bank_categ = mysql_fetch_array($result_verif_bank_categ);
	$verif_bank_categ=$donnees_verif_bank_categ['rowid'];
	if($verif_bank_categ=="")
		{
		$info_erreur="Erreur de synchro sur : INSERT TYPE DE BANQUE NOM : Vente de produits sur Site Internet";//or die($info_erreur."<br />\n".mysql_error())
		$req_id_bank_categ="select max(id) from ".$prefix_doli."bank_categ";
		$req_id_bank_categ=mysql_query($req_id_bank_categ);
		$id_bank_categ=mysql_result($req_id_bank_categ,0,"max(rowid)");
		$id_bank_categ=$id_bank_categ+1;
		mysql_query ("INSERT INTO ".$prefix_doli."bank_categ (label,entity) 
			VALUES ('Vente de produits sur Site Internet',1)")
				or die($info_erreur."<br />\n".mysql_error());
		}
	if($verif_bank_categ!="")
		{
		$id_bank_categ=$donnees_verif_bank_categ['rowid'];
		}
	// FIN Insertion TYPE DE BANQUE ***********************************************************
	*/
	// Insertion du produit PORT dans Dolibarr ***************************
	/*
	$info_erreur="Erreur de synchro sur : Produit PORT dans Dolibarr - Code article/ID : $code_article_port - Libelle : $libelle_port NOM : Vente de produits sur Site Internet";//or die($info_erreur."<br />\n".mysql_error())
	$fk_product_type=0;
	mysql_query ("INSERT INTO ".$prefix_doli."product (rowid,ref,label,description,tva_tx,note,fk_user_author,fk_product_type,barcode) 
		VALUES ($code_article_port,'$libelle_port','$libelle_port','$libelle_port','$tva_tx_taux','$libelle_port','$rowid_user',$fk_product_type,$code_article_port)") 
			or mysql_query ("UPDATE ".$prefix_doli."product set ref='$libelle_port',label='$libelle_port',description='$libelle_port',tva_tx='$tva_tx_taux',note='$libelle_port',fk_user_author='$rowid_user',fk_product_type=$fk_product_type,barcode='".$code_article_port."' where rowid='".$code_article_port."'")
				or die($info_erreur."<br />\n".mysql_error());
	// FIN Insertion du produit PORT dans Dolibarr ***************************

	// CREATION DU TYPE DE PAIEMENT DANS DOLIBARR *******************************
	$req_id_mode_paiement="select max(id) from ".$prefix_doli."c_paiement";
	$req_id_mode_paiement=mysql_query($req_id_mode_paiement);
	$id_mode_paiement=mysql_result($req_id_mode_paiement,0,"max(id)");
	$id_mode_paiement=$id_mode_paiement+1;
	$sql_recup_verif_mode_paiement="select * from ".$prefix_doli."c_paiement where libelle='$type_paiement'";
	$result_verif_mode_paiement = mysql_query($sql_recup_verif_mode_paiement) or die($sql_recup_verif_mode_paiement."<br />\n".mysql_error());
	$donnees_verif_mode_paiement = mysql_fetch_array($result_verif_mode_paiement);
	$verif_mode_paiement=$donnees_verif_mode_paiement['id'];
	$code_paiement=$type_paiement;
		$chaine=$code_paiement;    
		$chaine= accents_sans("$chaine");   
		$code_paiement=$chaine;
		$code_paiement=strtoupper($code_paiement);
	$code_paiement=substr($type_paiement,0,3);
	if ($verif_mode_paiement!="")
		{
		$rowid_mode_paiement=$donnees_verif_mode_paiement['id'];
		}
	if ($verif_mode_paiement=="")
		{
		$info_erreur="Erreur de synchro sur : INSERT MODE DE PAIEMENT - ID : $rowid_mode_paiement - Code paiement : $code_paiement - Libelle : $type_paiement";//or die($info_erreur."<br />\n".mysql_error())
		$rowid_mode_paiement=$id_mode_paiement;
		mysql_query ("INSERT INTO ".$prefix_doli."c_paiement (id,code,libelle,type,active) 
			VALUES ($rowid_mode_paiement,'$code_paiement','$type_paiement','$type_doli','$active')")
				or die($info_erreur."<br />\n".mysql_error());
		}
	$fk_mode_reglement_commande=$rowid_mode_paiement;
	// FIN CREATION DU TYPE DE PAIEMENT DANS DOLIBARR *******************************

	// DETERMINATION ID PROPAL DOLIBARR *******************************************
	$req_id_propal="select max(rowid) from ".$prefix_doli."propal";
	$req_id_propal=mysql_query($req_id_propal);
	$id_propal=mysql_result($req_id_propal,0,"max(rowid)");
	$id_propal=$id_propal+1;
	$sql_recup_verif_propal="select * from ".$prefix_doli."propal where total='$total_a_payer_TTC' and datec ='".$dateorder."'";
	$result_verif_propal = mysql_query($sql_recup_verif_propal) or die($sql_recup_verif_propal."<br />\n".mysql_error());
	$donnees_verif_propal = mysql_fetch_array($result_verif_propal);
	$verif_propal=$donnees_verif_propal['ref_client'];
	if ($verif_propal!="")
		{
		$rowid_propal=$donnees_verif_propal['rowid'];
		// CREATION DE LA REFERENCE PROPAL *************************************************
		$ref_propal=$donnees_verif_propal['ref'];
		// FIN CREATION DE LA REFERENCE PROPAL *************************************************
		}
	if ($verif_propal=="")
		{
		$rowid_propal=$id_propal;
		// CREATION DE LA REFERENCE PROPAL *************************************************
		$req_dernier_id="select max(rowid) from ".$prefix_doli."propal order by ref asc";
		$req_dernier_id=mysql_query($req_dernier_id);
		$dernier_id=mysql_result($req_dernier_id,0,"max(rowid)");
		$sql_derniere_ref = Db::getInstance()->GetRow("select * from ".$prefix_doli."propal where rowid='$dernier_id'");
		$derniere_ref=$sql_derniere_ref['ref'];
		$verif_chrono=substr($derniere_ref,0,2);
		$chrono=substr($derniere_ref,7,4);
		$chrono=$chrono+1;
		$chrono_nnnn=str_pad($chrono, 4, "0", STR_PAD_LEFT);
		$annee=substr($date_propal,2,2);
		$mois=substr($date_propal,5,2);
		$ref="$annee";
		$ref="$ref$mois-";
		$ref="PR$ref$chrono_nnnn";
		}
		// FIN CREATION DE LA REFERENCE PROPAL *************************************************
	// FIN DETERMINATION ID PROPAL DOLIBARR *******************************************
	*/
	// CREATION DE LA PROPAL ***************************************************
	/*if ($rowid_client!="")
		{
		if ($verif_propal!="")
			{
			$info_erreur="Erreur de synchro sur : UPDATE PROPAL - ID PROPAL : $rowid_propal - ID CLIENT : $rowid_client - REF COMMANDE PRESTASHOP : $ref_propal";//or die($info_erreur."<br />\n".mysql_error())
			mysql_query ("UPDATE ".$prefix_doli."propal set fk_soc='$rowid_client',tms='$dateorder',entity='$entity',ref_client='$ref_client_doli',datec='$dateorder',datep='$date_propal',fin_validite='$dateorder',date_valid='$dateorder',date_cloture='$dateorder',fk_statut='$statut_propal',total_ht='$total_a_payer_HT',tva='$total_taxes',total='$total_a_payer_TTC',fk_mode_reglement='$fk_mode_reglement_commande' where rowid=$rowid_propal") 
				or die($info_erreur."<br />\n".mysql_error());
			}
		if ($verif_propal=="")
			{
			$info_erreur="Erreur de synchro sur : INSERT PROPAL - ID PROPAL : $rowid_propal - ID CLIENT : $rowid_client - REF COMMANDE PRESTASHOP : $ref_propal";//or die($info_erreur."<br />\n".mysql_error())
			mysql_query ("INSERT INTO ".$prefix_doli."propal (rowid,fk_soc,tms,ref,entity,ref_client,datec,datep,fin_validite,date_valid,date_cloture,fk_statut,total_ht,tva,total,fk_cond_reglement,fk_mode_reglement,model_pdf) 
				VALUES ('$rowid_propal','$rowid_client','$dateorder','$ref','$entity','$ref_client_doli','$dateorder','$date_propal','$dateorder','$dateorder','$dateorder','$statut_propal','$total_a_payer_HT','$total_taxes','$total_a_payer_TTC','$fk_cond_reglement','$fk_mode_reglement','$model_pdf')") 
					or die($info_erreur."<br />\n".mysql_error());
			}
		}
	// FIN CREATION DE LA PROPAL ***************************************************
*/
	// DETERMINATION ID COMMANDE DOLIBARR *******************************************
	/*$req_id_commande="select max(rowid) from ".$prefix_doli."commande";
	$req_id_commande=mysql_query($req_id_commande);
	$id_commande=mysql_result($req_id_commande,0,"max(rowid)");
	$id_commande=$id_commande+1;
	$sql_recup_verif_commande="select * from ".$prefix_doli."commande where total_ttc='$total_a_payer_TTC' and date_creation ='".$dateorder."'";
	$result_verif_commande = mysql_query($sql_recup_verif_commande) or die($sql_recup_verif_commande."<br />\n".mysql_error());
	$donnees_verif_commande = mysql_fetch_array($result_verif_commande);
	$verif_commande=$donnees_verif_commande['ref_client'];
	if ($verif_commande!="")
		{
		$rowid_commande=$donnees_verif_commande['rowid'];
		// CREATION DE LA REFERENCE COMMANDE *************************************************
		$ref_commande=$donnees_verif_commande['ref'];
		// FIN CREATION DE LA REFERENCE COMMANDE *************************************************
		}
	if ($verif_commande=="")
		{
		$rowid_commande=$id_commande;
		// CREATION DE LA REFERENCE COMMANDE *************************************************
		$req_dernier_id="select max(rowid) from ".$prefix_doli."commande order by ref asc";
		$req_dernier_id=mysql_query($req_dernier_id);
		$dernier_id=mysql_result($req_dernier_id,0,"max(rowid)");
		$sql_derniere_ref = Db::getInstance()->GetRow("select * from ".$prefix_doli."commande where rowid='$dernier_id'");
		$derniere_ref=$sql_derniere_ref['ref'];
		$verif_chrono=substr($derniere_ref,0,2);
		$chrono=substr($derniere_ref,7,4);
		$chrono=$chrono+1;
		$chrono_nnnn=str_pad($chrono, 4, "0", STR_PAD_LEFT);
		$annee=substr($date_commande,2,2);
		$mois=substr($date_commande,5,2);
		$ref="$annee";
		$ref="$ref$mois-";
		$ref="CO$ref$chrono_nnnn";
		}
		// FIN CREATION DE LA REFERENCE COMMANDE *************************************************
	// FIN DETERMINATION ID COMMANDE DOLIBARR *******************************************
*/

	// load order details
	$products = Db::getInstance()->executeS("select * from "._DB_PREFIX_."order_detail where id_order='".$id_order."'");
	$count = 0;
	foreach ( $products as $product )
	{
		$line = new DolibarrOrderLines();
		$line->desc = $product['product_name'];
		$line->qty = $product['product_quantity'];
		$line->unitprice = $product['unit_price_tax_excl'];
		$line->remise = $product['reduction_amount'];
		$line->remise_percent = $product['reduction_percent'];
		$line->total_net = $product['total_price_tax_excl'];
		$line->total = $product['total_price_tax_incl'];
		$line->total_vat = 	sprintf("%.2f", $product['total_price_tax_incl'] - $product['total_price_tax_excl']);
		
		// vat_rate isn't set properly on order_detail so compute it...
		$line->vat_rate = sprintf("%.1f", ($product['total_price_tax_incl'] - $product['total_price_tax_excl'])/$product['total_price_tax_excl']*100);
		if ($line->vat_rate > 19.8 && $line->vat_rate < 20.2) {
			$line->vat_rate = 20;
		}else if ($line->vat_rate > 9.8 && $line->vat_rate < 10.2) {
			$line->vat_rate = 10;
		}
		/*public $id;
		public $type;
		

		public $vat_rate;

		
		public $date_start = ""; // dateTime
		public $date_end = ""; // dateTime*/
		
		$lines[$count]= $line;
		$count++;

	}

	$dolibarr = Dolibarr::getInstance();
	
	var_dump($prefix_ref_client.$id_customer);
	// retrieve user
	$client = $dolibarr->getUser($prefix_ref_client.$id_customer);
	if ($client["result"]->result_code == 'NOT_FOUND')
    {
		echo "Error : client doesn't exist. Try to synchronize clients first.";
		return;
	}
	var_dump($client["thirdparty"]->id);

	// Check if already exists in Dolibarr
	$exists = $dolibarr->getOrder($id_order);
	
	// Create order
	$dolibarrOrder = new DolibarrOrder();
	$dolibarrOrder->ref_ext = $id_order;
	$dolibarrOrder->thirdparty_id = $client["thirdparty"]->id;
	$dolibarrOrder->date = $order["date_add"];
	if ($order['delivery_number'] != 0) {
		$dolibarrOrder->date_livraison = $order['delivery_date'];
	}
	$dolibarrOrder->status = $order_status;
	/*$dolibarrOrder->total = $total;
    $dolibarrOrder->total_net = $total_net;
    $dolibarrOrder->total_vat = $total_v;*/
	$dolibarrOrder->lines = $lines;

	if ($exists["result"]->result_code == 'NOT_FOUND')
    {
		// Create new order
		echo "Create new order : <br>";
		$result = $dolibarr->createOrder($dolibarrOrder);
		if ($result["result"]->result_code == 'KO')
        {
			echo "Erreur de synchronisation : ".$result["result"]->result_label;
		}
	} else
    {
		echo "Can't update order, dolibarr 3.6 webservice is bugged. Fixed in 3.7";
		return;
		/*
		// Update order
		echo "update order<br>";
		$oldOrder = $exists["order"];
		$dolibarrOrder->id = $oldOrder->id;
		$result = $dolibarr->updateUser($dolibarrOrder);
		if ($result["result"]->result_code == 'KO')
        {
			echo "Erreur de synchronisation : ".$result["result"]->result_label;
		}
		*/
	}	
	/*
	// CREATION DE LA FACTURE *********************************************************
	if ($rowid_client!="")
		{
		if ($create_invoice)
			{        
			if ($verif_facture=="")
				{
				$info_erreur="Erreur de synchro sur : INSERT FACTURE - ID FACTURE : $rowid_facture - ID CLIENT : $rowid_client - REF COMMANDE PRESTASHOP : $ref_facture";//or die($info_erreur."<br />\n".mysql_error())
				mysql_query ("INSERT INTO ".$prefix_doli."facture (rowid,facnumber,ref_client,fk_soc,datec,datef,date_valid,tms,paye,tva,total,total_ttc,fk_statut,fk_cond_reglement,fk_mode_reglement,date_lim_reglement,model_pdf) 
					VALUES ('$rowid_facture','$ref','$ref_client_doli','$rowid_client','$dateorder','$dateorder','$dateorder','$dateorder','$paye','$total_taxes','$total_a_payer_HT','$total_a_payer_TTC','$invoice_status',$fk_cond_reglement_commande,$fk_mode_reglement_commande,'$dateorder','$model_pdf_facture')") 
						or die($info_erreur."<br />\n".mysql_error());
				}
			if ($verif_facture!="")
				{
				$info_erreur="Erreur de synchro sur : UPDATE FACTURE - ID FACTURE : $rowid_facture - ID CLIENT : $rowid_client - REF COMMANDE PRESTASHOP : $ref_facture";//or die($info_erreur."<br />\n".mysql_error())
				mysql_query ("UPDATE ".$prefix_doli."facture set entity='$entity',ref_client='$ref_client_doli',fk_soc='$rowid_client',datec='$dateorder',datef='$dateorder',date_valid='$dateorder',tms='$dateorder',tva='$total_taxes',total='$total_a_payer_HT',total_ttc='$total_a_payer_TTC',fk_mode_reglement='$fk_mode_reglement_commande' where rowid=$rowid_facture") 
					or die($info_erreur."<br />\n".mysql_error());
				}
			}        
		// INSERTION DES LIENS COMMANDE / FACTURE ************************************
		$sql_recup_verif_lien_commande="select * from ".$prefix_doli."element_element where fk_source ='".$rowid_commande."' and sourcetype='commande' and fk_target='".$rowid_facture."' and targettype='facture'";
		$result_verif_lien_commande = mysql_query($sql_recup_verif_lien_commande) or die($sql_recup_verif_lien_commande."<br />\n".mysql_error());
		$donnees_verif_lien_commande = mysql_fetch_array($result_verif_lien_commande);
		$verif_lien_commande=$donnees_verif_lien_commande['rowid'];
		if ($verif_lien_commande=="")
			{
			$info_erreur="Erreur de synchro sur : INSERTION DES LIENS COMMANDE / FACTURE";//or die($info_erreur."<br />\n".mysql_error())
			mysql_query ("INSERT INTO ".$prefix_doli."element_element (fk_source,sourcetype,fk_target,targettype) 
				VALUES ('$rowid_commande','commande','$rowid_facture','facture')")
					or die($info_erreur."<br />\n".mysql_error());
			}
		// FIN INSERTION DES LIENS COMMANDE / FACTURE ************************************
		
		}

	// INSERTION DES LIENS PROPAL / FACTURE ************************************
	if ($rowid_client!="")
		{
		$sql_recup_verif_lien_commande="select * from ".$prefix_doli."element_element where fk_source ='".$rowid_propal."' and sourcetype='propal' and fk_target='".$rowid_facture."' and targettype='facture'";
		$result_verif_lien_commande = mysql_query($sql_recup_verif_lien_commande) or die($sql_recup_verif_lien_commande."<br />\n".mysql_error());
		$donnees_verif_lien_commande = mysql_fetch_array($result_verif_lien_commande);
	//     $donnees_verif_lien_commande = Db::getInstance()->GetRow("select * from ".$prefix_doli."element_element where fk_source ='".$rowid_propal."' and sourcetype='propal' and fk_target='".$rowid_facture."' and targettype='facture'");
		$verif_lien_commande=$donnees_verif_lien_commande['rowid'];
		if ($verif_lien_commande=="")
			{
			$info_erreur="Erreur de synchro sur : INSERTION DES LIENS PROPAL / FACTURE";//or die($info_erreur."<br />\n".mysql_error())
			mysql_query ("INSERT INTO ".$prefix_doli."element_element (fk_source,sourcetype,fk_target,targettype) 
				VALUES ('$rowid_propal','propal','$rowid_facture','facture')")
					or die($info_erreur."<br />\n".mysql_error());
			}
		}
	// FIN INSERTION DES LIENS PROPAL / FACTURE ************************************
	// FIN CREATION DE LA FACTURE *********************************************************

	// ECRITURE DU PAIEMENT SI POSSIBLE ***********************************************************
	if ($create_invoice)
		{
		$req_id_paiement="select max(rowid) from ".$prefix_doli."paiement";
		$req_id_paiement=mysql_query($req_id_paiement);
		$id_paiement=mysql_result($req_id_paiement,0,"max(rowid)");
		$id_paiement=$id_paiement+1;
		$sql_recup_verif_paiement="select * from ".$prefix_doli."paiement where amount='".$total_paid_real_TTC."' and datep='".$dateorder."'";
		$result_verif_paiement = mysql_query($sql_recup_verif_paiement) or die($sql_recup_verif_paiement."<br />\n".mysql_error());
		$donnees_verif_paiement = mysql_fetch_array($result_verif_paiement);
		$verif_paiement=$donnees_verif_paiement['amount'];
		if ($verif_paiement!="")
			{
			$rowid_paiement=$donnees_verif_paiement['rowid'];
			}
		if ($verif_paiement=="")
			{
			$rowid_paiement=$id_paiement;
			}
		$req_rowid_bank="select max(rowid) from ".$prefix_doli."bank";
		$req_rowid_bank=mysql_query($req_rowid_bank);
		$id_bank=mysql_result($req_rowid_bank,0,"max(rowid)");
		$id_bank=$id_bank+1;
		$sql_recup_verif_bank="select * from ".$prefix_doli."bank where datec='".$dateorder."' and amount='".$total_paid_real_TTC."'";
		$result_verif_bank = mysql_query($sql_recup_verif_bank) or die($sql_recup_verif_bank."<br />\n".mysql_error());
		$donnees_verif_bank = mysql_fetch_array($result_verif_bank);
		$verif_bank=$donnees_verif_bank['datec'];
		if ($verif_bank==$date_commande)
			{
			$rowid_bank=$donnees_verif_bank['rowid'];
			}
		if ($verif_bank!=$date_commande)
			{
			$rowid_bank=$id_bank;
			}
		$num_releve = $dateorder;
		$num_releve = str_replace( "-", "", $num_releve);
		$lg_max = 6;
		if (strlen($num_releve) > $lg_max)
			{
			$num_releve = substr($num_releve, 0, $lg_max);
			}
		
		$info_erreur="Erreur de synchro sur : PAIEMENT - ID PAIEMENT : $rowid_paiement - ID BANK : $rowid_bank";//or die($info_erreur."<br />\n".mysql_error())    
		mysql_query ("INSERT INTO ".$prefix_doli."paiement (rowid,datec,tms,datep,amount,fk_paiement,fk_bank,statut) 
			VALUES ('$rowid_paiement','$dateorder','$dateorder','$dateorder','$total_paid_real_TTC','$rowid_mode_paiement','$rowid_bank',0)"); 
		
		$info_erreur="Erreur de synchro sur : paiement_facture - ID PAIEMENT : $rowid_paiement - ID FACTURE : $rowid_facture";//or die($info_erreur."<br />\n".mysql_error())
		mysql_query ("INSERT INTO ".$prefix_doli."paiement_facture (rowid,fk_paiement,fk_facture,amount) 
			VALUES ('$rowid_paiement','$rowid_paiement','$rowid_facture','$total_paid_real_TTC')") 
				Or mysql_query ("UPDATE ".$prefix_doli."paiement_facture set amount='$total_paid_real_TTC' where rowid='".$rowid_paiement."'")
					or die($info_erreur."<br />\n".mysql_error()); 
		
		$info_erreur="Erreur de synchro sur : UPDATE statut facture - ID FACTURE : $rowid_facture";//or die($info_erreur."<br />\n".mysql_error())
		mysql_query ("UPDATE ".$prefix_doli."facture set paye='$paye',amount='$total_paid_real_TTC',fk_statut=2 where rowid='".$rowid_facture."'")
			or die($info_erreur."<br />\n".mysql_error());
		
		$info_erreur="Erreur de synchro sur : bank - ID BANK : $rowid_bank - EMETTEUR PAIEMENT : $emetteur_paiement - DATE : $dateorder - MONTANT : $total_paid_real_TTC";//or die($info_erreur."<br />\n".mysql_error())
		mysql_query ("INSERT INTO ".$prefix_doli."bank (rowid,datec,datev,dateo,amount,label,fk_account,fk_type,banque,emetteur) 
			VALUES ('$rowid_bank','$dateorder','$dateorder','$dateorder','$total_paid_real_TTC','(CustomerInvoicePayment)','$id_bank_account','VAD',0,'$emetteur_paiement')") 
				Or mysql_query ("UPDATE ".$prefix_doli."bank set datec='$dateorder',datev='$dateorder',dateo='$dateorder',amount='$total_paid_real_TTC',label='(CustomerInvoicePayment)',fk_type='VAD',banque=0,emetteur='$emetteur_paiement' where rowid='".$rowid_bank."'")
					or die($info_erreur."<br />\n".mysql_error());
		
		$info_erreur="Erreur de synchro sur : bank_url 1";//or die($info_erreur."<br />\n".mysql_error())
		mysql_query ("INSERT INTO ".$prefix_doli."bank_url (fk_bank,url_id,url,label,type) 
			VALUES ('$rowid_bank','$rowid_paiement','/_doli/htdocs/compta/paiement/fiche.php?id=','(paiement)','payment')") 
				Or mysql_query ("UPDATE ".$prefix_doli."bank_url set url='/_doli/htdocs/compta/paiement/fiche.php?id=',label='(paiement)',type='payment' where fk_bank='".$rowid_bank."' and url_id='".$rowid_paiement."'")
					or die($info_erreur."<br />\n".mysql_error());
		
		$info_erreur="Erreur de synchro sur : bank_url 2 - EMETTEUR PAIEMENT : $emetteur_paiement";//or die($info_erreur."<br />\n".mysql_error())
		mysql_query ("INSERT INTO ".$prefix_doli."bank_url (fk_bank,url_id,url,label,type) 
			VALUES ('$rowid_bank','$rowid_client','/_doli/htdocs/comm/fiche.php?socid=','$emetteur_paiement','company')") 
				Or mysql_query ("UPDATE ".$prefix_doli."bank_url set url='/_doli/htdocs/comm/fiche.php?socid=',label='$emetteur_paiement',type='company' where fk_bank='".$rowid_bank."' and url_id='".$rowid_client."'")
					or die($info_erreur."<br />\n".mysql_error());
		} 
	// FIN ECRITURE DU PAIEMENT SI POSSIBLE ***********************************************************

	// AJOUT DE LA LIGNE DE PORT ****************************************************************************************    
	$rang=$rang+1;
	mysql_connect("$serveur_doli","$admin_doli","$mdp_doli");
	mysql_select_db("$base_doli");
	mysql_query("SET NAMES UTF8"); 
	if ($rowid_client!="")
		{
		$rowid=$code_article_port;      
		$sql_recup_verif_art_propal="select * from ".$prefix_doli."propaldet where fk_propal=$rowid_propal and fk_product=$rowid";
		$result_verif_art_propal= mysql_query($sql_recup_verif_art_propal) or die($sql_recup_verif_art_propal."<br />\n".mysql_error());
		$donnees_verif_art_propal = mysql_fetch_array($result_verif_art_propal);
		$verif_art_propal=$donnees_verif_art_propal['fk_product'];
		if ($verif_art_propal==$rowid)
			{
			$info_erreur="Erreur de synchro sur : UPDATE DE LA LIGNE DE PORT - ID PROPAL : $rowid_propal - ID DU PORT : $rowid - LIBELLE DU PORT : $libelle_port";//or die($info_erreur."<br />\n".mysql_error())
			mysql_query ("UPDATE ".$prefix_doli."propaldet set description='$libelle_port',tva_tx='$carrier_tax_rate',qty='1',price=$total_shipping_HT,subprice=$total_shipping_HT,total_ht=$total_shipping_HT,total_tva=$total_shipping_TVA,total_ttc=$total_shipping_TTC where fk_propal=$rowid_propal and fk_product=$rowid")
				or die($info_erreur."<br />\n".mysql_error()); 
			}
		else
			{
			$info_erreur="Erreur de synchro sur : INSERT DE LA LIGNE DE PORT - ID PROPAL : $rowid_propal - ID DU PORT : $rowid - LIBELLE DU PORT : $libelle_port";//or die($info_erreur."<br />\n".mysql_error())
			mysql_query ("INSERT INTO ".$prefix_doli."propaldet (fk_propal,fk_product,description,tva_tx,qty,price,subprice,total_ht,total_tva,total_ttc,product_type,rang) 
				values ($rowid_propal,$rowid,'$libelle_port','$carrier_tax_rate','1','$total_shipping_HT','$total_shipping_HT','$total_shipping_HT','$total_shipping_TVA','$total_shipping_TTC',0,'$rang')")
					or die($info_erreur."<br />\n".mysql_error()); 
			}
		$rowid=$code_article_port;      
		$sql_recup_verif_art_commande="select * from ".$prefix_doli."commandedet where fk_commande=$rowid_commande and fk_product=$rowid";
		$result_verif_art_commande= mysql_query($sql_recup_verif_art_commande) or die($sql_recup_verif_art_commande."<br />\n".mysql_error());
		$donnees_verif_art_commande = mysql_fetch_array($result_verif_art_commande);
		$verif_art_commande=$donnees_verif_art_commande['fk_product'];      
		if ($verif_art_commande==$rowid)
			{
			$info_erreur="Erreur de synchro sur : UPDATE DE LA LIGNE DE PORT - ID COMMANDE : $rowid_commande - ID DU PORT : $rowid - LIBELLE DU PORT : $libelle_port";//or die($info_erreur."<br />\n".mysql_error())
			mysql_query ("UPDATE ".$prefix_doli."commandedet set description='$libelle_port',tva_tx='$carrier_tax_rate',qty='1',price='$total_shipping_HT',subprice='$total_shipping_HT',total_ht='$total_shipping_HT',total_tva='$total_shipping_TVA',total_ttc='$total_shipping_TTC' where fk_commande=$rowid_commande and fk_product=$rowid")
				or die($info_erreur."<br />\n".mysql_error()); 
			}
		else
			{
			$info_erreur="Erreur de synchro sur : INSERT DE LA LIGNE DE PORT - ID COMMANDE : $rowid_commande - ID DU PORT : $rowid - LIBELLE DU PORT : $libelle_port";//or die($info_erreur."<br />\n".mysql_error())
			mysql_query ("INSERT INTO ".$prefix_doli."commandedet (	fk_commande,fk_product,description,tva_tx,qty,subprice,total_ht,total_tva,total_ttc,product_type,rang) 
				values ($rowid_commande,$rowid,'$libelle_port','$carrier_tax_rate','1','$total_shipping_HT','$total_shipping_HT','$total_shipping_TVA','$total_shipping_TTC',0,'$rang')")
					or die($info_erreur."<br />\n".mysql_error()); 
			}
		if ($create_invoice)
			{
			$rowid=$code_article_port;      
			$sql_recup_verif_art_facture="select * from ".$prefix_doli."facturedet where fk_facture=$rowid_facture and fk_product=$rowid";
			$result_verif_art_facture= mysql_query($sql_recup_verif_art_facture) or die($sql_recup_verif_art_facture."<br />\n".mysql_error());
			$donnees_verif_art_facture = mysql_fetch_array($result_verif_art_facture);
			$verif_art_facture=$donnees_verif_art_facture['fk_product'];      
			if ($verif_art_facture==$rowid)
				{
				$info_erreur="Erreur de synchro sur : UPDATE DE LA LIGNE DE PORT - ID FACTURE : $rowid_facture - ID DU PORT : $rowid - LIBELLE DU PORT : $libelle_port";//or die($info_erreur."<br />\n".mysql_error())
				mysql_query ("UPDATE ".$prefix_doli."facturedet set description='$libelle_port',tva_tx='$carrier_tax_rate',qty='1',subprice='$total_shipping_HT',price='$total_shipping_HT',total_ht='$total_shipping_HT',total_tva='$total_shipping_TVA',total_ttc='$total_shipping_TTC' where fk_product=$rowid and fk_facture=$rowid_facture")
					or die($info_erreur."<br />\n".mysql_error());
				}
			else
				{
				$info_erreur="Erreur de synchro sur : INSERT DE LA LIGNE DE PORT - ID FACTURE : $rowid_facture - ID DU PORT : $rowid - LIBELLE DU PORT : $libelle_port";//or die($info_erreur."<br />\n".mysql_error())
				mysql_query ("INSERT INTO ".$prefix_doli."facturedet (fk_facture,fk_product,description,tva_tx,qty,subprice,price,total_ht,total_tva,total_ttc,product_type,rang) 
					values ($rowid_facture,$rowid,'$libelle_port','$carrier_tax_rate','1','$total_shipping_HT','$total_shipping_HT','$total_shipping_HT','$total_shipping_TVA','$total_shipping_TTC',0,'$rang')")
						or die($info_erreur."<br />\n".mysql_error());
				}
			}
		}
	// FIN AJOUT DE LA LIGNE DE PORT ****************************************************************************************
*/
}

if (Tools::isSubmit('id_order'))
{
    $id_order=Tools::getValue('id_order');
    synchroOrder($id_order);
}


?>
