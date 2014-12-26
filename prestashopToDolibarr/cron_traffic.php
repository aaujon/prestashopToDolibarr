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
		
if (Configuration::get('cron_method') == 'traffic')
    Module::getInstanceByName('all4doli')->runJobs();

// generate empty picture http://www.nexen.net/articles/dossier/16997-une_image_vide_sans_gd.php
$hex = "47494638396101000100800000ffffff00000021f90401000000002c00000000010001000002024401003b";
$img = '';
$t = strlen($hex) / 2;
for($i = 0; $i < $t; $i++) 
    $img .= chr(hexdec(substr($hex, $i * 2, 2) ));
header('Last-Modified: Fri, 01 Jan 1999 00:00 GMT', true, 200);
header('Content-Length: '.strlen($img));
header('Content-Type: image/gif');
echo $img;
?>
