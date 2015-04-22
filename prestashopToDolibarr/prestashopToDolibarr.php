<?php
include_once(dirname(__FILE__).'/dolibarr/DolibarrApi.php');

class prestashopToDolibarr extends Module {
    private	$_html = '';
    private $_postErrors = array();
    const INSTALL_SQL_FILE = 'install.sql';
    public function __construct()
    { 
        $this->name = 'prestashopToDolibarr';
        $this->tab = 'migration_tools';
        $this->version = '0.1';
        $this->author = 'Arnaud Aujon Chevallier';
        $this->page = basename(__FILE__, '.php');
        parent::__construct();
        $this->displayName = $this->l('prestashopToDolibarr');
        $this->description = $this->l('Synchronize clients, products, orders and invoices from PrestaShop to Dolibarr. Module is originally based on all4doli module by Presta 2 Doli');
    }

    public function install()
    {
        Configuration::updateValue('validated', '0');
        Configuration::updateValue('client_status', 0);
        Configuration::updateValue('clients_last_synchro', "1970-01-01 00:00:00");
        Configuration::updateValue('products_last_synchro', "1970-01-01 00:00:00");
        Configuration::updateValue('orders_last_synchro', "1970-01-01 00:00:00");
        Configuration::updateValue('invoices_last_synchro', "1970-01-01 00:00:00");
        Configuration::updateValue('delivery_line_label', "delivery");

        if (!parent::install()
            OR !$this->registerHook('footer')
            OR !$this->registerHook('adminOrder')
            OR !$this->registerHook('AdminCustomers')
            OR !$this->registerHook('displayAdminProductsExtra') ) {
        	return false;
		}
            return true;
    }

    public function uninstall()
    {
	    Configuration::updateValue('validated', '0');
	    Dolibarr::reset();
        parent::uninstall();
    }
        
    public function getContent()
    {
		if (Tools::getValue('submit'.$this->name) == "1")
		{
			$dolibarr_server_url = Tools::getValue('dolibarr_server_url');
			$dolibarr_key = Tools::getValue('dolibarr_key');
			$dolibarr_login = Tools::getValue('dolibarr_login');
			$dolibarr_password = Tools::getValue('dolibarr_password');

			$prefix_ref_client = Tools::getValue('prefix_ref_client');
			$client_status = Tools::getValue('client_status');
			$prefix_ref_product = Tools::getValue('prefix_ref_product');                                          
			$product_description = Tools::getValue('product_description');                                          
			$delivery_line_label = Tools::getValue('delivery_line_label');                                          
		   
			Configuration::updateValue('dolibarr_server_url', $dolibarr_server_url);
			Configuration::updateValue('dolibarr_key', $dolibarr_key);
			Configuration::updateValue('dolibarr_login', $dolibarr_login);
			if($dolibarr_password) {
			  // only save if a value was entered
			 Configuration::updateValue('dolibarr_password', $dolibarr_password);
			}

			Configuration::updateValue('prefix_ref_client', $prefix_ref_client);
			Configuration::updateValue('client_status', $client_status);
			Configuration::updateValue('prefix_ref_product', $prefix_ref_product);
			Configuration::updateValue('product_description', $product_description);
			Configuration::updateValue('delivery_line_label', $delivery_line_label);
			//Configuration::updateValue('option_image', $option_image);

			// test dolibarr webservices connexion
			$client = new SoapClient($dolibarr_server_url."/webservices/server_other.php?wsdl");
				
			if (is_null($client))
			{
				Configuration::updateValue('validated', '0');
				$testdoliserveur=$this->l("DOLIBARR : Paramètres incorrectes : vérifez l'adresse du serveur et que les webservices sont bien activés.<br>");
			} else
			{
				$dolibarr = Dolibarr::getInstance();
				$response = $dolibarr->getVersions();
				if ($response["result"]->result_code == 'OK') {
					$testdoliserveur=$this->l("DOLIBARR : Server parameters OK");
					Configuration::updateValue('validated', '1');
					Configuration::updateValue('dolibarr_version', $response["dolibarr"]);
				} else {
					Configuration::updateValue('validated', '0');
					$testdoliserveur=$this->l("DOLIBARR : Wrong server parameters. Check api key, login or password.");
				}
			}

			$validated = Configuration::get('validated');
			
			if($validated != '1')
			{
				$this->_html .= '
				<div class="alert error">
				<img src="../img/admin/warning.gif" alt="'.$this->l('Confirmation').'" />
				'.$this->l('Erreur Paramètres').'
				<fieldset class="width10"><legend><img src="../img/admin/contact.gif" />'.$this->l('Rapport').'</legend>
				<b style="color: #000033;">'.$this->l($testdoliserveur).'</b><br />
				</fieldset>
				</div>';     
			} else
			{
				$this->_html .= '
				<div class="conf confirm">
				<img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />
				'.$this->l('Paramètres Enregistrés').'
				<fieldset class="width10"><legend><img src="../img/admin/contact.gif" />'.$this->l('Rapport').'</legend>
				<b style="color: #000033;">'.$this->l($testdoliserveur).'</b><br />
				</fieldset> 
				</div>';
			 }
		}

		$output = $this->_html;
		$output .= $this->_displayErrors();
		$output .= '<fieldset style="width8">
						<legend>'.$this->l('Informations').'</legend>  
								   <a>'.$this->l('Dolibar version : ').Configuration::get('dolibarr_version').'</a><br /></fieldset><br /> ';
		//if (!Tools::isSubmit('action')) {
			$output .= $this->displayForm();
		//}
		$output .= $this->displayActions();

		return $output;
    }

