<?php
/*
Methods: 
llx_array(4) {
 [0]=> string(132) "list(result $result, thirdparty $thirdparty) getThirdParty(authentication $authentication, string $id, string $ref, string $ref_ext)" 
 [1]=> string(118) "list(result $result, string $id, string $ref) createThirdParty(authentication $authentication, thirdparty $thirdparty)" 
 [2]=> string(105) "list(result $result, string $id) updateThirdParty(authentication $authentication, thirdparty $thirdparty)" 
 [3]=> string(144) "list(result $result, ThirdPartiesArray2 $thirdparties) getListOfThirdParties(authentication $authentication, filterthirdparty $filterthirdparty)" 
} 
*/
ini_set('display_errors', 1);
error_reporting(E_ALL);
include('DolibarrThirdParty.php');
include('DolibarrContact.php');
include('DolibarrProduct.php');
include('DolibarrOrder.php');
include('DolibarrInvoice.php');

include('DolibarrAuthentication.php');

class Dolibarr {
	private static $_instance = null;
	
	private $authentication;
	private $dolibarr_server_url;
	private $client_thirdparty;
	private $client_contact;
    private $client_product;
    private $client_order;
    private $client_invoice;
	
	private function initAuthentication() {
		// load credentials
		$this->dolibarr_server_url = Configuration::get('dolibarr_server_url');
		$this->authentication = new DolibarrAuthentication();
		$this->authentication->dolibarrkey = Configuration::get('dolibarr_key');
		$this->authentication->sourceapplication="prestashop module prestashopToDolibarr";
		$this->authentication->login = Configuration::get('dolibarr_login');
		$this->authentication->password = Configuration::get('dolibarr_password');
	}

	private function __construct() {
		$this->initAuthentication();
		// init webservice client
		$this->client_thirdparty = new SoapClient($this->dolibarr_server_url."/webservices/server_thirdparty.php?wsdl");
		$this->client_contact = new SoapClient($this->dolibarr_server_url."/webservices/server_contact.php?wsdl");
		$this->client_product = new SoapClient($this->dolibarr_server_url."/webservices/server_productorservice.php?wsdl");
		$this->client_order = new SoapClient($this->dolibarr_server_url."/webservices/server_order.php?wsdl");
		$this->client_invoice = new SoapClient($this->dolibarr_server_url."/webservices/server_invoice.php?wsdl");
		//var_dump($this->client_product);
		//var_dump($this->client_product->__getFunctions());
	}

	public static function getInstance() {
		if(is_null(self::$_instance)) {
			self::$_instance = new Dolibarr();
		}
		return self::$_instance;
	}

    /********** Methods for users **********/
	public function getUser($ref_ext) {
		// Set parameters for the request
		$params = array(
		  "authentication" => $this->authentication,
          "id" => "",
          "ref" => "",
		  "ref_ext" => $ref_ext
		);

		// Invoke webservice
		$response = $this->client_thirdparty->__soapCall("getThirdParty", $params);

		return $response;
	}

	public function createUser($thirdParty) {
		// Set parameters for the request
		$params = array(
		  "authentication" => $this->authentication,
		  "thirdparty" => $thirdParty
		);

		// Invoke webservice
		$response = $this->client_thirdparty->__soapCall("createThirdParty", $params);

		return $response;
	}

	public function updateUser($thirdParty) {
		// Set parameters for the request
		$params = array(
		  "authentication" => $this->authentication,
		  "thirdparty" => $thirdParty
		);

		// Invoke webservice
		$response = $this->client_thirdparty->__soapCall("updateThirdParty", $params);

		return $response;
	}
	
	public function getUsers() {
		// Set parameters for the request
		$params = array(
		  "authentication" => $this->authentication,
		  "filterthirdparty" => ""
		);

		// Invoke webservice
		$response = $this->client_thirdparty->__soapCall("getListOfThirdParties", $params);

		return $response;
	}
	
	/********** Methods for contacts **********/
	public function getContact($ref_ext) {
		// Set parameters for the request
		$params = array(
		  "authentication" => $this->authentication,
          "id" => "",
          "ref" => "",
		  "ref_ext" => $ref_ext
		);

		// Invoke webservice
		$response = $this->client_contact->__soapCall("getContact", $params);

		return $response;
	}

