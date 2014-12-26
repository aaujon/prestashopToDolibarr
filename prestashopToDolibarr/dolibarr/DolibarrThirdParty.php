<?php
/* From dolibarr api : 
	[1]=> string(60) "struct result { string result_code; string result_label; }" 
	[2]=> string(687) "struct thirdparty { string id; string ref; string ref_ext; string fk_user_author; string status; string client; string supplier; string customer_code; string supplier_code; string customer_code_accountancy; string supplier_code_accountancy; dateTime date_creation; dateTime date_modification; string note_private; string note_public; string address; string zip; string town; string province_id; string country_id; string country_code; string country; string phone; string fax; string email; string url; string profid1; string profid2; string profid3; string profid4; string profid5; string profid6; string capital; string vat_used; string vat_number; }" 
	[3]=> string(79) "struct filterthirdparty { string client; string supplier; string category; }" 
	[4]=> string(30) "thirdparty ThirdPartiesArray[]" 
	[5]=> string(53) "struct ThirdPartiesArray2 { thirdparty thirdparty; }" 

*/

class DolibarrThirdParty {
	public $id;
	public $ref; // nom
	public $ref_ext;
	public $fk_user_author;
	public $status = "";
	public $client = 1;
	public $supplier; 
	public $customer_code;
	public $supplier_code;
	public $customer_code_accountancy;
	public $supplier_code_accountancy;
	public $date_creation = ""; // dateTime
	public $date_modification = ""; // dateTime
	public $note_private;
	public $note_public;
	public $address;
	public $zip;
	public $town ;
	public $province_id;
	public $country_id;
	public $country_code;
	public $country;
	public $phone;
	public $fax;
	public $email;
	public $url = "http://www.funkyvinyl.com";
	public $profid1;
	public $profid2;
	public $profid3;
	public $profid4;
	public $profid5;
	public $profid6;
	public $capital;
	public $vat_used = 1;
	public $vat_number;
}

?>