    private function _displayErrors() {
        $nbErrors = sizeof($this->_postErrors);
        $output = '';
        if ($nbErrors) 
        {
            $output .= '
                <div class="alert error">
                  <h3>'.($nbErrors > 1 ? $this->l('There are') : $this->l('There is')).' '.$nbErrors.' '.($nbErrors > 1 ? $this->l('errors') : $this->l('error')).'</h3>
                  <ol>';
            foreach ($this->_postErrors AS $error)
                $output .= '<li>'.$error.'</li>';
                $output .= '</ol> </div>';
        }
        return $output;
    }

    public function displayForm()
    {
        // Get default language
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        $client_status_options = array(
          array(
            'id_option' => 0,
            'name' => 'Closed'
          ),
          array(
            'id_option' => 1,
            'name' => 'In activity'
          ),
        );
         
        // Init Fields form array
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Dolibarr settings'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Dolibarr server url'),
                    'name' => 'dolibarr_server_url',
                    'size' => 33,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Dolibarr api key'),
                    'name' => 'dolibarr_key',
                    'size' => 33,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Dolibarr login'),
                    'name' => 'dolibarr_login',
                    'size' => 33,
                    'required' => true
                ),
                array(
                    'type' => 'password',
                    'label' => $this->l('Dolibarr password'),
                    'name' => 'dolibarr_password',
                    'size' => 33,
                    'required' => false
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'button'
            )
        );

        $fields_form[1]['form'] = array(
            'legend' => array(
                'title' => $this->l('Client synchronisation settings'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Client code prefix'),
					'desc' => $this->l('If you define one the client code will be {your-prefix}-{prestashopId}. Leave blank if you prefer to let Dolibarr handle code.'),
                    'name' => 'prefix_ref_client',
                    'size' => 33,
                    'required' => false
                ),
                array(
                  'type' => 'select',
                  'label' => $this->l('Dolibarr client status'),
                  'desc' => $this->l('Status of the client in Dolibarr'),
                  'name' => 'client_status',                     // The content of the 'id' attribute of the <select> tag.
                  'required' => true,                              // If set to true, this option must be set.
                  'options' => array(
                   'query' => array(
                                  array(
                                    'id_option' => 0,
                                    'name' => 'Closed'
                                  ),
                                  array(
                                    'id_option' => 1,
                                    'name' => 'In activity'
                                  ),
                                ),                           // $options contains the data itself.
                   'id' => 'id_option',                           // The value of the 'id' key must be the same as the key for 'value' attribute of the <option> tag in each $options sub-array.
                   'name' => 'name'
                  )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Product reference prefix'),
                    'name' => 'prefix_ref_product',
                    'size' => 33,
                    'required' => false
                )
            )
        );
        
        $fields_form[2]['form'] = array(
            'legend' => array(
                'title' => $this->l('Product synchronisation settings'),
            ),
            'input' => array(
                array(
                  'type' => 'select',
                  'label' => $this->l('Dolibarr product description'),
                  'desc' => $this->l('Product description to use in dolibarr'),
                  'name' => 'product_description',
                  'required' => true,
                  'options' => array(
                  'query' => array(
                                  array(
                                    'id_option' => 0,
                                    'name' => 'Short description'
                                  ),
                                  array(
                                    'id_option' => 1,
                                    'name' => 'Full description'
                                  ),
                                ),                           // $options contains the data itself.
                  'id' => 'id_option',
                  'name' => 'name'
                  )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('delivery line label'),
                    'name' => 'delivery_line_label',
                    'required' => false
                )
            )
        );
         
        $helper = new HelperForm();
         
        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
         
        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
         
        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit'.$this->name;
        $helper->toolbar_btn = array(
            'save' =>
            array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                '&token='.Tools::getAdminTokenLite('AdminModules'),
            ),
            'back' => array(
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );
         
        // Load values
        $helper->fields_value['dolibarr_server_url'] = Configuration::get('dolibarr_server_url');
        $helper->fields_value['dolibarr_key'] = Configuration::get('dolibarr_key');
        $helper->fields_value['dolibarr_login'] = Configuration::get('dolibarr_login');
        $helper->fields_value['dolibarr_password'] = Configuration::get('dolibarr_password');

        $helper->fields_value['client_status'] = Configuration::get('client_status');
        $helper->fields_value['prefix_ref_client'] = Configuration::get('prefix_ref_client');
        $helper->fields_value['prefix_ref_product'] = Configuration::get('prefix_ref_product');
        $helper->fields_value['product_description'] = Configuration::get('product_description');
        $helper->fields_value['delivery_line_label'] = Configuration::get('delivery_line_label');
         
        return $helper->generateForm($fields_form);
    }

    function displayActions()
    {
		$nb_clients_to_sync = Db::getInstance()->getValue("select count(*) from "._DB_PREFIX_."customer where date_upd > '".Configuration::get('clients_last_synchro')."'");
		$nb_products_to_sync = Db::getInstance()->getValue("select count(*) from "._DB_PREFIX_."product where date_upd > '".Configuration::get('products_last_synchro')."'");
		$nb_orders_to_sync = Db::getInstance()->getValue("select count(*) from "._DB_PREFIX_."orders where date_upd > '".Configuration::get('orders_last_synchro')."'");

        $output = '<br />
            <fieldset class="width10">
			    <legend><img src="../modules/'.$this->name.'/synchro.png" /> '.$this->l('Synchronization').'</legend>    
				    <fieldset style="width8">
						<legend>'.$this->l('Clients').'</legend>  
								  <a>'.$this->l('Last synchronization : ').Configuration::get('clients_last_synchro').', 
								  '.$this->l('There is ').$nb_clients_to_sync.$this->l(' updated or new clients').'</a><br />																													
								  <img src="../modules/prestashopToDolibarr/yes.gif" />'.$this->l(' > ').'</a><a href="../modules/prestashopToDolibarr/synchroclients.php" target="blank" ><b style="color: #000099;">' .
                                            $this->l('Synchronize updated and new clients').'</b></a><br />
                                  <img src="../modules/prestashopToDolibarr/yes.gif" />'.$this->l(' > ').'</a><a href="../modules/prestashopToDolibarr/synchroclients.php?action=reset" target="blank" ><b style="color: #000099;">' .
                                            $this->l('Synchronize all clients').'</b></a><br />
                            <br />
					</fieldset>
					<fieldset style="width8">
						<legend>'.$this->l('Products').'</legend>  
								   <a>'.$this->l('Last synchronization : ').Configuration::get('products_last_synchro').', 
								   '.$this->l('There is ').$nb_products_to_sync.$this->l(' updated or new products').'</a><br />
                                  <img src="../modules/prestashopToDolibarr/yes.gif" />'.$this->l(' > ').'</a><a href="../modules/prestashopToDolibarr/synchroproducts.php" target="blank" ><b style="color: #000099;">' .
                                            $this->l('Synchronize updated and new products').'</b></a><br />
                                  <img src="../modules/prestashopToDolibarr/yes.gif" />'.$this->l(' > ').'</a><a href="../modules/prestashopToDolibarr/synchroproducts.php?action=reset" target="blank" ><b style="color: #000099;">' .
                                            $this->l('Synchronize all products').'</b></a><br />	
                            <br />		
					</fieldset> 
					<fieldset style="width8">
						<legend>'.$this->l('Orders and invoices').'</legend>  
								   <a>'.$this->l('Last synchronization : ').Configuration::get('orders_last_synchro').', 
								   '.$this->l('There is ').$nb_orders_to_sync.$this->l(' updated or new orders or invoices').'</a><br />
                                  <img src="../modules/prestashopToDolibarr/yes.gif" />'.$this->l(' > ').'</a><a href="../modules/prestashopToDolibarr/synchroorders.php" target="blank" ><b style="color: #000099;">' .
                                            $this->l('Synchronize updated and new orders or invoices').'</b></a><br />
                                  <img src="../modules/prestashopToDolibarr/yes.gif" />'.$this->l(' > ').'</a><a href="../modules/prestashopToDolibarr/synchroorders.php?action=reset" target="blank" ><b style="color: #000099;">' .
                                            $this->l('Synchronize all orders and invoices').'</b></a><br />
                            <br />		
					</fieldset>                 
			</fieldset>';

        return $output;
    }

    function hookAdminCustomers($params)
    {
        $cible_synchro1client='../modules/prestashopToDolibarr/synchro1client.php?id_customer='.$params["id_customer"];
        $display = '
            <br />
            <fieldset class="width10">
                <legend><img src="../modules/prestashopToDolibarr/synchro.png" /> '.$this->l('Dolibarr synchronization').'</legend>    
            <fieldset class="width8"> 
                <img src="../modules/prestashopToDolibarr/yes.gif" /> > <a href='.$cible_synchro1client.' target="blank" ><b style="color: #000099;">Synchroniser le client</b></a><br />
              </fieldset>
            </fieldset>';
        return $display;
    }
    
    function hookAdminOrder($params)
    {                
	    $cible_synchro1order='href="../modules/prestashopToDolibarr/synchro1order.php?id_order='.$params["id_order"].'"';

        $display = '<br />
                <fieldset style="width: 400px">
                    <legend><img src="../modules/prestashopToDolibarr/synchro.png" /> '.$this->l('Dolibarr synchronization').'</legend>    
                <br />
                    <img src="../modules/prestashopToDolibarr/yes.gif" /> > </a><a  '.$cible_synchro1order.' target="blank" ><b style="color: #000099;">Synchroniser la commande</b></a><br />
                </fieldset>
                
                <br />';
        return $display;
    }

    public function hookDisplayAdminProductsExtra($params)
    {
	    $id_product = (int)Tools::getValue('id_product');		
	    $synchro1product='../modules/prestashopToDolibarr/synchro1product.php?id_product='.$id_product;
        $display = '
            <br />
            <fieldset class="width10">
                <legend><img src="../modules/prestashopToDolibarr/synchro.png" /> '.$this->l('Dolibarr synchronization').'</legend>    
            <fieldset class="width8"> 
                <img src="../modules/prestashopToDolibarr/yes.gif" /> > <a href='.$synchro1product.' target="blank" ><b style="color: #000099;">Synchroniser le produit</b></a><br />
              </fieldset>
            </fieldset>';
        return $display;
    }

    public function getHttpHost($http = false, $entities = false)
    {
        $host = (isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST']);
        if ($entities)
            $host = htmlspecialchars($host, ENT_COMPAT, 'UTF-8');
        if ($http)
            $host = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').$host;
        return $host;
    }

    public function displayAjaxSynchronizeClients()
    {
            /*$tmp = Configuration::getInt('tmp_synchroclients_id');
            var_dump($tmp);
            include_once('synchroclients.php');
            if (synchronizeClients($tmp)) {
                die(Tools::jsonEncode("Synchronize of client : ".$tmp));
            } else {
               die(Tools::jsonEncode("Synchronize of ".$tmp."clients done!"));
            }*/
        echo "ok";
        die(Tools::jsonEncode("Synchronization of clients done!"));
    }
}
?>