	public function createContact($thirdParty) {
		// Set parameters for the request
		$params = array(
		  "authentication" => $this->authentication,
		  "contact" => $thirdParty
		);

		// Invoke webservice
		$response = $this->client_contact->__soapCall("createContact", $params);

		return $response;
	}

	public function updateContact($thirdParty) {
		// Set parameters for the request
		$params = array(
		  "authentication" => $this->authentication,
		  "contact" => $thirdParty
		);

		// Invoke webservice
		$response = $this->client_contact->__soapCall("updateContact", $params);

		return $response;
	}
	
	public function getContactsForThirdParty($id_third_party) {
		// Set parameters for the request
		$params = array(
		  "authentication" => $this->authentication,
		  "idthirdparty" => $id_third_party
		);

		// Invoke webservice
		$response = $this->client_contact->__soapCall("getListOfContactsForThirdParty", $params);

		return $response;
	}

    /********** Methods for products **********/

	public function getProduct($ref_ext) {
		// Set parameters for the request
		$params = array(
		  "authentication" => $this->authentication,
          "id" => "",
          "ref" => "",
		  "ref_ext" => $ref_ext
		);

		// Invoke webservice
		$response = $this->client_product->__soapCall("getProductOrService", $params);
		//var_dump($response);

		return $response;
	}

	public function createProduct($product) {
		// Set parameters for the request
		$params = array(
		  "authentication" => $this->authentication,
		  "product" => $product
		);

		// Invoke webservice
		$response = $this->client_product->__soapCall("createProductOrService", $params);
		//var_dump($response);

		return $response;
	}

	public function updateProduct($product) {
		// Set parameters for the request
		$params = array(
		  "authentication" => $this->authentication,
		  "product" => $product
		);

		// Invoke webservice
		$response = $this->client_product->__soapCall("updateProductOrService", $params);
		//var_dump($response);
		return $response;
	}
	
	/********** Methods for orders **********/

	public function getOrder($ref_ext) {
		// Set parameters for the request
		$params = array(
		  "authentication" => $this->authentication,
          "id" => "",
          "ref" => "",
		  "ref_ext" => $ref_ext
		);

		// Invoke webservice
		$response = $this->client_order->__soapCall("getOrder", $params);
		var_dump($response);

		return $response;
	}

	public function createOrder($order) {
		// Set parameters for the request
		$params = array(
		  "authentication" => $this->authentication,
		  "order" => $order
		);
		var_dump($params);
		// Invoke webservice
		$response = $this->client_order->__soapCall("createOrder", $params);
		var_dump($response);

		return $response;
	}

	public function updateOrder($order) {
		// Set parameters for the request
		$params = array(
		  "authentication" => $this->authentication,
		  "order" => $order
		);

		// Invoke webservice
		$response = $this->client_order->__soapCall("updateOrder", $params);
		var_dump($response);
		return $response;
	}
	
	/********** Methods for invoices **********/

	public function getInvoice($ref_ext) {
		// Set parameters for the request
		$params = array(
		  "authentication" => $this->authentication,
          "id" => "",
          "ref" => "",
		  "ref_ext" => $ref_ext
		);

		// Invoke webservice
		$response = $this->client_order->__soapCall("getInvoice", $params);
		var_dump($response);

		return $response;
	}

	public function createInvoice($invoice) {
		// Set parameters for the request
		$params = array(
		  "authentication" => $this->authentication,
		  "invoice" => $invoice
		);

		var_dump($params);
		// Invoke webservice
		$response = $this->client_order->__soapCall("createInvoice", $params);
		var_dump($response);

		return $response;
	}

	public function updateInvoice($order) {
		// Set parameters for the request
		$params = array(
		  "authentication" => $this->authentication,
		  "invoice" => $invoice
		);

		// Invoke webservice
		$response = $this->client_order->__soapCall("updateInvoice", $params);
		var_dump($response);
		return $response;
	}
}

?>
