<?php
/* From dolibarr api : 
 * struct authentication { 
 * 		string dolibarrkey;
 * 		string sourceapplication;
 * 		string login;
 * 		string password;
 * 		string entity; 
 * }
 * */

class DolibarrAuthentication {
	public $dolibarrkey;
	public $sourceapplication;
	public $login;
	public $password;
	public $entity = "";
}
?>
