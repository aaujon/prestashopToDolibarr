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
$total_adresses=0;
$total_comptes=0;
$boucle=0;
// FIN DEFINITION DE DONNEES *****************************************

$sql="select * from ".$prefix_presta."customer where id_customer>='$nb_clients' order by id_customer asc";
$result = mysql_query($sql) or die($sql."<br />\n".mysql_error());
while ($donnees_customer = mysql_fetch_array($result) )
    {
    $id_customer=$donnees_customer['id_customer'];
    
    // RECUPERATION DONNEES DU CLIENT *************************************************
    $donnees_customer = Db::getInstance()->GetRow("select * from ".$prefix_presta."customer where id_customer='".$id_customer."'");
    $id_gender=$donnees_customer['id_gender'];
    $note=$donnees_customer['note'];
        $chaine=$note;    
        $chaine= accents_minuscules("$chaine");
        $note=$chaine;
    $birthday=$donnees_customer['birthday'];
    if ($id_gender==9)
        {
        $civilite="";
        }
    if ($id_gender==1)
        {
        $civilite="MR";
        }
    if ($id_gender==2)
        {
        $civilite="MME";
        }
    $mail=$donnees_customer['email'];
    // FIN RECUPERATION DONNEES DU CLIENT *************************************************
    
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
    
        // DETERMINATION PAYS DOLIBARR *****************************************************************
        $sql_recup_country_doli="select * from ".$prefix_doli."c_pays where code='".$iso_code_country."'";
        $result_country_doli = mysql_query($sql_recup_country_doli) or die($sql_recup_country_doli."<br />\n".mysql_error());
        $donnees_country_doli = mysql_fetch_array($result_country_doli);
        $country_doli=$donnees_country_doli['rowid'];
        // FIN DETERMINATION PAYS DOLIBARR *****************************************************************
    
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
    
        // DETERMINATION ID CLIENT DOLIBARR ******************************************    
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
        // FIN DETERMINATION ID CLIENT DOLIBARR ******************************************
    
        // RECUPERATION DU MASQUE CODE CLIENT *******************************
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
        // FIN RECUPERATION DU MASQUE CODE CLIENT *******************************
    
        // CREATION DE LA FICHE SOCIETE *****************************************
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
        // FIN CREATION DE LA FICHE SOCIETE *****************************************     
        
        // CREATION DE LA FICHE CONTACT ***********************************************
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
        
        $total_adresses=$total_adresses+1;
        mysql_connect("$serveur_presta","$admin_presta","$mdp_presta");
        mysql_select_db("$base_presta");
        mysql_query("SET NAMES UTF8");
        $boucle=$boucle+1;          
        }
    $boucle=0;
    $total_comptes=$total_comptes+1;
    $boucle=0;
    }
$nb_id_a_traiter= $id_max_clients-$nb_clients+1;
   
// DEFINITION DE L'AFFICHAGE *************************************************************
$echo ='';
$echo =''.$echo.'Le '.$date_synchro.' à '.$heure_synchro.'\n';
$echo =''.$echo.'\n';
$echo =''.$echo.'[ SYNCHRONISATION REUSSIE ]\n';
$echo =''.$echo.'\n';
$echo =''.$echo.'---------------------------------------------------\n';
$echo =''.$echo.'Nombre ID fiches clients à traiter : '.$nb_id_a_traiter.'\n';
$echo =''.$echo.'\n';
$echo =''.$echo.'Total des Fiches Clients traitées : '.$total_comptes.'\n';
$echo =''.$echo.'Total des Adresses Clients traitées : '.$total_adresses.'\n';
$echo =''.$echo.'---------------------------------------------------\n';
$echo =''.$echo.'\n';
$echo =''.$echo.'^^^^^^^^^^^^^^^^^^^^\n';
$echo =''.$echo.'Info Configuration :\n';
$echo =''.$echo.'PrestaShop : '.$version_presta.' / Dolibarr : '.$version_dolibarr.' \n';
$echo =''.$echo.'^^^^^^^^^^^^^^^^^^^^\n';
$echo =''.$echo.'\n';
// FIN DEFINITION DE L'AFFICHAGE *************************************************************

//**************************************  FIN   CRON   *******************************************************

?>