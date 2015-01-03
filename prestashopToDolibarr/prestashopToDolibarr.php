<?php
class prestashopToDolibarr extends Module {
        private	$_html = '';
        private $_postErrors = array();
        const INSTALL_SQL_FILE = 'install.sql';
        public function __construct() { 
            $this->name = 'prestashopToDolibarr';
            $this->tab = 'migration_tools';
            $this->version = ' 21_11_2013 > P1.5.4 (+1.4.11) / D3.4.0';
            $this->author = 'Arnaud Aujon Chevallier';
            $this->page = basename(__FILE__, '.php');
            parent::__construct();
            $this->displayName = $this->l('prestashopToDolibarr');
            $this->description = $this->l('Synchronisation des Commandes, Produits, Clients et Stocks de PrestaShop vers Dolibarr. Module basé sur le module all4doli par Presta 2 Doli');
        }

        public function install() {
			Configuration::updateValue('dolibarr_server_url', 'localhost');
            Configuration::updateValue('libelle_port', 'port expedition');
            Configuration::updateValue('code_article_port', '1234567890');
            Configuration::updateValue('prefix_ref_client', 'Client prestashop N-');
            Configuration::updateValue('validated', '0');
            Configuration::updateValue('memo_id', '0');

            if (!parent::install()
                OR !$this->registerHook('footer')
                OR !$this->registerHook('adminOrder')
                OR !$this->registerHook('AdminCustomers') ) {
				return false;
			}
            return true;
        }

        public function uninstall() {
			Configuration::updateValue('validated', '0');

            parent::uninstall();
        }
        
