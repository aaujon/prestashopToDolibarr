<?php
/* From dolibarr api : 
<xsd:element name="id" type="xsd:string"/>
* <xsd:element name="lastname" type="xsd:string"/>
* <xsd:element name="firstname" type="xsd:string"/>
* <xsd:element name="address" type="xsd:string"/>
* <xsd:element name="zip" type="xsd:string"/>
* <xsd:element name="town" type="xsd:string"/>
* <xsd:element name="state_id" type="xsd:string"/>
* <xsd:element name="state_code" type="xsd:string"/>
* <xsd:element name="state" type="xsd:string"/>
* <xsd:element name="country_id" type="xsd:string"/>
* <xsd:element name="country_code" type="xsd:string"/>
* <xsd:element name="country" type="xsd:string"/>
* <xsd:element name="socid" type="xsd:string"/> // id thirdparty
* <xsd:element name="status" type="xsd:string"/>
* <xsd:element name="phone_pro" type="xsd:string"/>
* <xsd:element name="fax" type="xsd:string"/>
* <xsd:element name="phone_perso" type="xsd:string"/>
* <xsd:element name="phone_mobile" type="xsd:string"/>
* <xsd:element name="code" type="xsd:string"/>
* <xsd:element name="email" type="xsd:string"/>
* <xsd:element name="birthday" type="xsd:string"/>
* <xsd:element name="default_lang" type="xsd:string"/>
* <xsd:element name="note" type="xsd:string"/>
* <xsd:element name="no_email" type="xsd:string"/>
* <xsd:element name="ref_facturation" type="xsd:string"/>
* <xsd:element name="ref_contrat" type="xsd:string"/>
* <xsd:element name="ref_commande" type="xsd:string"/>
* <xsd:element name="ref_propal" type="xsd:string"/>
* <xsd:element name="user_id" type="xsd:string"/>
* <xsd:element name="user_login" type="xsd:string"/><
* xsd:element name="civility_id" type="xsd:string"/>
* <xsd:element name="poste" type="xsd:string"/>
* <xsd:element name="statut" type="xsd:string"/> 

*/

class DolibarrContact {
	public $id;
	public $ref_ext;
	public $lastname;
	public $firstname;
	public $address;
	public $zip;
	public $town ;
	public $state_id;
	public $state_code;
	public $state;
	public $country_id;
	public $country_code;
	public $country;
	public $socid;
    public $status = 1; // actived
    public $statut = 1; // actived
	public $phone_pro;
	public $fax;
	public $phone_perso;
	public $phone_mobile;
	public $code;
	public $email;
	public $birthday;
	public $default_lang;
	public $note;
	public $no_email;
	public $ref_facturation;
	public $ref_contrat;
	public $ref_commande;
	public $ref_propal;
	public $user_id;
	public $user_login;
	public $civility_id;
	public $poste = "Prestashop client";
}

?>
