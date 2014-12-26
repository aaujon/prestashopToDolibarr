<?php
/**
 * Cron module
 * Cron is a time-based job scheduler in Unix-like computer operating systems.
 * This module automaticaly executes jobs like Cron
 *
 * @category Prestashop
 * @category Module
 * @author Samdha <contact@samdha.net>
 * @copyright Samdha
 * @license http://www.opensource.org/licenses/osl-3.0.php Open-source licence 3.0
 * @author logo Alessandro Rei
 * @license logo http://www.gnu.org/copyleft/gpl.html GPLv3
 * @version 1.2
**/
require(dirname(__FILE__).'/../../config/config.inc.php');
include_once(_PS_ROOT_DIR_.'/init.php');
		
if (Configuration::get('cron_method') == 'webcron')
    Module::getInstanceByName('all4doli')->runJobs();
?>