        public function getContent() {
			include_once(dirname(__FILE__).'/dolibarr/DolibarrApi.php');
			
            $output = '<h2>'.$this->displayName.'</h2>';

            if (isset($_POST['submitdonnees'])) {
                $dolibarr_server_url=$_POST['dolibarr_server_url'];
                $dolibarr_key=$_POST['dolibarr_key'];
                $dolibarr_login=$_POST['admindoli'];
                $dolibarr_password=$_POST['mdpdoli'];
                $libelle_port=$_POST['libelleport'];
                $code_article_port=$_POST['codearticleport'];        
                $prefix_ref_client=$_POST['prefix_ref_client'];       
                $option_image=$_POST['option_image'];
                $decremente=$_POST['decremente'];
                $stock_doli=$_POST['stock_doli'];                                    
                $memo_parametres=$_POST['memo_parametres'];
                echo "$dolibarr_server_url"." "."$dolibarr_key"." "."$dolibarr_login"." "."$dolibarr_password"." "."$base_doli"." "."$prefix_doli";
               
               Configuration::updateValue('dolibarr_server_url', $dolibarr_server_url);
               Configuration::updateValue('dolibarr_key', $dolibarr_key);
               Configuration::updateValue('dolibarr_login', $dolibarr_login);
               Configuration::updateValue('dolibarr_password', $dolibarr_password);
               Configuration::updateValue('libelle_port', $libelle_port);
               Configuration::updateValue('code_article_port', $code_article_port);
               Configuration::updateValue('prefix_ref_client', $prefix_ref_client);
               Configuration::updateValue('option_image', $option_image);
               Configuration::updateValue('decremente', $decremente);
               Configuration::updateValue('stock_doli', $stock_doli);
               Configuration::updateValue('memo_parametres', $memo_parametres);

				// test dolibarr webservices connexion
				$client = new SoapClient($dolibarr_server_url."/webservices/server_thirdparty.php?wsdl");
					
				if (is_null($client)){
					$testdoliserveur="DOLIBARR : Paramètres incorrectes : vérifez l'adresse du serveur et que les webservices sont bien activés.<br>Arret du TEST";
				} else {
					echo "Serveur webservice is enabled<br>";
					$dolibarr = Dolibarr::getInstance();

					$response = $dolibarr->getUsers();
					
					if (is_null($response)) {
						$testdoliserveur="DOLIBARR : url serveur correctes. Vérifez la clé api, le login et le password.<br>Arret du TEST";
					} else {
						$testdoliserveur="DOLIBARR : Parametres serveur OK 
								<br>--> Les parametres du serveur sont actuellement verrouilles !
								<br>--> Si vous souhaitez pouvoir modifier ces parametres : desinstallez puis re-installez le module.<br>
								<br><br>Fin du test & Parametres enregistres";
						Configuration::updateValue('validated', '1');

					}
				}

                $validated = Configuration::get('validated');
                
                if($validated!='1') {
                    $this->_html .= '
                    <div class="alert error">
                    <img src="../img/admin/warning.gif" alt="'.$this->l('Confirmation').'" />
                    '.$this->l('Erreur Paramètres').'
                    <fieldset class="width10"><legend><img src="../img/admin/contact.gif" />'.$this->l('Rapport').'</legend>
                    <b style="color: #000033;">'.$this->l($testdoliserveur).'</b><br />
                    </fieldset>
                    </div>';     
                } else {
                    $this->_html .= '
                    <div class="conf confirm">
                    <img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />
                    '.$this->l('Paramètres Enregistrés').'
                    <fieldset class="width10"><legend><img src="../img/admin/contact.gif" />'.$this->l('Rapport').'</legend>
                    <b style="color: #000033;">'.$this->l($testdoliserveur).'</b><br />
                    </fieldset> 
                    </div>';
                 }
            } elseif (isset($_POST['synchro1client'])) {
                $id_customer=$_POST['id_customer'];
                include_once('synchro1client.php');
                synchronizeClient($id_customer);
			}

            if (!empty($_POST))
                $output .= $this->_html;  
                $output .= $this->_displayErrors();
                $output .= $this->_displayForm();
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
                    $output .= '
                    </ol>
                    </div>';
                }
            return $output;
        }

        private function _displayForm() {
        	global $cookie;
			
			$test_synchroorder="style='display:none;'";
			$test_texte_synchroorder=" Synchroniser les COMMANDES clients";
			$cible_synchroorder='href="../modules/prestashopToDolibarr/synchroorder.php"';
			
			$test_synchroclients="style='display:none;'";
			$test_texte_synchroclients=" Synchroniser les CLIENTS";
			$cible_synchroclients='href="../modules/prestashopToDolibarr/synchroclients.php"';
			
			$test_synchrocateg="style='display:none;'";
			$test_texte_synchrocateg=" Synchroniser les CATEGORIES";
			$cible_synchrocateg='<a href="../modules/prestashopToDolibarr/synchrocateg.php" target="blank" >';
		   
			$test_synchroprod="style='display:none;'";
			$test_texte_synchroprod=" Synchroniser les PRODUITS";
			$cible_synchroprod='href="../modules/prestashopToDolibarr/synchroprod.php"';

			$test_synchrostock2presta="style='display:none;'";
			$test_texte_synchrostock2presta=" Synchroniser les STOCKS";
			$cible_synchrostock2presta='href="../modules/prestashopToDolibarr/synchrostock2presta.php"';

			$memo_parametres = Configuration::get('memo_parametres');
			$option_image = Configuration::get('option_image');
			$decremente = Configuration::get('decremente');
			$stock_doli = Configuration::get('stock_doli');
			$output = '';	
			
			$output .= '
				  <form action="'.htmlentities($_SERVER['REQUEST_URI']).'" method="post">
					<fieldset class="width10"><legend><img src="../img/admin/unknown.gif" alt="" title="" />'.$this->l('PRESENTATION').'</legend>
							<b style="color: #000033;">'.$this->l('Ce module vous permet de synchroniser contacts, produits, commandes et factures vers Dolibarr :</b>').'</b><br />
							<style="color: #000033;">'.$this->l('A partir d\'une commande ou d\'une fiche client').'</b><br />
							<b style="color: #000033;">'.$this->l('</b>').'</b>
							<b style="color: #000033;">'.$this->l('ou</b>').'</b><br />
							<b style="color: #000033;">'.$this->l('</b>').'</b>
							<b style="color: #000033;">'.$this->l('De tout synchroniser en une seule fois. </b>').'
					</fieldset>
					
					<br /><br /> 

					<fieldset class="width10"><legend><img src="../img/admin/contact.gif" />'.$this->l('Paramètres de la base Dolibarr').'</legend>
					  <label>'.$this->l('Url serveur dolibarr').'</label>
							<div class="margin-form"><input type="text" size="33" name="dolibarr_server_url" value="'.htmlentities(Configuration::get('dolibarr_server_url'), ENT_COMPAT, 'UTF-8').'" /><i>'.$this->l(' -> URL du serveur dolibarr : par exemple => https://myserver/dolibarr/htdocs').'</i></div>
							<label>'.$this->l('clé API dolibarr').'</label>
							<div class="margin-form"><input type="text" size="33" name="dolibarr_key" value="'.htmlentities(Configuration::get('dolibarr_key'), ENT_COMPAT, 'UTF-8').'" /><i>'.$this->l(' -> Clé de l\'api du serveur Dolibarr').'</i></div>
							<label>'.$this->l('utilisateur dolibarr').'</label>
							<div class="margin-form"><input type="text" size="33" name="admindoli" value="'.htmlentities(Configuration::get('dolibarr_login'), ENT_COMPAT, 'UTF-8').'" /><i>'.$this->l(' -> Le nom d\'utilisateur de Dolibarr => admin').'</i></div>
							<label>'.$this->l('password dolibarr').'</label>
							<div class="margin-form"><input type="password" size="33" name="mdpdoli" value="'.htmlentities(Configuration::get('dolibarr_password'), ENT_COMPAT, 'UTF-8').'" /><i>'.$this->l(' -> Le mot de passe de cet utilisateur Dolibarr => ******').'</i></div>
					</fieldset>
					
					<br /><br />
					
					<fieldset class="width10"><legend><img src="../img/admin/contact.gif" />'.$this->l('Options du Module').'</legend>
						<label>'.$this->l('Conserver les Parametres').'</label>
						<div class="margin-form"><input type="checkbox" '.$memo_parametres.' name="memo_parametres" value="checked" /><i>'.$this->l(' -> Pour conserver les paramètres après une désinstallation (sinon, tout est remis à zero)').'</i></div>
						<label>'.$this->l('Affichage').'</label>
					</fieldset>
					
					<fieldset class="width10"><legend><img src="../img/admin/contact.gif" />'.$this->l('Donnees pour Dolibarr').'</legend>
							<label>'.$this->l('Libelle du Port').'</label>
							<div class="margin-form"><input type="text" size="33" name="libelleport" value="'.htmlentities(Configuration::get('libelle_port'), ENT_COMPAT, 'UTF-8').'" /><i>'.$this->l(' -> Libelle de la ligne de port => exemple : PORT').'</i></div>
							<label>'.$this->l('Code article du port').'</label>
							<div class="margin-form"><input type="text" size="33" name="codearticleport" value="'.htmlentities(Configuration::get('code_article_port'), ENT_COMPAT, 'UTF-8').'" /><i>'.$this->l(' -> Code article du port = ID - maxi 10 chiffres => exemple : 1234567890').'</i></div>
							<label>'.$this->l('Prefixe ref Cde client').'</label>
							<div class="margin-form"><input type="text" size="33" name="prefix_ref_client" value="'.htmlentities(Configuration::get('prefix_ref_client'), ENT_COMPAT, 'UTF-8').'" /><i>'.$this->l(' -> Préfixe réf commande client => exemple : Boutique CDE N').'</i></div>
					</fieldset>
					<fieldset class="width10"><legend><img src="../img/admin/contact.gif" />'.$this->l('Donnees pour les Produits').'</legend>
					  <label>'.$this->l('Option IMAGE').'</label>
						<div class="margin-form"><input type="checkbox" '.$option_image.' name="option_image" value="checked" /><i>'.$this->l(' -> Option pour intégrer une image à la description du produit').'</i></div>
					  <label>'.$this->l('Decremente stock Doli').'</label>
						<div class="margin-form"><input type="checkbox" '.$decremente.' name="decremente" value="checked" /><i>'.$this->l(' -> Si vous souhaitez décrementer les stock Dolibarr à chaque vente').'</i></div>
					  <label>'.$this->l('Stocks de Presta VERS Doli').'</label>
						<div class="margin-form"><input type="checkbox" '.$stock_doli.' name="stock_doli" value="checked" /><i>'.$this->l(' -> Par defaut, la Synchro des Stocks se fait de Dolibarr VERS PrestaShop<br />Cochez cette option si vous voulez faire l\'inverse (de PrestaShop VERS Dolibarr)').'</i></div>
					</fieldset>
					
					<br />
					
					<fieldset class="width10">      
						<center><input type="submit" name="submitdonnees" value="'.$this->l('Enregistrer').'" class="button" /></center>                                                                                                                                                                                                                                                                                                                                                                                                            
					</fieldset>
					
					<br />
					
					<fieldset class="width10">
					  <legend><img src="../modules/'.$this->name.'/synchro.png" /> '.$this->l('All 4 Dolibarr').'</legend>    
						<fieldset style="width8">                                                                                                                                           
								<legend><img src="../modules/'.$this->name.'/synchro.png" /> '.$this->l('Synchronisation manuelle des : ').'</legend>
							  <img src="../modules/prestashopToDolibarr/yes.gif" />'.$this->l(' > ').'</a><a '.$cible_synchroorder.'  target="blank" ><b  style="color: #000099;">' .$this->l(' '.$test_texte_synchroorder.' ').'</b></a><br />
								  <img src="../modules/prestashopToDolibarr/yes.gif" />'.$this->l(' > ').'</a><a '.$cible_synchroclients.' target="blank" ><b style="color: #000099;">' .$this->l(' '.$test_texte_synchroclients.'').'</b></a><br />		
								  <img src="../modules/prestashopToDolibarr/yes.gif" />'.$this->l(' > ').'</a>'.$cible_synchrocateg.'<b style="color: #000099;">' .$this->l(' '.$test_texte_synchrocateg.'').'</b></a><br />
								  <img src="../modules/prestashopToDolibarr/yes.gif" />'.$this->l(' > ').'</a><a '.$cible_synchroprod.' target="blank" ><b style="color: #000099;">' .$this->l(' '.$test_texte_synchroprod.'').'</b></a><br />
								  <img src="../modules/prestashopToDolibarr/yes.gif" />'.$this->l(' > ').'</a><a '.$cible_synchrostock2presta.' target="blank" ><b style="color: #000099;">' .$this->l(' '.$test_texte_synchrostock2presta.'').'</b></a><br />
						  <br />		
						</fieldset>                  
					</fieldset>
					
					<br />
						</form>
						<br /><br />';
			return $output;
        }

        function hookAdminCustomers($params) {
			$test_synchro1client="style='display:none;'";
			$test_texte_synchro1client=" Synchroniser la fiche client";
			$cible_synchro1client='../modules/prestashopToDolibarr/synchro1client.php?id_customer='.$params["id_customer"];
            $display = '
            <br />
            <fieldset class="width10">
                <legend><img src="../modules/prestashopToDolibarr/synchro.png" /> '.$this->l('Synchronisation Dolibarr').'</legend>    
            <fieldset class="width8"> 
                <img src="../modules/prestashopToDolibarr/yes.gif" />'.$this->l(' > ').'<a href='.$cible_synchro1client.' target="blank" ><b style="color: #000099;">Synchroniser le client</b></a><br />
              </fieldset>
            </fieldset>';
            return $display;
        }
    
        function hookAdminOrder($params) {                
			$test_synchro1order="style='display:none;'";
			$test_texte_synchro1order=" Synchroniser CETTE COMMANDE UNIQUEMENT";
			$cible_synchro1order='href="../modules/prestashopToDolibarr/synchro1order.php?id_order='.$params["id_order"].'"';

            $display = '<br />
                <fieldset style="width: 400px">
                    <legend><img src="../modules/prestashopToDolibarr/synchro.png" /> '.$this->l('Synchronisation Dolibarr').'</legend>    
                <br />
                    <img src="../modules/prestashopToDolibarr/yes.gif" />'.$this->l(' > ').'</a><a  '.$cible_synchro1order.' target="blank" ><b style="color: #000099;">Synchroniser la commande</b></a><br />
                </fieldset>
                
                <br />';
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
}
?>
