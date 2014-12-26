<?php
//**************************************  DEBUT   CRON   *******************************************************
    
// RECUPERATION DE LA CONFIG **********************************************
include('../../config/config.inc.php');
// RECUPERATION DE LA CONFIG **********************************************

// CONNEXION AUTOMATIQUE A LA BASE PRESTA **************************************************
mysql_connect(_DB_SERVER_,_DB_USER_,_DB_PASSWD_); 
mysql_select_db(_DB_NAME_);
mysql_query("SET NAMES UTF8");
// FIN CONNEXION AUTOMATIQUE A LA BASE PRESTA **************************************************

// FONCTIONS *********************************************************
function accents_majuscules($chaine)
    {
    $chaine = htmlentities($chaine);
    $chaine = html_entity_decode($chaine,ENT_QUOTES,"ISO-8859-1");
    $chaine = str_replace( "'", " ", $chaine);
    $chaine = str_replace( "ä", "Ä", $chaine);$chaine = str_replace( "â", "Â", $chaine);$chaine = str_replace( "à", "À", $chaine);$chaine = str_replace( "á", "Á", $chaine);$chaine = str_replace( "å", "Å", $chaine);
    $chaine = str_replace( "ã", "Ã", $chaine);$chaine = str_replace( "é", "É", $chaine);$chaine = str_replace( "è", "È", $chaine);$chaine = str_replace( "ë", "Ë", $chaine);$chaine = str_replace( "ê", "Ê", $chaine);
    $chaine = str_replace( "ò", "Ò", $chaine);$chaine = str_replace( "ó", "Ó", $chaine);$chaine = str_replace( "ô", "Ô", $chaine);$chaine = str_replace( "õ", "Õ", $chaine);$chaine = str_replace( "ö", "Ö", $chaine);
    $chaine = str_replace( "ø", "Ø", $chaine);$chaine = str_replace( "ì", "Ì", $chaine);$chaine = str_replace( "í", "Í", $chaine);$chaine = str_replace( "î", "Î", $chaine);$chaine = str_replace( "ï", "Ï", $chaine);
    $chaine = str_replace( "ù", "Ù", $chaine);$chaine = str_replace( "ú", "Ú", $chaine);$chaine = str_replace( "û", "Û", $chaine);$chaine = str_replace( "ü", "Ü", $chaine);$chaine = str_replace( "ý", "Ý", $chaine);
    $chaine = str_replace( "ñ", "Ñ", $chaine);$chaine = str_replace( "ç", "Ç", $chaine);$chaine = str_replace( "þ", "Þ", $chaine);$chaine = str_replace( "ÿ", "Ý", $chaine);$chaine = str_replace( "æ", "Æ", $chaine);
    $chaine = str_replace( "œ", "Œ", $chaine);$chaine = str_replace( "ð", "Ð", $chaine);$chaine = str_replace( "ø", "Ø", $chaine);
    $chaine=strtoupper($chaine);
    return $chaine;
    }
function accents_minuscules($chaine)
    {
    $chaine = htmlentities($chaine);
    $chaine = html_entity_decode($chaine,ENT_QUOTES,"ISO-8859-1");
    $chaine = str_replace( "'", " ", $chaine);
    return $chaine;
    }
function accents_sans($chaine)
    {
    $chaine = htmlentities($chaine);
    $chaine = html_entity_decode($chaine,ENT_QUOTES,"ISO-8859-1");
    $chaine = str_replace( "'", " ", $chaine);
    $chaine = str_replace( "°", "o", $chaine);
    $chaine = str_replace( "ä", "a", $chaine);$chaine = str_replace( "â", "a", $chaine);$chaine = str_replace( "à", "a", $chaine);$chaine = str_replace( "á", "a", $chaine);$chaine = str_replace( "å", "a", $chaine);
    $chaine = str_replace( "ã", "e", $chaine);$chaine = str_replace( "é", "e", $chaine);$chaine = str_replace( "è", "e", $chaine);$chaine = str_replace( "ë", "e", $chaine);$chaine = str_replace( "ê", "e", $chaine);
    $chaine = str_replace( "ò", "o", $chaine);$chaine = str_replace( "ó", "o", $chaine);$chaine = str_replace( "ô", "o", $chaine);$chaine = str_replace( "õ", "o", $chaine);$chaine = str_replace( "ö", "o", $chaine);
    $chaine = str_replace( "ø", "o", $chaine);$chaine = str_replace( "ì", "i", $chaine);$chaine = str_replace( "í", "i", $chaine);$chaine = str_replace( "î", "i", $chaine);$chaine = str_replace( "ï", "i", $chaine);
    $chaine = str_replace( "ù", "u", $chaine);$chaine = str_replace( "ú", "i", $chaine);$chaine = str_replace( "û", "u", $chaine);$chaine = str_replace( "ü", "y", $chaine);$chaine = str_replace( "ý", "y", $chaine);
    $chaine = str_replace( "ñ", "n", $chaine);$chaine = str_replace( "ç", "c", $chaine);$chaine = str_replace( "þ", "p", $chaine);$chaine = str_replace( "ÿ", "y", $chaine);$chaine = str_replace( "æ", "ae", $chaine);
    $chaine = str_replace( "œ", "oe", $chaine);$chaine = str_replace( "ð", "D", $chaine);$chaine = str_replace( "ø", "o", $chaine);
    $chaine = str_replace( "Ä", "A", $chaine);$chaine = str_replace( "Â", "A", $chaine);$chaine = str_replace( "À", "A", $chaine);$chaine = str_replace( "Á", "A", $chaine);$chaine = str_replace( "Å", "A", $chaine);
    $chaine = str_replace( "Ã", "A", $chaine);$chaine = str_replace( "É", "E", $chaine);$chaine = str_replace( "È", "E", $chaine);$chaine = str_replace( "Ë", "E", $chaine);$chaine = str_replace( "Ê", "E", $chaine);
    $chaine = str_replace( "Ò", "O", $chaine);$chaine = str_replace( "Ó", "O", $chaine);$chaine = str_replace( "Ô", "O", $chaine);$chaine = str_replace( "Õ", "O", $chaine);$chaine = str_replace( "Ö", "O", $chaine);
    $chaine = str_replace( "Ø", "O", $chaine);$chaine = str_replace( "Ì", "I", $chaine);$chaine = str_replace( "Í", "I", $chaine);$chaine = str_replace( "Î", "I", $chaine);$chaine = str_replace( "Ï", "I", $chaine);
    $chaine = str_replace( "Ù", "U", $chaine);$chaine = str_replace( "Ú", "U", $chaine);$chaine = str_replace( "Û", "U", $chaine);$chaine = str_replace( "Ü", "U", $chaine);$chaine = str_replace( "Ý", "Y", $chaine);
    $chaine = str_replace( "Ñ", "N", $chaine);$chaine = str_replace( "Ç", "C", $chaine);$chaine = str_replace( "Æ", "AE", $chaine);
    $chaine = str_replace( "Œ", "OE", $chaine);$chaine = str_replace( "Ð", "D", $chaine);
    return $chaine;
    }
function tel_cacateres($chaine)
    {
    $chaine = htmlentities($chaine);
    $chaine = html_entity_decode($chaine,ENT_QUOTES,"ISO-8859-1");
    $chaine = str_replace( "'", "", $chaine);$chaine = str_replace( "-", "", $chaine);$chaine = str_replace( ".", "", $chaine);$chaine = str_replace( " ", "", $chaine);$chaine = str_replace( ",", "", $chaine);$chaine = str_replace( "_", "", $chaine);
    return $chaine;
    }
function produits_caract($chaine)
    {
    $chaine = htmlentities($chaine);
    $chaine = html_entity_decode($chaine,ENT_QUOTES,"ISO-8859-1");
    $chaine = str_replace( "'", " ", $chaine);
    $chaine = str_replace( "<p> </p>", "<br />", $chaine);
    $chaine = str_replace( "</p>", "", $chaine);
    return $chaine;
    }
