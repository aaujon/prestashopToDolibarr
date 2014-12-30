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

            /*require_once(dirname(__FILE__).'/CronParser.php');
            if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
                return (false);*/
            /*else if (!$sql = file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
                return (false);
            $sql = str_replace('PREFIX_', _DB_PREFIX_, $sql);
            $sql = preg_split("/;\s*[\r\n]+/", $sql);
            foreach ($sql AS $query)
            if($query)
            if(!Db::getInstance()->Execute(trim($query)))
            return false;*/
            if (!parent::install()
                OR !$this->registerHook('footer')
                OR !$this->registerHook('adminOrder')
                OR !$this->registerHook('AdminCustomers')
                OR !Configuration::updateValue('cron_method', 'traffic')) {
				return false;
			}
            return true;
        }

        public function uninstall() {
			Configuration::updateValue('validated', '0');

            if ($memo_parametres!='checked') {
               Db::getInstance()->execute("DROP TABLE "._DB_PREFIX_."P2D_param");
               Db::getInstance()->execute("DROP TABLE "._DB_PREFIX_."P2D_cde_four");
            }
            Db::getInstance()->execute("DROP TABLE "._DB_PREFIX_."P2D_combinaisons");
            return (Configuration::deleteByName('cron_lasttime') AND
                    Configuration::deleteByName('cron_method') AND
                    parent::uninstall()
                    );
        }
        
        public function getContent() {
			include_once(dirname(__FILE__).'/dolibarr/DolibarrApi.php');
			
            $output = '<h2>'.$this->displayName.'</h2>';
            if ($id_cron = Tools::getValue('delete')) {
                if ($this->deleteCronByID($id_cron))
                Tools::redirectAdmin(str_replace(array('&delete='.$id_cron, '&conf=1'), '', $_SERVER['REQUEST_URI']).'&conf=1');
            }

            if ($id_cron_url = Tools::getValue('delete_url')) {
                if ($this->deleteCronURLByID($id_cron_url))
                Tools::redirectAdmin(str_replace(array('&delete_url='.$id_cron_url, '&conf=1'), '', $_SERVER['REQUEST_URI']).'&conf=1');
            }

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
                $output .= $this->_postProcess();
                $output .= $this->_displayErrors();
                $output .= $this->_displayForm();
                $output .= $this->_displayList();
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

			$visible_invisible="";	$invisible="style='display:none;'";
			
			$test_synchroorder="style='display:none;'";
			$test_texte_synchroorder=" Synchroniser les COMMANDES clients";
			$cible_synchroorder='href="../modules/prestashopToDolibarr/synchroorder.php"';
			$test_texte_synchroorder_cron=' > URL de la tache CRON pour les COMMANDES (faites un copier/coller) : <br>';
			$url_synchroorder_cron='http://'.$this->getHttpHost(false, true).$this->_path.'synchroordercron.php';
			
			$test_synchroclients="style='display:none;'";
			$test_texte_synchroclients=" Synchroniser les CLIENTS";
			$cible_synchroclients='href="../modules/prestashopToDolibarr/synchroclients.php"';
			$test_texte_synchroclients_cron=' > URL de la tache CRON pour les CLIENTS (faites un copier/coller) : <br>';
			$url_synchroclients_cron='http://'.$this->getHttpHost(false, true).$this->_path.'synchroclientscron.php';
			
			$test_synchrocateg="style='display:none;'";
			$test_texte_synchrocateg=" Synchroniser les CATEGORIES";
			$cible_synchrocateg='<a href="../modules/prestashopToDolibarr/synchrocateg.php" target="blank" >';
			$test_texte_synchrocateg_cron=' > URL de la tache CRON pour les CATEGORIES (faites un copier/coller) : <br>';
			$url_synchrocateg_cron='http://'.$this->getHttpHost(false, true).$this->_path.'synchrocategcron.php';
		   
			$test_synchroprod="style='display:none;'";
			$test_texte_synchroprod=" Synchroniser les PRODUITS";
			$cible_synchroprod='href="../modules/prestashopToDolibarr/synchroprod.php"';
			$test_texte_synchroprod_cron=' > URL de la tache CRON pour les PRODUITS (faites un copier/coller) : <br>';
			$url_synchroprod_cron='http://'.$this->getHttpHost(false, true).$this->_path.'synchroprodcron.php';
			$test_synchrostock2presta="style='display:none;'";
			$test_texte_synchrostock2presta=" Synchroniser les STOCKS";
			$cible_synchrostock2presta='href="../modules/prestashopToDolibarr/synchrostock2presta.php"';
			$test_texte_synchrostock2presta_cron=' > URL de la tache CRON pour les STOCKS (faites un copier/coller) : <br>';
			$url_synchrostock2presta_cron='http://'.$this->getHttpHost(false, true).$this->_path.'synchrostock2prestacron.php';

			$memo_parametres = Configuration::get('memo_parametres');
			$option_image = Configuration::get('option_image');
			$decremente = Configuration::get('decremente');
			$stock_doli = Configuration::get('stock_doli');
			$output = '';
			$cron_test = $this->cronExists($this->id, 'test');
			
			if ($cron_test) {
				if ($cron_lasttest = Configuration::get('cron_lasttest'))
							$output .= '
								<div class="conf confirm" style="width:898px">
									<img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />
									'.$this->l('Last test have been successfully executed on').' '.Tools::DisplayDate(date('Y-m-d H:i:s', $cron_lasttest), $cookie->id_lang, true).' 
								</div>';
						else
							$output .= '
								<div class="alert">
									'.$this->l('Test have not been executed yet.').' 
								</div>';
			}
			$cron_method = Configuration::get('cron_method');
			$output .= '
				  <form action="'.htmlentities($_SERVER['REQUEST_URI']).'" method="post">
					<fieldset class="width10"><legend><img src="../img/admin/unknown.gif" alt="" title="" />'.$this->l('PRESENTATION').'</legend>
							<b style="color: #000033;">'.$this->l('Ce module vous permet de synchroniser divers éléments vers Dolibarr :</b>').'</b><br />
							<b style="color: #000033;">'.$this->l('</b>').'</b><br />
							<b style="color: #000033;">'.$this->l('- Manuellement : </b>').'
							<style="color: #000033;">'.$this->l('A partir d une commande ou d une fiche client').'</b><br />
							<b style="color: #000033;">'.$this->l('</b>').'</b>
							<b style="color: #000033;">'.$this->l('ou</b>').'</b><br />
							<b style="color: #000033;">'.$this->l('</b>').'</b>
							<b style="color: #000033;">'.$this->l('- Automatiquement a chaque visite sur votre site : </b>').'
							<style="color: #000033;">'.$this->l('Avec une tache CRON a rajouter plus bas dans ce module').'</b>
					</fieldset>
					
					<br /><br /> 

					<fieldset class="width10"><legend><img src="../img/admin/contact.gif" />'.$this->l('Paramètres de la base Dolibarr').'</legend>
					  <label>'.$this->l('Url serveur dolibarr').'</label>
							<div class="margin-form"><input type="text" size="33" name="dolibarr_server_url" value="'.htmlentities(Configuration::get('dolibarr_server_url'), ENT_COMPAT, 'UTF-8').'" /><i>'.$this->l(' -> URL du serveur dolibarr : Generalement => localhost').'</i></div>
							<label>'.$this->l('clé API dolibarr').'</label>
							<div class="margin-form"><input type="text" size="33" name="dolibarr_key" value="'.htmlentities(Configuration::get('dolibarr_key'), ENT_COMPAT, 'UTF-8').'" /><i>'.$this->l(' -> Clé de l\'api du serveur Dolibarr').'</i></div>
							<label>'.$this->l('utilisateur dolibarr').'</label>
							<div class="margin-form"><input type="text" size="33" name="admindoli" value="'.htmlentities(Configuration::get('dolibarr_login'), ENT_COMPAT, 'UTF-8').'" /><i>'.$this->l(' -> Le nom de l administrateur de la base de donnees DOLIBARR => root').'</i></div>
							<label>'.$this->l('password dolibarr').'</label>
							<div class="margin-form"><input type="password" size="33" name="mdpdoli" value="'.htmlentities(Configuration::get('dolibarr_password'), ENT_COMPAT, 'UTF-8').'" /><i>'.$this->l(' -> Le mot de passe d acces a la base de donnees DOLIBARR => ******').'</i></div>
					</fieldset>
					
					<br /><br />
					
					<fieldset '.$visible_invisible.' '.$param_parametres_visible.' class="width10"><legend><img src="../img/admin/contact.gif" />'.$this->l('Options du Module').'</legend>
						<label>'.$this->l('Conserver les Parametres').'</label>
						<div class="margin-form"><input type="checkbox" '.$memo_parametres.' name="memo_parametres" value="checked" /><i>'.$this->l(' -> Pour conserver les paramètres après une desinstallation (sinon, tout est remis a zero)').'</i></div>
						<label>'.$this->l('Affichage').'</label>
					</fieldset>
					
					<fieldset '.$visible_invisible.' '.$param_parametres_visible.' class="width10"><legend><img src="../img/admin/contact.gif" />'.$this->l('Donnees pour Dolibarr').'</legend>
							<label>'.$this->l('Libelle du Port').'</label>
							<div class="margin-form"><input type="text" size="33" name="libelleport" value="'.htmlentities($donnees['libelle_port'], ENT_COMPAT, 'UTF-8').'" /><i>'.$this->l(' -> Libelle de la ligne de port => exemple : PORT').'</i></div>
							<label>'.$this->l('Code article du port').'</label>
							<div class="margin-form"><input type="text" size="33" name="codearticleport" value="'.htmlentities(Configuration::get('code_article_port'), ENT_COMPAT, 'UTF-8').'" /><i>'.$this->l(' -> Code article du port = ID - maxi 10 chiffres => exemple : 1234567890').'</i></div>
							<label>'.$this->l('Prefixe ref Cde client').'</label>
							<div class="margin-form"><input type="text" size="33" name="prefix_ref_client" value="'.htmlentities(Configuration::get('prefix_ref_client'), ENT_COMPAT, 'UTF-8').'" /><i>'.$this->l(' -> Préfixe réf commande client => exemple : Boutique CDE N').'</i></div>
					</fieldset>
					<fieldset '.$visible_invisible.' '.$param_parametres_visible.' class="width10"><legend><img src="../img/admin/contact.gif" />'.$this->l('Donnees pour les Produits').'</legend>
					  <label>'.$this->l('Option IMAGE').'</label>
						<div class="margin-form"><input type="checkbox" '.$option_image.' name="option_image" value="checked" /><i>'.$this->l(' -> Option pour intégrer une image à la description du produit').'</i></div>
					  <label>'.$this->l('Decremente stock Doli').'</label>
						<div class="margin-form"><input type="checkbox" '.$decremente.' name="decremente" value="checked" /><i>'.$this->l(' -> Si vous souhaitez décrementer les stock Dolibarr à chaque vente').'</i></div>
					  <label>'.$this->l('Stocks de Presta VERS Doli').'</label>
						<div class="margin-form"><input type="checkbox" '.$stock_doli.' name="stock_doli" value="checked" /><i>'.$this->l(' -> Par defaut, la Synchro des Stocks se fait de Dolibarr VERS PrestaShop<br />Cochez cette option si vous voulez faire l\'inverse (de PrestaShop VERS Dolibarr)').'</i></div>
					</fieldset>
					
					<br />
					
					<fieldset '.$param_parametres_visible.' class="width10">      
						<center><input type="submit" name="submitdonnees" value="'.$this->l('Enregistrer').'" class="button" /></center>                                                                                                                                                                                                                                                                                                                                                                                                            
					</fieldset>
					
					<br />
					
					<fieldset '.$param_parametres_visible.' class="width10">
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
					
				   '/* <fieldset '.$invisible.' class="width10">
								<legend>'.$this->l('Paramètres de la tache CRON').'</legend>
								<p>
								'.$this->l('Please choose the method used to determine when executing jobs.').'
								</p>
								<label for="cron_method">'.$this->l('Method').'</label>
								<div class="margin-form">
									<select name="cron_method" id="cron_method">
										<option value="traffic" '.($cron_method == 'traffic'?'selected="selected"':'').'>'.$this->l('Shop traffic').'</option>
										<option value="crontab" '.($cron_method == 'crontab'?'selected="selected"':'').'>'.$this->l('Server crontab').'</option>
										<option value="webcron" '.($cron_method == 'webcron'?'selected="selected"':'').'>'.$this->l('Webcron service').'</option>
									</select>
								</div>
								<hr/>
								<p>
								'.$this->l('"Shop traffic" method doesn\'t need configuration but is not sure. It depends of your website frequentation so when it isn\'t visited, jobs are not executed.').'
								</p>
								<hr/>
								<p>
								'.$this->l('"Server crontab" is the best method but only if your server uses Linux and you have access to crontab. In that case add the line below to your crontab file.').'
								</p>
								<code>* * * * * php -f '.dirname(__FILE__).DIRECTORY_SEPARATOR.'cron_crontab.php</code>
								<hr/>
								<p>
								'.$this->l('"Webcron service" is a good alternative to crontab but is often not free. Register to a service like').' <a href="http://www.webcron.org">webcron.org</a> 
								'.$this->l('and configure it to visit the URL below every minutes or the nearest.').'
								</p>
								<code>http://'.$this->getHttpHost(false, true).$this->_path.'cron_webcron.php</code>
			
								<hr/>
								<p>
								'.$this->l('To check whether the choosen method works, you can enable the test job. It should be executed every minutes and show it at the top of this form. When everything is ok you can disable it.').'
								</p>
								<label for="cron_test">'.$this->l('Test job').'</label>
								<div class="margin-form">
									<select name="cron_test" id="cron_test">
										<option value="1" '.($cron_test?'selected="selected"':'').'>'.$this->l('Enable').'</option>
										<option value="0" '.(!$cron_test?'selected="selected"':'').'>'.$this->l('Disable').'</option>
									</select>
								</div>
							<p><center><input type="submit" class="button" name="submitAddThis" value="'.$this->l('Save').'" /></center></p>
							</fieldset>
				  </form>
					  <fieldset '.$param_parametres_visible.' class="width10"><legend><img src="../img/admin/unknown.gif" alt="" title="" />'.$this->l('Taches CRON').'</legend>
						<img '.$test_image_synchroorder.' />'.$this->l(' '.$test_texte_synchroorder_cron.'').'</a><a href="'.$url_synchroorder_cron.'"  target="blank" ><b  style="color: #000099;">' .$this->l(' '.$url_synchroorder_cron.' ').'</b></a><br />
						<br>
						<img '.$test_image_synchroclients.' />'.$this->l(' '.$test_texte_synchroclients_cron.'').'</a><a href="'.$url_synchroclients_cron.'"  target="blank" ><b  style="color: #000099;">' .$this->l(' '.$url_synchroclients_cron.' ').'</b></a><br />
						<br>
						<img '.$test_image_synchrocateg.' />'.$this->l(' '.$test_texte_synchrocateg_cron.'').'</a><a href="'.$url_synchrocateg_cron.'"  target="blank" ><b  style="color: #000099;">' .$this->l(' '.$url_synchrocateg_cron.' ').'</b></a><br />
						<br>
						<img '.$test_image_synchroprod.' />'.$this->l(' '.$test_texte_synchroprod_cron.'').'</a><a href="'.$url_synchroprod_cron.'"  target="blank" ><b  style="color: #000099;">' .$this->l(' '.$url_synchroprod_cron.' ').'</b></a><br />
						<br>
						<img src="../modules/prestashopToDolibarr/yes.gif />'.$this->l(' '.$test_texte_synchrostock2presta_cron.'').'</a><a href="'.$url_synchrostock2presta_cron.'"  target="blank" ><b  style="color: #000099;">' .$this->l(' '.$url_synchrostock2presta_cron.' ').'</b></a><br />
						<br>
					  <br>
							<style="color: #000033;">'.$this->l('(INFO : si vous faites un <strong> TEST </strong> pour les taches CRON, un resultat conforme donne une \'PAGE BLANCHE\' (sans message d\'erreur))').'</b>
					  <br>
					  <style="color: #000033;">'.$this->l('(INFO : Utilisez aussi ce module pour vos autres taches CRON)').'</b>
					  <br />
					</fieldset> 
						<form action="'.htmlentities($_SERVER['REQUEST_URI']).'" method="post" id="cron_add">
							<fieldset '.$param_parametres_visible.' class="space width10">
								<legend>'.$this->l('Ajouter une tache CRON').'</legend>
								<p>
								'.$this->l('Cette URL sera realisee a chaque visite sur votre site suivant la frequence que vous indiquerez.').'
								</p>
								<label for="cron_method">'.$this->l('URL').'</label>
								<div class="margin-form">
									<input type="text" name="cron_url" id="cron_url" value="'.(isset($_POST['cron_url'])?$_POST['cron_url']:'').'" />
								</div>
								<label for="cron_method">'.$this->l('Programmation').'</label>
								<div class="margin-form">
									<input type="text" name="cron_mhdmd" id="cron_mhdmd" value="'.(isset($_POST['cron_mhdmd'])?$_POST['cron_mhdmd']:'').'" />
									<select style="display: none;" name="cron_mhdmd2" id="cron_mhdmd2">
										<option value="0 * * * *">'.$this->l('Chaque heure').'</option>
										<option value="0 0 * * *" selected="selected">'.$this->l('Chaque jour (minuit)').'</option>
										<option value="0 0 * * 0">'.$this->l('Chaque semaine (Samedi)').'</option>
										<option value="0 0 1 * *">'.$this->l('Chaque Mois (le premier)').'</option>
										<option value="0 0 1 1 *" >'.$this->l('Chaque annee (1er Janvier)').'</option>
										<option value="other" >'.$this->l('Autre (a preciser)').'</option>
									</select>
								</div>
								<script type="text/javascript"><!--//
									$(document).ready(function() {
										$("#cron_mhdmd").hide();
										$("#cron_mhdmd2").show();
										$("#cron_mhdmd2").click(function() {
											if ($(this).val() == "other") {
												$("#cron_table").show();
												$("#cron_add fieldset").css("width", "900px");
											}
											else {
												$("#cron_table").hide();
												$("#cron_add fieldset").css("width", "");
											}
										});
										$("#cron_add").submit(function() {
											// http://www.php.net/manual/fr/function.preg-match.php#93824
											var reg = /^https?\:\/\/([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?([a-z0-9-.]*)(\.([a-z]{2,3}))?(\:[0-9]{2,5})?(\/([a-z0-9+\$_-]\.?)+)*\/?(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?(#[a-z_.-][a-z0-9+\$_.-]*)?$/i;
											if ( !reg.test($("#cron_url").val() ) ) {
												alert (\''.$this->l('URL Invalide', __CLASS__, true).'\');
												return false;
											}
											var mhdmd = "";
											if ($("#cron_mhdmd2").val() != "other")
												mhdmd = $("#cron_mhdmd2").val();
											else {
												// minutes
												if ($("input:radio[name=all_mins]:checked").val() == "1")
													mhdmd = "*";
												else {
													var tmp = "";
													$("select[name=mins]").each(function(){
														if ($(this).val())
															tmp = tmp + "," + $(this).val().join(",");											
													});
													if (tmp == "")
														tmp = ",*";
													mhdmd = tmp.slice(1);
												}
												// hours
												mhdmd = mhdmd + " ";
												if ($("input:radio[name=all_hours]:checked").val() == "1")
													mhdmd = mhdmd + "*";
												else {
													var tmp = "";
													$("select[name=hours]").each(function(){
														if ($(this).val())
															tmp = tmp + "," + $(this).val().join(",");											
													});
													if (tmp == "")
														tmp = ",*";
													mhdmd = mhdmd + tmp.slice(1);
												}
												// days
												mhdmd = mhdmd + " ";
												if ($("input:radio[name=all_days]:checked").val() == "1")
													mhdmd = mhdmd + "*";
												else {
													var tmp = "";
													$("select[name=days]").each(function(){
														if ($(this).val())
															tmp = tmp + "," + $(this).val().join(",");											
													});
													if (tmp == "")
														tmp = ",*";
													mhdmd = mhdmd + tmp.slice(1);
												}
												// months
												mhdmd = mhdmd + " ";
												if ($("input:radio[name=all_months]:checked").val() == "1")
													mhdmd = mhdmd + "*";
												else {
													var tmp = "";
													$("select[name=months]").each(function(){
														if ($(this).val())
															tmp = tmp + "," + $(this).val().join(",");											
													});
													if (tmp == "")
														tmp = ",*";
													mhdmd = mhdmd + tmp.slice(1);
												}
												// weekdays
												mhdmd = mhdmd + " ";
												if ($("input:radio[name=all_weekdays]:checked").val() == "1")
													mhdmd = mhdmd + "*";
												else {
													var tmp = "";
													$("select[name=weekdays]").each(function(){
														if ($(this).val())
															tmp = tmp + "," + $(this).val().join(",");											
													});
													if (tmp == "")
														tmp = ",*";
													mhdmd = mhdmd + tmp.slice(1);
												}
											}
											$("#cron_mhdmd").val(mhdmd);
										});
									});
									function enable_cron_fields(name, form, ena)
									{
										var els = form.elements[name];
										els.disabled = !ena;
										for(i=0; i<els.length; i++) {
										  els[i].disabled = !ena;
										  }
										}
								//--></script>
								<table class="table" style="display: none; width:100%" id="cron_table">
									<thead>
										<tr>
											<th>'.$this->l('Minutes').'</th>
											<th>'.$this->l('Heures').'</th>
											<th>'.$this->l('Jour du Mois').'</th>
											<th>'.$this->l('Mois').'</th>
											<th>'.$this->l('Jour de la semaine').'</th>
										</tr>
									</thead>
									<tfoot>
										<tr>
											<td colspan="5">'.$this->l('Utilisez [ctrl] ou [command] Pour selectionner plusieur valeurs').'</td>
										</tr>
									</tfoot>
									<tbody>
										<tr>
											<td valign="top">
												<input type="radio" name="all_mins" value="1" checked="checked" onclick="enable_cron_fields(\'mins\', form, 0)"/> '.$this->l('Tout').'<br/>
												<input type="radio" name="all_mins" value="0" onclick="enable_cron_fields(\'mins\', form, 1)"/> '.$this->l('Selectionner').'<br/>
												<table>
													<tr>
														<td valign="top">
															<select multiple="multiple" size="12" name="mins" disabled="disabled">
																<option value="0" >00</option>
																<option value="1" >01</option>
																<option value="2" >02</option>
																<option value="3" >03</option>
																<option value="4" >04</option>
																<option value="5" >05</option>
																<option value="6" >06</option>
																<option value="7" >07</option>
																<option value="8" >08</option>
																<option value="9" >09</option>
																<option value="10" >10</option>
																<option value="11" >11</option>
															</select>
														</td>
														<td valign="top">
															<select multiple="multiple" size="12" name="mins" disabled="disabled">
																<option value="12" >12</option>
																<option value="13" >13</option>
																<option value="14" >14</option>
																<option value="15" >15</option>
																<option value="16" >16</option>
																<option value="17" >17</option>
																<option value="18" >18</option>
																<option value="19" >19</option>
																<option value="20" >20</option>
																<option value="21" >21</option>
																<option value="22" >22</option>
																<option value="23" >23</option>
															</select>
														</td>
														<td valign="top">
															<select multiple="multiple" size="12" name="mins" disabled="disabled">
																<option value="24" >24</option>
																<option value="25" >25</option>
																<option value="26" >26</option>
																<option value="27" >27</option>
																<option value="28" >28</option>
																<option value="29" >29</option>
																<option value="30" >30</option>
																<option value="31" >31</option>
																<option value="32" >32</option>
																<option value="33" >33</option>
																<option value="34" >34</option>
																<option value="35" >35</option>
															</select>
														</td>
														<td valign="top">
															<select multiple="multiple" size="12" name="mins" disabled="disabled">
																<option value="36" >36</option>
																<option value="37" >37</option>
																<option value="38" >38</option>
																<option value="39" >39</option>
																<option value="40" >40</option>
																<option value="41" >41</option>
																<option value="42" >42</option>
																<option value="43" >43</option>
																<option value="44" >44</option>
																<option value="45" >45</option>
																<option value="46" >46</option>
																<option value="47" >47</option>
															</select>
														</td>
														<td valign="top">
															<select multiple="multiple" size="12" name="mins" disabled="disabled">
																<option value="48" >48</option>
																<option value="49" >49</option>
																<option value="50" >50</option>
																<option value="51" >51</option>
																<option value="52" >52</option>
																<option value="53" >53</option>
																<option value="54" >54</option>
																<option value="55" >55</option>
																<option value="56" >56</option>
																<option value="57" >57</option>
																<option value="58" >58</option>
																<option value="59" >59</option>
															</select>
														</td>
													</tr>
												</table>
											</td>
											<td valign="top">
												<input type="radio" name="all_hours" value="1"  checked="checked" onclick="enable_cron_fields(\'hours\', form, 0)"/> '.$this->l('Tout').'<br/>
												<input type="radio" name="all_hours" value="0"  onclick="enable_cron_fields(\'hours\', form, 1)"/> '.$this->l('Selectionner').'<br/>
												<table>
													<tr>
														<td valign="top">
															<select multiple="multiple" size="12" name="hours" disabled="disabled">
																<option value="0" >00</option>
																<option value="1" >01</option>
																<option value="2" >02</option>
																<option value="3" >03</option>
																<option value="4" >04</option>
																<option value="5" >05</option>
																<option value="6" >06</option>
																<option value="7" >07</option>
																<option value="8" >08</option>
																<option value="9" >09</option>
																<option value="10" >10</option>
																<option value="11" >11</option>
															</select>
														</td>
														<td valign="top">
															<select multiple="multiple" size="12" name="hours" disabled="disabled">
																<option value="12" >12</option>
																<option value="13" >13</option>
																<option value="14" >14</option>
																<option value="15" >15</option>
																<option value="16" >16</option>
																<option value="17" >17</option>
																<option value="18" >18</option>
																<option value="19" >19</option>
																<option value="20" >20</option>
																<option value="21" >21</option>
																<option value="22" >22</option>
																<option value="23" >23</option>
															</select>
														</td>
													</tr>
												</table>
											</td>
											<td valign="top">
												<input type="radio" name="all_days" value="1"  checked="checked" onclick="enable_cron_fields(\'days\', form, 0)"/> '.$this->l('Tout').'<br/>
												<input type="radio" name="all_days" value="0"  onclick="enable_cron_fields(\'days\', form, 1)"/> '.$this->l('Selectionner').'<br/>
												<table>
													<tr>
														<td valign="top">
															<select multiple="multiple" size="12" name="days" disabled="disabled">
																<option value="1" >01</option>
																<option value="2" >02</option>
																<option value="3" >03</option>
																<option value="4" >04</option>
																<option value="5" >05</option>
																<option value="6" >06</option>
																<option value="7" >07</option>
																<option value="8" >08</option>
																<option value="9" >09</option>
																<option value="10" >10</option>
																<option value="11" >11</option>
																<option value="12" >12</option>
															</select>
														</td>
														<td valign="top">
															<select multiple="multiple" size="12" name="days" disabled="disabled">
																<option value="13" >13</option>
																<option value="14" >14</option>
																<option value="15" >15</option>
																<option value="16" >16</option>
																<option value="17" >17</option>
																<option value="18" >18</option>
																<option value="19" >19</option>
																<option value="20" >20</option>
																<option value="21" >21</option>
																<option value="22" >22</option>
																<option value="23" >23</option>
																<option value="24" >24</option>
															</select>
														</td>
														<td valign="top">
															<select multiple="multiple" size="7" name="days" disabled="disabled">
																<option value="25" >25</option>
																<option value="26" >26</option>
																<option value="27" >27</option>
																<option value="28" >28</option>
																<option value="29" >29</option>
																<option value="30" >30</option>
																<option value="31" >31</option>
															</select>
														</td>
													</tr>
												</table>
											</td>
											<td valign="top">
												<input type="radio" name="all_months" value="1"  checked="checked" onclick="enable_cron_fields(\'months\', form, 0)"/> '.$this->l('Tout').'<br/>
												<input type="radio" name="all_months" value="0"  onclick="enable_cron_fields(\'months\', form, 1)"/> '.$this->l('Selectionner').'<br/>
												<table>
													<tr>
														<td valign="top">
															<select multiple="multiple" size="12" name="months" disabled="disabled">
																<option value="1" >'.$this->l('Janvier').'</option>
																<option value="2" >'.$this->l('Fevrier').'</option>
																<option value="3" >'.$this->l('Mars').'</option>
																<option value="4" >'.$this->l('Avril').'</option>
																<option value="5" >'.$this->l('Mai').'</option>
																<option value="6" >'.$this->l('Juin').'</option>
																<option value="7" >'.$this->l('Juillet').'</option>
																<option value="8" >'.$this->l('Aout').'</option>
																<option value="9" >'.$this->l('Septembre').'</option>
																<option value="10" >'.$this->l('Octobre').'</option>
																<option value="11" >'.$this->l('Novembre').'</option>
																<option value="12" >'.$this->l('Decembre').'</option>
															</select>
														</td>
													</tr>
												</table>
											</td>
											<td valign="top">
												<input type="radio" name="all_weekdays" value="1"  checked="checked" onclick="enable_cron_fields(\'weekdays\', form, 0)"/> '.$this->l('Tout').'<br/>
												<input type="radio" name="all_weekdays" value="0"  onclick="enable_cron_fields(\'weekdays\', form, 1)"/> '.$this->l('Selectionner').'<br/>
												<table>
													<tr>
														<td valign="top">
															<select multiple="multiple" size="7" name="weekdays" disabled="disabled">
																<option value="0" >'.$this->l('Dimanche').'</option>
																<option value="1" >'.$this->l('Lundi').'</option>
																<option value="2" >'.$this->l('Mardi').'</option>
																<option value="3" >'.$this->l('Mercredi').'</option>
																<option value="4" >'.$this->l('Jeudi').'</option>
																<option value="5" >'.$this->l('Vendredi').'</option>
																<option value="6" >'.$this->l('samedi').'</option>
															</select>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</tbody>
								</table>
							<p><center><input type="submit" class="button" name="submitAddCron" value="'.$this->l('Ajouter cette tache').'" /></center></p>
							</fieldset>*/.'
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
 
        private function _postProcess() {
            if (Tools::getValue('cron_method'))
                Configuration::updateValue('cron_method', Tools::getValue('cron_method'));

            if (Tools::isSubmit('submitAddCron'))
                {
                $this->addCronURL(Tools::getValue('cron_url'), Tools::getValue('cron_mhdmd'));
                return '
                <div class="conf confirm">
                    <img src="../img/admin/ok.gif" alt="'.$this->l('Tache ajoutee').'" />
                    '.$this->l('Tache ajoutee').'
                </div>';
                return $output;
                }
            }
        private function _displayList() 
            {
            global $cookie;
            $output = '';
            // get the jobs
            $sql = 'SELECT * FROM `'._DB_PREFIX_.'cron`';
            $crons = Db::getInstance()->executeS($sql);
            $sql = 'SELECT * FROM `'._DB_PREFIX_.'cron_url`';
            $crons_url = Db::getInstance()->executeS($sql);
            if ($crons || $crons_url) 
                {
                $output .= '
                <fieldset width="900px" class="space">
                <legend>'.$this->l('Crons jobs').'</legend>
                <table class="table">';
                if ($crons) 
                    {
                    $output .= '
                        <tr>
                        <th>'.$this->l('Module').'</th>
                        <th>'.$this->l('Method').'</th>
                        <th>'.$this->l('Schedule').'</th>
                        <th>'.$this->l('Last execution').'</th>
                        <th>'.$this->l('Action').'</th>
                        </tr>';
                        foreach ($crons as $cron) 
                            {
                            $module = Db::getInstance()->GetRow('
                            SELECT `name`
                            FROM `'._DB_PREFIX_.'module`
                            WHERE `id_module` = '.intval($cron['id_module']));
                            $output .= '
                            <tr>
                            <td>'.$module['name'].'</td>
                            <td>'.$cron['method'].'</td>
                            <td>'.$cron['mhdmd'].'</td>
                            <td>'.($cron['last_execution']?Tools::displayDate(date('Y-m-d H:i:s',$cron['last_execution']), $cookie->id_lang, true):$this->l('Never')).'</td>
                            <td><a href="'.htmlentities($_SERVER['REQUEST_URI']).'&amp;delete='.$cron['id_cron'].'"><img src="../img/admin/delete.gif" alt="'.$this->l('Delete').'" title="'.$this->l('Delete').'" /></a></td>
                            </tr>
                            ';
                            }
                    }
                if ($crons_url) 
                    {
                    $output .= '
                    <tr>
                    <th colspan="2">'.$this->l('Url').'</th>
                    <th>'.$this->l('Schedule').'</th>
                    <th>'.$this->l('Last execution').'</th>
                    <th>'.$this->l('Action').'</th>
                    </tr>';
                    foreach ($crons_url as $cron) 
                        {
                        $output .= '
                        <tr>
                        <td colspan="2">'.$cron['url'].'</td>
                        <td>'.$cron['mhdmd'].'</td>
                        <td>'.($cron['last_execution']?Tools::displayDate(date('Y-m-d H:i:s',$cron['last_execution']), $cookie->id_lang, true):$this->l('Never')).'</td>
                        <td><a href="'.htmlentities($_SERVER['REQUEST_URI']).'&amp;delete_url='.$cron['id_cron_url'].'"><img src="../img/admin/delete.gif" alt="'.$this->l('Delete').'" title="'.$this->l('Delete').'" /></a></td>
                        </tr>
                        ';
                        }
                    }
                    $output .= '
                  </table>
                  </fieldset>';
                  }
              return $output;
              }
        public function hookFooter($params)
            {
            if (Configuration::get('cron_method') == 'traffic' &&(!Configuration::get('cron_lasttime') ||(Configuration::get('cron_lasttime') + 60 <= time())))
                return '<img src="'.$this->_path.'cron_traffic.php?time='.time().'" alt="cron module by samdha.net" width="0" height="0" style="border:none;margin:0; padding:0"/>';
            }
        public function addCron($id_module, $method, $mhdmd = '0 * * * *') 
            {
            if (!$this->active)
            return false;
            require_once(dirname(__FILE__).'/CronParser.php');
            if (!$module = Module::getInstanceById($id_module)) 
                {
                $this->_postErrors[] = $this->l('This module doesn\'t exists.');
                return false;
                }
            $classMethods = array_map('strtolower', get_class_methods($module));
            if (!$classMethods || !in_array(strtolower($method), $classMethods)) 
                {
                $this->_postErrors[] = $this->l('This method doesn\'t exists.');
                return false;
                }
            $cronParser = new CronParser();
            if (!$cronParser->calcLastRan($mhdmd)) 
                {
                $this->_postErrors[] = $this->l('This shedule isn\'t valide.');
                return false;
                }
            $values = array('id_module' => intval($id_module),'method' => pSQL($method),'mhdmd' => pSQL($mhdmd),'last_execution' => 0 );
            return Db::getInstance()->autoExecute(_DB_PREFIX_.'cron', $values, 'INSERT');
            }
        public function deleteCron($id_module, $method) 
            {
            if (!$this->active)
                return false;
            return Db::getInstance()->delete(_DB_PREFIX_.'cron','`id_module` = '.intval($id_module).' AND `method` = \''.pSQL($method).'\'');		
            }
        public function deleteCronByID($id_cron) 
            {
            if (!$this->active)
                return false;
            return Db::getInstance()->delete(_DB_PREFIX_.'cron','`id_cron` = '.intval($id_cron));		
            }
        public function cronExists($id_module, $method) 
            {
            if (!$this->active)
                return false;
            $sql = 'SELECT id_cron FROM `'._DB_PREFIX_.'cron` WHERE `id_module` = '.intval($id_module).' AND `method` = \''.pSQL($method).'\'';
            $cron = Db::getInstance()->getRow($sql);
            return is_array($cron);
            }
        public function addCronURL($url, $mhdmd = '0 * * * *') 
            {
            if (!$this->active)
                return false;
            require_once(dirname(__FILE__).'/CronParser.php');
            $cronParser = new CronParser();
            if (!$cronParser->calcLastRan($mhdmd)) 
                {
                $this->_postErrors[] = $this->l('This shedule isn\'t valide.');
                return false;
                }
            $values = array('url' => pSQL($url),'mhdmd' => pSQL($mhdmd),'last_execution' => 0 );
            return Db::getInstance()->autoExecute(_DB_PREFIX_.'cron_url', $values, 'INSERT');
            }
        public function deleteCronURL($url) 
            {
            if (!$this->active)
                return false;
            return Db::getInstance()->delete(_DB_PREFIX_.'cron_url','`url` = \''.pSQL($url).'\'');		
            }
        public function deleteCronURLByID($id_cron_url) 
            {
            if (!$this->active)
                return false;
            return Db::getInstance()->delete(_DB_PREFIX_.'cron_url','`id_cron_url` = '.intval($id_cron_url));		
            }
        public function cronURLExists($url) 
            {
            if (!$this->active)
                return false;
            $sql = 'SELECT id_cron_url FROM `'._DB_PREFIX_.'cron_url` WHERE `url` = \''.pSQL($url).'\'';
            $cron = Db::getInstance()->getRow($sql);
            return is_array($cron);
            }
        public function runJobs() 
            {
            if ($this->active &&(Configuration::get('cron_lasttime') + 60 <= time())) 
                {
                Configuration::updateValue('cron_lasttime', time());
                require_once(dirname(__FILE__).'/CronParser.php');
                $cronParser = new CronParser();
                $sql = 'SELECT * FROM `'._DB_PREFIX_.'cron`';
                $crons = Db::getInstance()->executeS($sql);
                foreach ($crons as $cron) 
                    {
                    $cronParser->calcLastRan($cron['mhdmd']);
                    var_dump($cron['mhdmd'], date('r', $cronParser->getLastRanUnix()), date('r', $cron['last_execution']));
                    if ($cronParser->getLastRanUnix() > $cron['last_execution']) 
                        {
                        if (!$module = Module::getInstanceById($cron['id_module'])) 
                            {
                            $this->deleteCron($cron['id_module'], $cron['method']);
                            }
                        else 
                            {
                            $classMethods = array_map('strtolower', get_class_methods($module));
                            if (!$classMethods || !in_array(strtolower($cron['method']), $classMethods)) 
                                {
                                $this->deleteCron($cron['id_module'], $cron['method']);
                                }
                            else 
                                {
                                $values = array('last_execution' => time());
                                Db::getInstance()->autoExecute(_DB_PREFIX_.'cron', $values, 'UPDATE', 'id_cron = '.$cron['id_cron']);
                                call_user_func(array($module, $cron['method']));
                                }
                            }
                        }
                    }
                $sql = 'SELECT * FROM `'._DB_PREFIX_.'cron_url`';
                $crons = Db::getInstance()->executeS($sql);
                foreach ($crons as $cron) 
                    {
                    $cronParser->calcLastRan($cron['mhdmd']);
                    if ($cronParser->getLastRanUnix() > $cron['last_execution']) 
                        {
                        $values = array('last_execution' => time());
                        Db::getInstance()->autoExecute(_DB_PREFIX_.'cron_url', $values, 'UPDATE', 'id_cron_url = '.$cron['id_cron_url']);
                        @file_get_contents($cron['url']);
                        }
                    }
                }
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