// FIN FONCTIONS *********************************************************

// RECUPERATION AUTOMATIQUE DE DONNEES **************************************************
$date_update=date("Y-m-d");
$heure_update=date("H:i:s");
$date_update="$date_update $heure_update";
setlocale (LC_TIME, 'fr_FR.utf8','fra');
$date_synchro=(strftime("%A %d %B %Y"));
$date_synchro=mb_strtoupper($date_synchro);
$heure_synchro=(strftime("%H:%M:%S"));
$URL  = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
$URL .= ($_SERVER['QUERY_STRING']!='')? '?' : '';
$URL .= $_SERVER['QUERY_STRING'];
$nb = strpos($URL,'/');
$URL=substr($URL,0,$nb);
$uri='http://'.$URL.'';
$serveur_presta=_DB_SERVER_;
$admin_presta=_DB_USER_;
$mdp_presta=_DB_PASSWD_;
$base_presta=_DB_NAME_;
$prefix_presta=_DB_PREFIX_;
$donnees_lang = Db::getInstance()->GetRow("select * from ".$prefix_presta."configuration where name='PS_LANG_DEFAULT'");
$lang=$donnees_lang['value'];
// FIN RECUPERATION AUTOMATIQUE DE DONNEES **************************************************

// RECUP VERSIONS PRESTA *****************************************************
$version_presta=_PS_VERSION_;
$version_presta=substr($version_presta,0,3);
// FIN RECUP VERSIONS PRESTA *****************************************************

// RECUPERATION DES PARAMETRES *************************************** 
$donnees_recup_des_bases = Db::getInstance()->GetRow("select * from P2D_param where id=1");
$serveur_doli=$donnees_recup_des_bases['serveur_doli'];
$admin_doli=$donnees_recup_des_bases['admin_doli'];
$mdp_doli=$donnees_recup_des_bases['mdp_doli'];
$base_doli=$donnees_recup_des_bases['base_doli'];
$prefix_doli=$donnees_recup_des_bases['prefix_doli'];
$libelle_port=$donnees_recup_des_bases['libelle_port'];
    $chaine=$libelle_port;    
    $chaine= accents_sans("$chaine");   
    $libelle_port=$chaine;
$code_article_port=$donnees_recup_des_bases['code_article_port'];
$label=$donnees_recup_des_bases['prefix_ref_client'];
    $chaine=$label;    
    $chaine= accents_sans("$chaine");   
    $label=$chaine;
$option_image=$donnees_recup_des_bases['option_image'];
$uri=$donnees_recup_des_bases['uri'];
$decremente=$donnees_recup_des_bases['decremente'];                            
$numero_de_commande=$donnees_recup_des_bases['numero_de_commande'];
$mail_achat=$donnees_recup_des_bases['mail_achat'];                 
$valide=$donnees_recup_des_bases ['valide'];
$memo_id=$donnees_recup_des_bases['memo_id'];                   
$stock_doli=$donnees_recup_des_bases['stock_doli'];

// CALCUL DU NOMBRE DE COMMANDES A TRAITER **************************************
$nb_commandes=$donnees_recup_des_bases['nb_commandes'];
$req_max_id_commandes="select max(id_order) from ".$prefix_presta."orders";
$req_max_id_commandes=mysql_query($req_max_id_commandes);
$id_max_commandes=mysql_result($req_max_id_commandes,0,"max(id_order)");
if ($nb_commandes!=0)
    {
    $nb_commandes=$nb_commandes-1;
    $nb_commandes=$id_max_commandes-$nb_commandes;
    }
else
    {
    $nb_commandes=0;
    }
// FIN CALCUL DU NOMBRE DE COMMANDES A TRAITER **************************************

// CALCUL DU NOMBRE DE CLIENTS A TRAITER ************************************
$nb_clients=$donnees_recup_des_bases['nb_clients'];
$req_max_id_clients="select max(id_customer) from ".$prefix_presta."customer";
$req_max_id_clients=mysql_query($req_max_id_clients);
$id_max_clients=mysql_result($req_max_id_clients,0,"max(id_customer)");
if ($nb_clients!=0)
    {
    $nb_clients=$nb_clients-1;
    $nb_clients=$id_max_clients-$nb_clients;
    }
else
    {
    $nb_clients=0;
    }
// FIN CALCUL DU NOMBRE DE CLIENTS A TRAITER ************************************
// FIN RECUPERATION DES PARAMETRES ***************************************

// DEFINITION DE DONNEES *****************************************
$entity=1;
$nbcateg=0;
$type=0;
// FIN DEFINITION DE DONNEES *****************************************

// CONNEXION A DOLIBARR *************************************
mysql_connect("$serveur_doli","$admin_doli","$mdp_doli");
mysql_select_db("$base_doli");
mysql_query("SET NAMES UTF8");
// FIN CONNEXION A DOLIBARR *************************************

// RECUPERATION DE LA VERSION DE DOLIBARR **************************************************
$sql_recup_version_dolibarr="select * from ".$prefix_doli."const where name='MAIN_VERSION_LAST_UPGRADE'";
$result_version_dolibarr = mysql_query($sql_recup_version_dolibarr) or die($sql_recup_version_dolibarr."<br />\n".mysql_error());
$donnees_version_dolibarr = mysql_fetch_array($result_version_dolibarr);
$version_dolibarr=$donnees_version_dolibarr['value'];
$version_dolibarr=substr($version_dolibarr,0,3);
if ($version_dolibarr=="")
    {
    $sql_recup_version_dolibarr="select * from ".$prefix_doli."const where name='MAIN_VERSION_LAST_INSTALL'";
    $result_version_dolibarr = mysql_query($sql_recup_version_dolibarr) or die($sql_recup_version_dolibarr."<br />\n".mysql_error());
    $donnees_version_dolibarr = mysql_fetch_array($result_version_dolibarr);
    $version_dolibarr=$donnees_version_dolibarr['value'];
    $version_dolibarr=substr($version_dolibarr,0,3);
    }
// FIN RECUPERATION DE LA VERSION DE DOLIBARR **************************************************

// CREATION DE LA TABLE PRODUITS COMBINAISONS ***************************************                
mysql_query("CREATE TABLE IF NOT EXISTS P2D_combinaisons(
                                    id_combinaison VARCHAR(30) NOT NULL unique,
                                    id_produit INT(40) NOT NULL,
                                    groupe INT(25) NOT NULL,
                                    type text(3) NOT NULL,
                                    ref varchar(200) NOT NULL,
                                    label varchar(200) NOT NULL unique,
                                    description text(500) NOT NULL,
                                    prix_ht decimal(20,2) NOT NULL,
                                    tva_tx decimal(11,2) NOT NULL,
                                    stock decimal(20,3) NOT NULL,
                                    envente INT(1) NOT NULL,
                                    barcode INT(40) NOT NULL,
                                    weight decimal(20,3) NOT NULL                               
                                    )");                                                                
// FIN CREATION DE LA TABLE PRODUITS COMBINAISONS ***************************************

// EFFACEMENT DE LA TABLE DES COMBINAISONS DE PRODUITS *******************************
if ($memo_id=="0")
    {
    mysql_query("TRUNCATE TABLE P2D_combinaisons");
    }
// FIN EFFACEMENT DE LA TABLE DES COMBINAISONS DE PRODUITS *******************************

// MODIFICATION DE TABLES DE DOLIBARR *************************************************
mysql_query("ALTER TABLE ".$prefix_doli."commande DROP INDEX uk_commande_ref");
//mysql_query("ALTER TABLE ".$prefix_doli."product ADD INDEX uk_commande_ref") Or die ( mysql_error() );
// mysql_query("ALTER TABLE ".$prefix_doli."product MODIFY rowid bigint(30) AUTO_INCREMENT");
// mysql_query("ALTER TABLE ".$prefix_doli."categorie_product MODIFY fk_product bigint(30)");
mysql_query("ALTER TABLE ".$prefix_doli."product MODIFY ref varchar(255)");
mysql_query("ALTER TABLE ".$prefix_doli."product DROP INDEX uk_product_ref"); 
//mysql_query("ALTER TABLE ".$prefix_doli."product ADD INDEX uk_product_ref") Or die ( mysql_error() );
mysql_query("ALTER TABLE ".$prefix_doli."categorie DROP INDEX uk_categorie_ref");
//mysql_query("ALTER TABLE ".$prefix_doli."categorie ADD INDEX uk_categorie_ref") Or die ( mysql_error() );
// FIN MODIFICATION DE TABLES DE DOLIBARR *************************************************

// CREATION USER DANS DOLIBARR ***************************************
$req_id_user="select max(rowid) from ".$prefix_doli."user";
$req_id_user=mysql_query($req_id_user);
$id_user=mysql_result($req_id_user,0,"max(rowid)");
$id_user=$id_user+1;
$sql_recup_verif_user="select * from ".$prefix_doli."user where login='noreply@boutique.fr'";
$result_verif_user = mysql_query($sql_recup_verif_user) or die($sql_recup_verif_user."<br />\n".mysql_error());
$donnees_verif_user = mysql_fetch_array($result_verif_user);
$verif_user=$donnees_verif_user['login'];
if ($verif_user=='noreply@boutique.fr')
    {
    $info_erreur="Erreur de synchro sur : UPDATE USER DANS DOLIBARR - ID User : $rowid_user";//or die($info_erreur."<br />\n".mysql_error())
    $rowid_user=$donnees_verif_user['rowid'];
    mysql_query ("UPDATE ".$prefix_doli."user set login='noreply@boutique.fr',statut=1 where rowid=$rowid_user")
        or die($info_erreur."<br />\n".mysql_error());
        
    }
else
    {
    $info_erreur="Erreur de synchro sur : INSERT USER DANS DOLIBARR - ID User : $rowid_user";//or die($info_erreur."<br />\n".mysql_error())
    $rowid_user=$id_user;
    mysql_query ("INSERT INTO ".$prefix_doli."user (rowid,login,statut) VALUES ($rowid_user,'noreply@boutique.fr',1)")
        or die($info_erreur."<br />\n".mysql_error());
    }
// FIN CREATION USER DANS DOLIBARR ***************************************

// CONNEXION A PRESTASHOP *************************************
mysql_connect("$serveur_presta","$admin_presta","$mdp_presta");
mysql_select_db("$base_presta");
mysql_query("SET NAMES UTF8");     
// FIN CONNEXION A PRESTASHOP *************************************

// CALCUL DU NOMBRE DE PRODUITS *******************************************************
$req_nb_id_produits = mysql_query("SELECT count(distinct(id_product)) FROM ".$prefix_presta."product "); 
$nb_produits=(array_pop(mysql_fetch_row($req_nb_id_produits)));
// FIN CALCUL DU NOMBRE DE PRODUITS *******************************************************

$sql_liste="select * from ".$prefix_presta."product where id_product>'".$memo_id."' order by id_product asc";
$result_liste = mysql_query($sql_liste) or die($sql_liste."<br />\n".mysql_error());
while ($creer = mysql_fetch_array($result_liste))
    {
    $product_id=$creer['id_product'];
    mysql_query("UPDATE P2D_param set memo_id='$product_id' where id=1");
    $donnees_compte = Db::getInstance()->GetRow("select * from P2D_param where id=1");
    $memo_id=$product_id;           
    $fk_user_author=$rowid_user;
//     $donnees_product = Db::getInstance()->GetRow("select * from ".$prefix_presta."product where id_product='".$product_id."'");
        
    // CALCUL DU PRIX DU PRODUIT NORMALEMENT SANS REDUCTION **********************************************
    $donnees_product = Db::getInstance()->GetRow("select * from ".$prefix_presta."product where id_product='".$product_id."'");
    $prix_produit_normal_HT=$donnees_product['price'];

    // VERIFICATION SI REDUCTION / PROMO ******************************************************            
    $donnees_specific_price = Db::getInstance()->GetRow("select * from ".$prefix_presta."specific_price where id_product='".$product_id."'");
    $reduc_valeur=$donnees_specific_price['reduction'];
    $reduction_type=$donnees_specific_price['reduction_type'];
    $date_debut_reduc=$donnees_specific_price['from'];
    $date_debut_reduc = str_replace( "-", " ", $date_debut_reduc);
    $date_debut_reduc = str_replace( ":", " ", $date_debut_reduc);
    $date_debut_reduc = str_replace( " ", "", $date_debut_reduc);
    $date_fin_reduc=$donnees_specific_price['to'];
    $date_fin_reduc = str_replace( "-", " ", $date_fin_reduc);
    $date_fin_reduc = str_replace( ":", " ", $date_fin_reduc);
    $date_fin_reduc = str_replace( " ", "", $date_fin_reduc);
    $date_fin_reduc_lu=$donnees_specific_price['to'];
    $date_jour = date('Y-m-d G:i:s');
    $date_jour = str_replace( "-", " ", $date_jour);
    $date_jour = str_replace( ":", " ", $date_jour);
    $date_jour = str_replace( " ", "", $date_jour);
    $prix_produit_normal_HT=$donnees_product['price'];
    $prix_produit_normal_HT=sprintf("%.2f",$prix_produit_normal_HT);
    if (($date_jour<$date_debut_reduc) or ($date_jour>$date_fin_reduc))
        {
        $prix_produit_normal_HT=$donnees_product['price'];
        $prix_produit_normal_HT=sprintf("%.2f",$prix_produit_normal_HT);
        }
    if (($date_jour>=$date_debut_reduc) and ($date_jour<=$date_fin_reduc))
        {
        if ($reduction_type=="percentage")
            {
            $calcul_valeur_pourcen=$prix_produit_normal_HT*$reduc_valeur;
            $prix_produit_normal_HT=$prix_produit_normal_HT-$calcul_valeur_pourcen;
            $prix_produit_normal_HT=sprintf("%.2f",$prix_produit_normal_HT);
            }
        if (($reduction_type=="amount"))
            {
            $prix_produit_normal_HT=$prix_produit_normal_HT-$reduc_valeur;
            $prix_produit_normal_HT=sprintf("%.2f",$prix_produit_normal_HT);
            }
        }
    if (($date_fin_reduc_lu==="0000-00-00 00:00:00"))
        {
        if ($reduction_type=="percentage")
            {
            $calcul_valeur_pourcen=$prix_produit_normal_HT*$reduc_valeur;
            $prix_produit_normal_HT=$prix_produit_normal_HT-$calcul_valeur_pourcen;
            $prix_produit_normal_HT=sprintf("%.2f",$prix_produit_normal_HT);
            }
        if (($reduction_type=="amount"))
            {
            $prix_produit_normal_HT=$prix_produit_normal_HT-$reduc_valeur;
            $prix_produit_normal_HT=sprintf("%.2f",$prix_produit_normal_HT);
            }
        }
    if($prix_produit_normal_HT<=0)
        {
        $prix_produit_normal_HT=0;
        }
    // FIN VERIFICATION SI REDUCTION / PROMO ******************************************************

    // TAUX TVA PRODUIT NORMAL ***********************************************************************    
    if ($version_presta>"1.3")
        {
        $id_tax_rules_group=$donnees_product['id_tax_rules_group'];
        $donnees_id_tax_rules_group = Db::getInstance()->GetRow("select * from ".$prefix_presta."tax_rule where id_tax_rules_group='".$id_tax_rules_group."'");
        $id_tax=$donnees_id_tax_rules_group['id_tax'];
        $donnees_tax = Db::getInstance()->GetRow("select * from ".$prefix_presta."tax where id_tax='".$id_tax."'");
        $tax_rate_product_normal=$donnees_tax['rate'];
        }
    if ($version_presta<="1.3")
        {
        $id_tax=$donnees_product['id_tax'];
        $donnees_tax = Db::getInstance()->GetRow("select * from ".$prefix_presta."tax where id_tax='".$id_tax."'");
        $tax_rate_product_normal=$donnees_tax['rate'];
        }
    $taux_taxe_produits_normal=$tax_rate_product_normal/100;
    $taux_taxe_produits_normal=$taux_taxe_produits_normal+1;
    $taux_taxe_produits_normal=sprintf("%.2f",$taux_taxe_produits_normal);
    // FIN TAUX TVA PRODUIT NORMAL ***********************************************************************
    
    $prix_produit_normal_HT=sprintf("%.2f",$prix_produit_normal_HT);
    $prix_produit_normal_TTC=$prix_produit_HT*$taux_taxe_produits_normal;
    $prix_produit_normal_TTC=sprintf("%.2f",$prix_produit_normal_TTC);
    $tva_produit_normal=$prix_produit_normal_TTC-$prix_produit_normal_HT;
    $tva_produit_normal=sprintf("%.2f",$tva_produit_normal);
    // FIN CALCUL DU PRIX DU PRODUIT NORMALEMENT SANS REDUCTION **********************************************
        
    // RECUPERATION DES DONNEES DU PRODUIT DANS LA BASE ARTICLES *********************************************
    $active=$donnees_product['active'];
    $ref_produit=$donnees_product['reference'];
        $chaine=$ref_produit;    
        $chaine= produits_caract("$chaine");
        $ref_produit=$chaine;
    $en_vente=$donnees_product['active'];
    $barcode=$donnees_product['ean13'];
    $datec=$donnees_product['date_add'];
    $tms=$donnees_product['date_upd'];
    $weight=$donnees_product['weight'];
    // FIN RECUPERATION DES DONNEES DU PRODUIT DANS LA BASE ARTICLES *********************************************

    // RECUPERATION ID IMAGE ****************************************************
    $donnees_id_image = Db::getInstance()->GetRow("select * from ".$prefix_presta."image where id_product='".$product_id."'");
    $id_image=$donnees_id_image['id_image'];
    // FIN RECUPERATION ID IMAGE ****************************************************
        
    // VERIFICATION DE LA DESCRIPTION PRODUIT **********************************************************    
    $donnees_product_desc = Db::getInstance()->GetRow("select * from ".$prefix_presta."product_lang where id_product='".$product_id."' and id_lang='".$lang."'");
    $label_produit=$donnees_product_desc['name'];
        $chaine=$label_produit;    
        $chaine= produits_caract("$chaine");
        $label_produit=$chaine;
    if($label_produit=="")
        {
        $label_produit="Défaut de Label sur ID $product_id";
        }
    if($ref_produit=="")
        {
        $ref_produit=$label_produit;
        }
    $description_produit=$donnees_product_desc['description'];
        $chaine=$description_produit;    
        $chaine= produits_caract("$chaine");
        $description_produit=$chaine;
    if ($description_produit=="")
        {
        $ref_produit="LA REFERENCE DU PRODUIT N\'EXISTE PLUS";
        $label_produit="LE LABEL DU PRODUIT N\'EXISTE PLUS";
        $description_produit="LA DESCRIPTION DU PRODUIT N\'EXISTE PLUS";
        }                          
    // FIN VERIFICATION DE LA DESCRIPTION PRODUIT **********************************************************
        
    // PRODUIT NORMAL AVEC IMAGES (PAS UNE DECLINAISON) *****************************
    $description_produit_sans_image=$description_produit;

    // IMAGE PRODUIT EXISTE PAS **********************************
    if (($id_image=="") and ($option_image=="checked"))
        {
        $description_produit="$description_produit<br />-<br />";
        }
    // FIN IMAGE PRODUIT EXISTE PAS **********************************

    if (($id_image!="") and ($option_image=="checked"))
        { 
        // IMAGE EXISTE NOUVEAU SYSTEME STOCKAGE ******************************
        if (!$fp = fopen("../../img/p/$product_id-$id_image-home.jpg","r"))
            { 
            $dossier=substr($id_image,0,1);
            $chemin = "../../img/p/$dossier";
            $dossier_suiv=substr($id_image,1,1);
            if($dossier_suiv!="")
                {
                $chemin = "$chemin/$dossier_suiv";
                    $dossier_suiv=substr($id_image,2,1);
                    if($dossier_suiv!="")
                        {
                        $chemin = "$chemin/$dossier_suiv";
                        }
                        $dossier_suiv=substr($id_image,3,1);
                        if($dossier_suiv!="")
                            {
                            $chemin = "$chemin/$dossier_suiv";
                            }
                            $dossier_suiv=substr($id_image,4,1);
                            if($dossier_suiv!="")
                                {
                                $chemin = "$chemin/$dossier_suiv";
                                }
                                $dossier_suiv=substr($id_image,5,1);
                                if($dossier_suiv!="")
                                    {
                                    $chemin = "$chemin/$dossier_suiv";
                                    }
                                    $dossier_suiv=substr($id_image,6,1);
                                    if($dossier_suiv!="")
                                        {
                                        $chemin = "$chemin/$dossier_suiv";
                                        }
                                        $dossier_suiv=substr($id_image,7,1);
                                        if($dossier_suiv!="")
                                            {
                                            $chemin = "$chemin/$dossier_suiv";
                                            }
                                            $dossier_suiv=substr($id_image,8,1);
                                            if($dossier_suiv!="")
                                                {
                                                $chemin = "$chemin/$dossier_suiv";
                                                }
                                                $dossier_suiv=substr($id_image,9,1);
                                                if($dossier_suiv!="")
                                                    {
                                                    $chemin = "$chemin/$dossier_suiv";
                                                    }
                                                    $dossier_suiv=substr($id_image,10,1);
                                                    if($dossier_suiv!="")
                                                        {
                                                        $chemin = "$chemin/$dossier_suiv";
                                                        }
                }
            $chemin = str_replace( "../..", "$uri", $chemin);
            $title =  $ref_produit;
            $title =  str_replace( " ", "_", $title);
            $description_produit="$description_produit<br /><img title=$title src=$chemin/$id_image-home.jpg alt=Produit/>";
            }
        // FIN IMAGE EXISTE NOUVEAU SYSTEME STOCKAGE ******************************

        // IMAGE EXISTE ANCIEN SYSTEME STOCKAGE ******************************            
        else
            {
            $title =  $ref_produit;
            $title =  str_replace( " ", "_", $title);
            $description_produit="$description_produit<br /><img title=$title src=$uri/img/p/$product_id-$id_image-home.jpg alt=Produit/>";                    
            }
        // FIN IMAGE EXISTE ANCIEN SYSTEME STOCKAGE ******************************
        }
    // FIN PRODUIT NORMAL AVEC IMAGES (PAS UNE DECLINAISON) *****************************

    // PRODUIT NORMAL SANS IMAGE ********************************************************************        
    if ($option_image!="checked")
        {
        $description_produit=$description_produit;
        }
    // FIN PRODUIT NORMAL SANS IMAGE ********************************************************************

    // RATTACHEMENT DU PRODUIT A LA CATEGORIE ***************************************************        
    $sql_category_product="select * from ".$prefix_presta."category_product where id_product='".$product_id."'";
    $result_category_product = mysql_query($sql_category_product) or die($sql_category_product."<br />\n".mysql_error());
    while ($creer_category_product = mysql_fetch_array($result_category_product))
        {
        $id_category_prod=$creer_category_product['id_category'];
        $info_erreur="Erreur de synchro sur : RATTACHEMENT DU PRODUIT A LA CATEGORIE - ID CATEGORIE : $id_category_prod - ID PRODUIT : $product_id";//or die($info_erreur."<br />\n".mysql_error())
        mysql_connect("$serveur_doli","$admin_doli","$mdp_doli");
        mysql_select_db("$base_doli");
        mysql_query("SET NAMES UTF8");
        mysql_query ("INSERT INTO ".$prefix_doli."categorie_product (fk_categorie,fk_product) 
            VALUES ('".$id_category_prod."','".$product_id."')"); 
        mysql_connect("$serveur_presta","$admin_presta","$mdp_presta");
        mysql_select_db("$base_presta");     
        mysql_query("SET NAMES UTF8");
        }
    // FIN RATTACHEMENT DU PRODUIT A LA CATEGORIE ***************************************************

    mysql_connect("$serveur_doli","$admin_doli","$mdp_doli");
    mysql_select_db("$base_doli");
    mysql_query("SET NAMES UTF8");

    // EXPORT PRODUIT VERS DOLIBARR *************************************************                
    $info_erreur="Erreur de synchro sur : EXPORT PRODUIT VERS DOLIBARR - ID PRODUIT : $product_id - REFERENCE PRODUIT : $ref_produit";//or die($info_erreur."<br />\n".mysql_error())
    if ($version_dolibarr>="3")
        {
        mysql_query ("INSERT INTO ".$prefix_doli."product (rowid,datec,tms,ref,entity,label,description,note,price,price_ttc,tva_tx,tosell,barcode,weight,finished) 
            VALUES ($product_id,'$datec','$date_update','$product_id','$entity','$ref_produit','$ref_produit','$description_produit','$prix_produit_normal_HT','$prix_produit_normal_TTC','$tax_rate_product_normal','$active','$barcode','$weight',1)") 
                or mysql_query ("UPDATE ".$prefix_doli."product set datec='$datec',tms='$date_update',ref='$product_id',entity='$entity',label='$ref_produit',description='$ref_produit',note='$description_produit',price='$prix_produit_normal_HT',price_ttc='$prix_produit_normal_TTC',tva_tx='$tax_rate_product_normal',tosell='$active',barcode='$barcode',weight='$weight',finished=1 where rowid=$product_id")
                    or die($info_erreur."<br />\n".mysql_error());
        }
    if ($version_dolibarr<"3")
        {
        mysql_query ("INSERT INTO ".$prefix_doli."product (rowid,datec,tms,ref,entity,label,description,note,price,price_ttc,tva_tx,envente,barcode,weight,stock) 
            VALUES ($product_id,'$datec','$date_update','$product_id','$entity','$ref_produit','$ref_produit','$description_produit','$prix_produit_normal_HT','$prix_produit_normal_TTC','$tax_rate_product_normal','$active','$barcode','$weight',1)")  
                or mysql_query ("UPDATE ".$prefix_doli."product set datec='$datec',tms='$date_update',ref='$product_id',entity='$entity',label='$ref_produit',description='$ref_produit',note='$description_produit',price='$prix_produit_normal_HT',price_ttc='$prix_produit_normal_TTC',tva_tx='$tax_rate_product_normal',envente='$active',barcode='$barcode',weight='$weight',stock=1 where rowid=$product_id")
                    or die($info_erreur."<br />\n".mysql_error());
        }
    // FIN EXPORT PRODUIT VERS DOLIBARR *************************************************
    
//* SPE FSO ****************************************************************************************************************
//                 mysql_query ("INSERT INTO ".$prefix_doli."product_fournisseur (rowid,fk_product,fk_soc,ref_fourn,entity,fk_user_author) 
//                     VALUES ($product_id,$product_id,1,'$ref_produit',1,2)") Or
//                       mysql_query ("UPDATE ".$prefix_doli."product_fournisseur set fk_product=$product_id,fk_soc=1,ref_fourn='$ref_produit',entity=1,fk_user_author=2 where rowid='".$product_id."'")  Or die ( mysql_error() );//
// 
//                 mysql_query ("INSERT INTO ".$prefix_doli."product_fournisseur_price (rowid,fk_product_fournisseur,price,quantity,unitprice,fk_user) 
//                     VALUES ($product_id,$product_id,0,1,0,2)") Or die ( mysql_error() );//
// 
//                 mysql_query ("INSERT INTO ".$prefix_doli."product_fournisseur_price_log (rowid,fk_product_fournisseur,price,quantity,fk_user) 
//                     VALUES ($product_id,$product_id,0,1,2)") Or die ( mysql_error() );//
//*****************************************************************************************************************

    // RATTACHEMENT DU PRODUIT AU STOCK CENTRAL *********************************************        
    $sql_recup_verif_product_stock="select * from ".$prefix_doli."product_stock where fk_product='".$product_id."'";
    $result_verif_product_stock = mysql_query($sql_recup_verif_product_stock) or die($sql_recup_verif_product_stock."<br />\n".mysql_error());
    $donnees_verif_product_stock = mysql_fetch_array($result_verif_product_stock);
    $verif_product_stock=$donnees_verif_product_stock['rowid'];
    if ($verif_product_stock=="")
        {
        $info_erreur="Erreur de synchro sur : RATTACHEMENT DU PRODUIT AU STOCK CENTRAL - ID PRODUIT : $product_id";//or die($info_erreur."<br />\n".mysql_error())
        mysql_query ("INSERT INTO ".$prefix_doli."product_stock (tms,fk_product,fk_entrepot,reel) 
            VALUES ('$datec',$product_id,1,0)")
                or die($info_erreur."<br />\n".mysql_error());
        }
    // FIN RATTACHEMENT DU PRODUIT AU STOCK CENTRAL *********************************************

    mysql_connect("$serveur_presta","$admin_presta","$mdp_presta");
    mysql_select_db("$base_presta");
    mysql_query("SET NAMES UTF8");     

    // VERIFICATION SI IL EXISTE DES ATTRIBUTS A CE PRODUIT ******************************************************
    if ($declinaisons!="") 
        {
        $verif_attribut = Db::getInstance()->GetRow("select * from ".$prefix_presta."product_attribute where id_product='".$product_id."'");
        $attribut_existe=$verif_attribut['id_product_attribute'];
        if ($attribut_existe!="")
            {
            $sql_declinaisons="select * from ".$prefix_presta."product_attribute where id_product='".$product_id."'";
            $result_declinaisons = mysql_query($sql_declinaisons) or die($sql_declinaisons."<br />\n".mysql_error());
            //$nb_declinaisons=0;
            while ($creer_declinaisons = mysql_fetch_array($result_declinaisons) )
                {
                //$nb_declinaisons=$nb_declinaisons+1;
                $id_product_attribute=$creer_declinaisons['id_product_attribute'];
                // RECUPERATION DES DONNEES DE L'ATTRIBUT **************************************************************************
                $donnees_id_image = Db::getInstance()->GetRow("select * from ".$prefix_presta."product_attribute_image where id_product_attribute='".$id_product_attribute."'");
                $id_image=$donnees_id_image['id_image']; 
                $ref_attribut=$creer_declinaisons['reference'];
                    $chaine=$ref_attribut;    
                    $chaine= produits_caract("$chaine");
                    $ref_attribut=$chaine;
                $supplier_reference =$creer_declinaisons['supplier_reference'];
                    $chaine=$supplier_reference;    
                    $chaine= produits_caract("$chaine");
                    $supplier_reference=$chaine;
                $location =$creer_declinaisons['location'];
                $barcode =$creer_declinaisons['ean13'];
                $upc =$creer_declinaisons['upc'];
                $wholesale_price =$creer_declinaisons['wholesale_price'];
                $wholesale_price=sprintf("%.2f",$wholesale_price);
                $price_att =$creer_declinaisons['price'];
                $price_att=sprintf("%.2f",$price_att);
                $ecotax =$creer_declinaisons['ecotax'];
                $ecotax=sprintf("%.2f",$ecotax);
                $quantity =$creer_declinaisons['quantity'];
                $weight =$creer_declinaisons['weight'];
                $unit_price_impact =$creer_declinaisons['unit_price_impact'];
                $price_attribut_HT= $price_att+$prix_produit_normal_HT;
                $price_attribut_HT=sprintf("%.2f",$price_attribut_HT);
                $price_attribut_TTC= $price_attribut_HT*$taux_taxe_produits_normal;
                $price_attribut_TTC=sprintf("%.2f",$price_attribut_TTC);
                $donnees_product_attribute_combination = Db::getInstance()->GetRow("select * from ".$prefix_presta."product_attribute_combination where id_product_attribute='".$id_product_attribute."'");
                $product_attribute_combination=$donnees_product_attribute_combination['id_attribute'];
                $donnees_attribute = Db::getInstance()->GetRow("select * from ".$prefix_presta."attribute where id_attribute='".$product_attribute_combination."'");
                $id_attribute_group =$donnees_attribute['id_attribute_group'];
                $donnees_attribute_group_lang = Db::getInstance()->GetRow("select * from ".$prefix_presta."attribute_group_lang where id_attribute_group='".$id_attribute_group."' and id_lang='".$lang."'");
                $attribute_group_name =$donnees_attribute_group_lang['name'];
                    $chaine=$attribute_group_name;    
                    $chaine= produits_caract("$chaine");
                    $attribute_group_name=$chaine;
                $donnees_attribute_lang = Db::getInstance()->GetRow("select * from ".$prefix_presta."attribute_lang where id_attribute='".$product_attribute_combination."' and id_lang='".$lang."'");
                $attribute_lang_name =$donnees_attribute_lang['name'];
                    $chaine=$attribute_lang_name;    
                    $chaine= produits_caract("$chaine");
                    $attribute_lang_name=$chaine;
                $id_attribute_lang_name=$donnees_attribute_lang['id_attribute'];
                // FIN RECUPERATION DES DONNEES DE L'ATTRIBUT **************************************************************************
                
                // VERIFICATION DU NOM DE L'ATTRIBUT **********************************************************
                if ($attribute_lang_name=="")
                    {
                    $attribute_lang_name="*";
                    $attribute_group_name="DECLINAISON";  
                    }
                if ($ref_attribut=="")
                    {
                    $ref_attribut="$ref_produit $attribute_lang_name";
                    }
                if($ref_attribut==$ref_produit)
                    {
                    $ref_attribut="$ref_produit $attribute_lang_name *";
                    }
                // FIN VERIFICATION DU NOM DE L'ATTRIBUT **********************************************************
                    
                // DEFINITION DU LABEL ET DE LA DESCRIPTION DE L'ATTRIBUT *******************************************
                $label_produit_attribut="$label_produit<br /> $attribute_group_name = $attribute_lang_name";
                $description_produit_attribut="$description_produit_sans_image<br /><strong> $attribute_group_name = $attribute_lang_name</strong>";
                // FIN DEFINITION DU LABEL ET DE LA DESCRIPTION DE L'ATTRIBUT *******************************************
                    
                // IMAGE ATTRIBUT N'EXISTE PAS **********************************
                if (($id_image=="") and ($option_image!=""))
                    {
                    $description_produit_attribut="$description_produit_attribut<br />-<br />";
                    }
                // FIN IMAGE ATTRIBUT N'EXISTE PAS **********************************
                    
                if (($id_image!="") and ($option_image!=""))
                    {
                    // IMAGE EXISTE NOUVEAU SYSTEME STOCKAGE ******************************
                    if (!$fp = fopen("../../img/p/$id_product_attribute-$id_image-home.jpg","r")) 
                        { 
                        $dossier=substr($id_image,0,1);
                        $chemin = "../../img/p/$dossier";
                        $dossier_suiv=substr($id_image,1,1);
                        if($dossier_suiv!="")
                            {
                            $chemin = "$chemin/$dossier_suiv";
                                $dossier_suiv=substr($id_image,2,1);
                                if($dossier_suiv!="")
                                    {
                                    $chemin = "$chemin/$dossier_suiv";
                                    }
                                    $dossier_suiv=substr($id_image,3,1);
                                    if($dossier_suiv!="")
                                        {
                                        $chemin = "$chemin/$dossier_suiv";
                                        }
                                        $dossier_suiv=substr($id_image,4,1);
                                        if($dossier_suiv!="")
                                            {
                                            $chemin = "$chemin/$dossier_suiv";
                                            }
                                            $dossier_suiv=substr($id_image,5,1);
                                            if($dossier_suiv!="")
                                                {
                                                $chemin = "$chemin/$dossier_suiv";
                                                }
                                                $dossier_suiv=substr($id_image,6,1);
                                                if($dossier_suiv!="")
                                                    {
                                                    $chemin = "$chemin/$dossier_suiv";
                                                    }
                                                    $dossier_suiv=substr($id_image,7,1);
                                                    if($dossier_suiv!="")
                                                        {
                                                        $chemin = "$chemin/$dossier_suiv";
                                                        }
                                                        $dossier_suiv=substr($id_image,8,1);
                                                        if($dossier_suiv!="")
                                                            {
                                                            $chemin = "$chemin/$dossier_suiv";
                                                            }
                                                            $dossier_suiv=substr($id_image,9,1);
                                                            if($dossier_suiv!="")
                                                                {
                                                                $chemin = "$chemin/$dossier_suiv";
                                                                }
                                                                $dossier_suiv=substr($id_image,10,1);
                                                                if($dossier_suiv!="")
                                                                    {
                                                                    $chemin = "$chemin/$dossier_suiv";
                                                                    }
                            }
                        $chemin = str_replace( "../..", "$uri", $chemin);
                        $title =  $ref_attribut;
                        $title =  str_replace( " ", "_", $title);
                        $description_produit_attribut="$description_produit_attribut<br /><img title=$title src=$chemin/$id_image-home.jpg alt=Produit/>";
                        } 
                    // FIN IMAGE EXISTE NOUVEAU SYSTEME STOCKAGE ******************************
                        
                    // IMAGE EXISTE ANCIEN SYSTEME STOCKAGE ******************************
                    else
                        { 
                        $title =  $ref_attribut;
                        $title =  str_replace( " ", "_", $title);
                        $description_produit_attribut="$description_produit_attribut<br /><img title=$title src=$uri/img/p/$id_product_attribute-$id_image-home.jpg alt=Produit/>";
                        }
                    // FIN IMAGE EXISTE ANCIEN SYSTEME STOCKAGE ******************************
                    
                    }
                    
                // PRODUIT DECLINAISON SANS IMAGE ***********************************************************  
                if ($option_image=="")
                    {
                    $description_produit_attribut=$description_produit_attribut;
                    }
                // FIN PRODUIT DECLINAISON SANS IMAGE ***********************************************************
                    
                    
                // DEFINITION ID DU PRODUIT DECLINAISON ************************************* 
                $id_product_attribut_creer="99$id_product_attribute";
//                 $memo_id_product_attribut_creer=$id_product_attribut_creer;
                // FIN DEFINITION ID DU PRODUIT DECLINAISON *************************************
                   
                mysql_connect("$serveur_doli","$admin_doli","$mdp_doli");
                mysql_select_db("$base_doli");
                mysql_query("SET NAMES UTF8"); 
    
                // INSERTION ATTRIBUT DANS LA TABLE ATTRIBUTS *********************************************************************
                mysql_query ("INSERT INTO P2D_combinaisons (id_combinaison,id_produit,groupe,type,ref,label,description,prix_ht,tva_tx,envente,barcode,weight)
                    VALUES ($id_product_attribut_creer,'$product_id',$id_attribute_group,'D','$ref_attribut','$label_produit_attribut','$description_produit_attribut','$price_attribut_HT','$tax_rate_product_normal','$active','$barcode','$weight')") 
                        Or mysql_query ("UPDATE P2D_combinaisons set id_produit='$product_id',groupe='$id_attribute_group',type='D',ref='$ref_attribut',label='$label_produit_attribut',description='$description_produit_attribut',prix_ht='$price_attribut_HT',tva_tx='$tax_rate_product_normal',envente='$active',barcode='$barcode',weight='$weight' where id_combinaison='".$id_product_attribut_creer."'");
//                 $boucle_produits=$boucle_produits+1;
                // FIN INSERTION ATTRIBUT DANS LA TABLE ATTRIBUTS *********************************************************************
    
// INSERTION ATTRIBUT DANS LA TABLE ATTRIBUTS *********************************************************************
// mysql_query ("INSERT INTO P2D_combinaisons (id_combinaison,id_produit)
//     VALUES ('$id_product_attribut_creer','$product_id')") or die($product_id."<br />\n".mysql_error());
// FIN INSERTION ATTRIBUT DANS LA TABLE ATTRIBUTS *********************************************************************

                
                
                
                // REMISE A 0 DE DONNEES *****************************************************************
                $price_att =0;
                $unit_price_impact =0;
                $id_groupe_verif="$id_attribute_group";
                $id_product_attribut_creer="$product_id$id_attribute_group$product_attribute_combination";
                $label_attributs_combinaisons="$label_produit ";
                // FIN REMISE A 0 DE DONNEES *****************************************************************
    
                mysql_connect("$serveur_presta","$admin_presta","$mdp_presta");
                mysql_select_db("$base_presta");
                mysql_query("SET NAMES UTF8");
                }
            mysql_connect("$serveur_presta","$admin_presta","$mdp_presta");
            mysql_select_db("$base_presta");
            mysql_query("SET NAMES UTF8");
            }
        }
    mysql_connect("$serveur_presta","$admin_presta","$mdp_presta");
    mysql_select_db("$base_presta");
    mysql_query("SET NAMES UTF8");
    }

    // CREATION/INSERT DES DECLINAISONS DANS DOLIBARR **********************************************************
    if ($declinaisons!="") 
        {
        mysql_connect("$serveur_doli","$admin_doli","$mdp_doli");
        mysql_select_db("$base_doli");
        mysql_query("SET NAMES UTF8");
        $sql_creer_combinaisons="select * from P2D_combinaisons";
        $result_creer_combinaisons = mysql_query($sql_creer_combinaisons) or die($sql_creer_combinaisons."<br />\n".mysql_error());
        while ($creer_creer_combinaisons = mysql_fetch_array($result_creer_combinaisons))
            {
            $id_prod=$creer_creer_combinaisons['id_produit'];
            $id_product_attribut_creer=$creer_creer_combinaisons['id_combinaison'];
            $id_product_attribut_creer = (int)$id_product_attribut_creer;
            $label_produit_attribut=$creer_creer_combinaisons['label'];
            $ref_attribut=$creer_creer_combinaisons['ref'];
            $description_produit_attribut=$creer_creer_combinaisons['description'];
            $price_attribut_HT=$creer_creer_combinaisons['prix_ht'];
            $tax_rate_product_normal=$creer_creer_combinaisons['tva_tx'];
            $active=$creer_creer_combinaisons['envente'];
            $barcode=$creer_creer_combinaisons['barcode'];
             
            // EXPORT PRODUIT VERS DOLIBARR *************************************************                
            $info_erreur="Erreur de synchro sur un PRODUIT --> ID Produit: $id_product_attribut_creer - Ref Produit : $ref_attribut - Note : $description_produit_attribut --> Verifiez cette fiche...!!!";//or die($info_erreur."<br />\n".mysql_error())
            if ($version_dolibarr>="3.0")
                {
            mysql_query ("INSERT INTO ".$prefix_doli."product (rowid,ref,entity,label,description,note) 
                VALUES ($id_product_attribut_creer,$id_product_attribut_creer,'$entity','$ref_attribut','$ref_attribut','$description_produit_attribut')") 
                    or mysql_query ("UPDATE ".$prefix_doli."product set ref=$id_product_attribut_creer,entity='$entity',label='$ref_attribut',description='$ref_attribut',note='$description_produit_attribut' where rowid=$id_product_attribut_creer")
                        or die($info_erreur."<br />\n".mysql_error());;
                }
            if ($version_dolibarr<"3.0")
                {
            mysql_query ("INSERT INTO ".$prefix_doli."product (rowid,datec,tms,ref,entity,label,description,note,price,tva_tx,envente,barcode,stock) 
                VALUES ($id_product_attribut_creer,'$datec','$date_update',$id_product_attribut_creer,'$entity','$ref_attribut','$ref_attribut','$description_produit_attribut','$price_attribut_HT','$tax_rate_product_normal','$active','$barcode',1)")  
                    or mysql_query ("UPDATE ".$prefix_doli."product set datec='$datec',tms='$date_update',ref=$id_product_attribut_creer,entity='$entity',label='$ref_attribut',description='$ref_attribut',note='$description_produit_attribut',price='$price_attribut_HT',tva_tx='$tax_rate_product_normal',envente='$active',barcode='$barcode',stock=1 where rowid=$id_product_attribut_creer")
                        or die($info_erreur."<br />\n".mysql_error());;
                }
            // FIN EXPORT PRODUIT VERS DOLIBARR *************************************************

            mysql_connect("$serveur_presta","$admin_presta","$mdp_presta");
            mysql_select_db("$base_presta");
            mysql_query("SET NAMES UTF8");     
            
            // RATTACHEMENT DE L'ATTRIBUT A LA CATEGORIE ***************************************************        
            $sql_category_product="select * from ".$prefix_presta."category_product where id_product='".$id_prod."'";
            $result_category_product = mysql_query($sql_category_product) or die($sql_category_product."<br />\n".mysql_error());
            while ($creer_category_product = mysql_fetch_array($result_category_product))
                {
                $id_category_prod=$creer_category_product['id_category'];
                mysql_connect("$serveur_doli","$admin_doli","$mdp_doli");
                mysql_select_db("$base_doli");
                mysql_query("SET NAMES UTF8");
                mysql_query ("INSERT INTO ".$prefix_doli."categorie_product (fk_categorie,fk_product) 
                    VALUES ('".$id_category_prod."','".$id_product_attribut_creer."')"); 
                mysql_connect("$serveur_presta","$admin_presta","$mdp_presta");
                mysql_select_db("$base_presta");     
                mysql_query("SET NAMES UTF8");
                }
            // FIN RATTACHEMENT DE L'ATTRIBUT A LA CATEGORIE ***************************************************

            mysql_connect("$serveur_doli","$admin_doli","$mdp_doli");
            mysql_select_db("$base_doli");
            mysql_query("SET NAMES UTF8");

            // RATTACHEMENT DE L'ATTRIBUT AU STOCK CENTRAL *********************************************        
            $sql_recup_verif_product_stock="select * from ".$prefix_doli."product_stock where fk_product='".$id_product_attribut_creer."'";
            $result_verif_product_stock= mysql_query($sql_recup_verif_product_stock) or die($sql_recup_verif_product_stock."<br />\n".mysql_error());
            $donnees_verif_product_stock = mysql_fetch_array($result_verif_product_stock);
            $donnees_verif_product_stock = Db::getInstance()->GetRow("select * from ".$prefix_doli."product_stock where fk_product='".$id_product_attribut_creer."'");
            $verif_product_stock=$donnees_verif_product_stock['rowid'];
            if ($verif_product_stock=="")
                {
                mysql_query ("INSERT INTO ".$prefix_doli."product_stock (tms,fk_product,fk_entrepot,reel) 
                    VALUES ('$datec','$id_product_attribut_creer',1,0)");
                }
            // FIN RATTACHEMENT DE L'ATTRIBUT AU STOCK CENTRAL *********************************************
            
            mysql_connect("$serveur_doli","$admin_doli","$mdp_doli");
            mysql_select_db("$base_doli");
            mysql_query("SET NAMES UTF8");
        }
    }
// FIN CREATION/INSERT DES DECLINAISONS DANS DOLIBARR **********************************************************
    
    mysql_connect("$serveur_presta","$admin_presta","$mdp_presta");
    mysql_select_db("$base_presta");
    mysql_query("SET NAMES UTF8");
    
// CREATION DES CATEGORIES *********************************************************************************
$sql_creer_category="select * from ".$prefix_presta."category";
$result_creer_category = mysql_query($sql_creer_category) or die($sql_creer_category."<br />\n".mysql_error());
while ($creer = mysql_fetch_assoc($result_creer_category) )
    {
    $id_categ=$creer['id_category'];
    $id_categ_parent=$creer['id_parent'];
    if ($id_categ_parent=="1")
        {
        $id_categ_parent="0";
        }
    $visible=$creer['active'];
    if($visible=="0")
        {
        $id_categ_parent="1";
        }
    $donnees_creer_category_lang = Db::getInstance()->GetRow("select * from ".$prefix_presta."category_lang where id_category='".$id_categ."' and id_lang='$lang'");
    $label_creer_category=$donnees_creer_category_lang['name'];
        $chaine=$label_creer_category;    
        $chaine= produits_caract("$chaine");
        $label_creer_category=$chaine;
    $description_creer_category=$donnees_creer_category_lang['description'];
        $chaine=$description_creer_category;    
        $chaine= produits_caract("$chaine");
        $description_creer_category=$chaine;
    $link_rewrite_creer_category=$donnees_creer_category_lang['link_rewrite'];
    
    mysql_connect("$serveur_doli","$admin_doli","$mdp_doli");
    mysql_select_db("$base_doli");
    mysql_query("SET NAMES UTF8");
    
    // RECUPERATION DE LA VERSION DE DOLIBARR **************************************************
    $sql_recup_version_dolibarr="select * from ".$prefix_doli."const where name='MAIN_VERSION_LAST_UPGRADE'";
    $result_version_dolibarr = mysql_query($sql_recup_version_dolibarr) or die($sql_recup_version_dolibarr."<br />\n".mysql_error());
    $donnees_version_dolibarr = mysql_fetch_array($result_version_dolibarr);
    $version_dolibarr=$donnees_version_dolibarr['value'];
    $version_dolibarr=substr($version_dolibarr,0,3);
    if ($version_dolibarr=="")
        {
        $sql_recup_version_dolibarr="select * from ".$prefix_doli."const where name='MAIN_VERSION_LAST_INSTALL'";
        $result_version_dolibarr = mysql_query($sql_recup_version_dolibarr) or die($sql_recup_version_dolibarr."<br />\n".mysql_error());
        $donnees_version_dolibarr = mysql_fetch_array($result_version_dolibarr);
        $version_dolibarr=$donnees_version_dolibarr['value'];
        $version_dolibarr=substr($version_dolibarr,0,3);
        }
    // FIN RECUPERATION DE LA VERSION DE DOLIBARR **************************************************

    // INSERTION CATEGORIE DANS DOLIBARR ******************************************
    if ($version_dolibarr<"3.3")
        {
        if (($id_categ!="0") and ($id_categ!="1") and ($label_creer_category!="Root"))
            {
            $info_erreur="Erreur de synchro sur : CREATION CATEGORIE PARENTE - ID : $id_categ - LABEL : $label_creer_category - DESCRIPTION : $description_creer_category";//or die($info_erreur."<br />\n".mysql_error())
            mysql_query ("INSERT INTO ".$prefix_doli."categorie (rowid,label,description,visible,type) 
                VALUES ('$id_categ','$label_creer_category','$description_creer_category','$visible','$type')") 
                    or mysql_query ("UPDATE ".$prefix_doli."categorie set label='$label_creer_category',description='$description_creer_category',visible='$visible' where rowid='".$id_categ."'")
                        or die($info_erreur."<br />\n".mysql_error());
            }
        if ($id_creer_category_parent!="0")
            {
            $info_erreur="Erreur de synchro sur : CREATION SOUS CATEGORIE - ID CATEGORIE FILLE : $id_creer_category - ID CATEGORIE PARENT : $id_categ_parent";//or die($info_erreur."<br />\n".mysql_error())
            mysql_query ("INSERT INTO ".$prefix_doli."categorie_association (fk_categorie_mere,fk_categorie_fille) 
                VALUES ('$id_categ_parent','$id_creer_category')") 
                    or mysql_query ("UPDATE ".$prefix_doli."categorie_association set fk_categorie_mere='$id_categ_parent' where fk_categorie_fille='".$id_categ."'")
                        or die($info_erreur."<br />\n".mysql_error());
            }
        }            
    if ($version_dolibarr>="3.3")
        {
        if (($id_categ!="0") and ($id_categ!="1") and ($label_creer_category!="Root"))
            {
            $info_erreur="Erreur de synchro sur : CREATION CATEGORIE - ID CATEGORIE FILLE : $id_categ - ID CATEGORIE PARENT : $id_categ_parent - LABEL : $label_creer_category - DESCRIPTION : $description_creer_category";//or die($info_erreur."<br />\n".mysql_error())
            mysql_query ("INSERT INTO ".$prefix_doli."categorie (rowid,fk_parent,label,description,visible,type) 
                VALUES ('$id_categ','$id_categ_parent','$label_creer_category','$description_creer_category','$visible','$type')") 
                    or mysql_query ("UPDATE ".$prefix_doli."categorie set fk_parent='$id_categ_parent',label='$label_creer_category',description='$description_creer_category',visible='$visible' where rowid='".$id_categ."'")
                        or die($info_erreur."<br />\n".mysql_error());
            }
        }
    // FIN INSERTION CATEGORIE DANS DOLIBARR ******************************************
        
    $nbcateg=$nbcateg+1;
    mysql_connect("$serveur_presta","$admin_presta","$mdp_presta");
    mysql_select_db("$base_presta");
    mysql_query("SET NAMES UTF8");
    }
// FIN CREATION DES CATEGORIES *********************************************************************************

// REMISE A 0 DES MEMORISATIONS **********************************************************************
mysql_query("UPDATE P2D_param set memo_id=0 where id=1") Or die ( mysql_error() );
// FIN REMISE A 0 DES MEMORISATIONS **********************************************************************

// DEFINITION DE L'AFFICHAGE *************************************************************
mysql_connect("$serveur_presta","$admin_presta","$mdp_presta");
mysql_select_db("$base_presta");
mysql_query("SET NAMES UTF8");
$echo ='';
$echo =''.$echo.'Le '.$date_synchro.' / '.$heure_synchro.'\n';
$echo =''.$echo.'\n';
$echo =''.$echo.'[ SYNCHRONISATION REUSSIE ]\n';
$echo =''.$echo.'\n';
$echo =''.$echo.'---------------------------------------------------\n';
$echo =''.$echo.'Il y a  :  '.$nb_produits.'  Produits dans la base (hors attributs produits)\n';
$echo =''.$echo.'\n';
$echo =''.$echo.''.$nbcateg.'  Categories creees ou mises a jour\n';
$echo =''.$echo.'---------------------------------------------------\n';
$echo =''.$echo.'\n';
$echo =''.$echo.'^^^^^^^^^^^^^^^^^^^^\n';
$echo =''.$echo.'Info Configuration :\n';
$echo =''.$echo.'PrestaShop : '.$version_presta.' / Dolibarr : '.$version_dolibarr.' \n';
$echo =''.$echo.'^^^^^^^^^^^^^^^^^^^^\n';
$echo =''.$echo.'\n';
// FIN DEFINITION DE L'AFFICHAGE *************************************************************

//************************************** FIN CRON *******************************************************

?>
