<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include('../../config/config.inc.php');
include('stringUtils.php');
include('dolibarr/DolibarrApi.php');

function synchroOrder($id_order)
{
	echo "Synchronisation order : $id_order<br>"; 
	

	// Retrieve order information
	$order = Db::getInstance()->GetRow("select * from "._DB_PREFIX_."orders where id_order='".$id_order."'");
	$id_customer=$order['id_customer'];
	$id_address_delivery=$order['id_address_delivery'];
	$date_order=$order['date_add'];
	$total=$order['total_paid'];
	$total = sprintf("%.2f",$total);
	//$total_paid_real_TTC=$order['total_paid_real'];
	//$total_paid_real_TTC=sprintf("%.2f",$total_paid_real_TTC);

	$total_net=$order['total_paid_tax_excl'];
	//$total_a_payer_HT=sprintf("%.2f",$total_a_payer_HT);
	$total_vat = $total - $total_net;
	$total_vat=sprintf("%.2f",$total_vat);

	// CALCUL DU PORT *********************************** 
	$total_shipping_TTC = $order['total_shipping_tax_incl'];
	$total_shipping_TTC=sprintf("%.2f",$total_shipping_TTC);
	$total_shipping_HT = $order['total_shipping_tax_excl'];
	$total_shipping_HT=sprintf("%.2f",$total_shipping_HT);
	$carrier_tax_rate = $order['carrier_tax_rate'];
	$carrier_tax_rate=sprintf("%.2f",$carrier_tax_rate);
	// FIN CALCUL DU PORT ***********************************

	$total_shipping_TVA = $total_shipping_TTC - $total_shipping_HT;
	$total_shipping_TVA=sprintf("%.2f",$total_shipping_TVA);
	$type_paiement = $order['payment'];
	$type_paiement = accents_minuscules("$type_paiement");   
	$valid=$order['valid'];
	
	if ($valid == 0) {
		echo "order is not valid. skip it.";
		return;
	}

	// get order status
	$create_invoice = false;
	$statut_propal=4;       //** Propal validée signée
	$order_status=0;     // draft by default
	$commande_facturee=0;   //** Commande NON facturée
	$invoice_status=0;      //** Facture en brouillon
	$paye=0;                //** Facture non payée
	$facture=0;             //** Commande Facturée --> Passe la commande en traitée quand statut='Livré'
	
	// handle order status
	$state=$order['current_state'];
	switch ($state) {
		case 0: // Paiement by check or virement
		case 10: // Paiement by check or virement
			$order_status = 0; // draft
			break;
		case 2: // Paiement accepted
			$create_invoice = true;
			$order_status = 1; // > validated
			break;
		case 3: // En cours de préparation
			$create_invoice = true;
			//$order_status = 2;           // Commande en Envoi en cours (mais pas encore expédiée)
			$order_status = 2;  // In progress
			break;
		case 4: // In delivery
			$create_invoice = true;
			$order_status = 3; // > closed

			break;
		
		case 5: // delivered
		case 35: // delivered
		case 37: // delivered
			$create_invoice = true;
			$order_status = 3; // > closed (has been sent)

			break;
		case 6: // cancelled or refund
		case 7: // cancelled or refund
			//$order_status = 0; // due to current dolibarr limitation using webservices
			$order_status='-1';        // canceled
			break;
		case 8: // paiement error or not validated
			$order_status=0;           // draft
			break;
	}
	
	// For now, we only want to synchonize orders that have been validated and paid
	if ($order_status <= 0) {
		echo "<br />Error : order isn't valid. Status ==".$order_status;
		return;
	}

	// load order details
	$products = Db::getInstance()->executeS("select * from "._DB_PREFIX_."order_detail where id_order='".$id_order."'");
	$count = 0;
	foreach ( $products as $product )
	{
		$line = new DolibarrOrderLines();
		$line->desc = $product['product_name'];
		$line->qty = $product['product_quantity'];
		$line->unitprice = $product['unit_price_tax_excl'];
		$line->remise = $product['reduction_amount'];
		$line->remise_percent = $product['reduction_percent'];
		$line->total_net = $product['total_price_tax_excl'];
		$line->total = $product['total_price_tax_incl'];
		$line->total_vat = 	sprintf("%.2f", $product['total_price_tax_incl'] - $product['total_price_tax_excl']);
		
		// vat_rate isn't set properly on order_detail so compute it...
		$line->vat_rate = sprintf("%.1f", ($product['total_price_tax_incl'] - $product['total_price_tax_excl'])/$product['total_price_tax_excl']*100);
		if ($line->vat_rate > 19.8 && $line->vat_rate < 20.2) {
			$line->vat_rate = 20;
		}else if ($line->vat_rate > 9.8 && $line->vat_rate < 10.2) {
			$line->vat_rate = 10;
		}
		
		$lines[$count]= $line;
		$count++;
	}
	
	// add shipping line
	$lines[$count]= addShippingLine($order);

	$dolibarr = Dolibarr::getInstance();

	// retrieve user
	echo "<br> Client : ";
	var_dump($id_customer);
	$client = $dolibarr->getUser("PSUSER-".$id_customer);
	if ($client["result"]->result_code == 'NOT_FOUND')
    {
		echo "<br />Error : client doesn't exist. Try to synchronize clients first.";
		return;
	}
	echo ", ";
	var_dump($client["thirdparty"]->id);
	
	echo "<br> Address : ";
	var_dump($id_address_delivery);
	
	// Retrieve delivery address
	$fk_delivery_address = retrieveDeliveryAddress($dolibarr, $id_address_delivery);

	// Check if already exists in Dolibarr
	$exists = $dolibarr->getOrder($id_order);

	// Create order
	$dolibarrOrder = new DolibarrOrder();
	$dolibarrOrder->ref_ext = $id_order;
	$dolibarrOrder->thirdparty_id = $client["thirdparty"]->id;
	$dolibarrOrder->fk_delivery_address = (int)$fk_delivery_address;
	$dolibarrOrder->date = $order["date_add"];
	if ($order['delivery_number'] != 0) {
		$dolibarrOrder->date_livraison = $order['delivery_date'];
	}
	$dolibarrOrder->status = 1; // we start with status validated, we wiil update status after if needed
	/*$dolibarrOrder->total = $total;
    $dolibarrOrder->total_net = $total_net;
    $dolibarrOrder->total_vat = $total_v;*/
	$dolibarrOrder->lines = $lines;

	if ($exists["result"]->result_code == 'NOT_FOUND')
    {
		// Create new order
		echo "Create new order : <br>";
		var_dump($dolibarrOrder);
		$result = $dolibarr->createOrder($dolibarrOrder);
		var_dump($result);
		if ($result["result"]->result_code == 'KO')
        {
			echo "<br />Erreur de synchronisation : ".$result["result"]->result_label;
		}
	}

	// update it now to have a correct status
    
	if (strpos(Configuration::get('dolibarr_version'), '3.6.') !== FALSE) {
		echo "<br />Dolibarr version 3.6 can't update orders, skip update. Please consider updating Dolibarr to 3.7 to have a full synchronisation.";
	} else {
		// Update order status
		echo "<br />update order<br>";
		$oldOrder = $exists["order"];
		$dolibarrOrder->status = $order_status;

		$result = $dolibarr->updateOrder($dolibarrOrder);
		if ($result["result"]->result_code == 'KO')
		{
			echo "<br />Erreur de synchronisation : ".$result["result"]->result_label;
		}
	}
	

	// Create invoice if necessary
	if ($create_invoice)
	{
		echo "<br />Creating invoice.<br />";
		//invoices dont really exists in prestashop, so id_order=id_invoice
		$exists = $dolibarr->getInvoice($id_order);

		$dolibarrInvoice = new DolibarrInvoice();
		$dolibarrInvoice->ref_ext = $id_order;
		$dolibarrInvoice->thirdparty_id = $client["thirdparty"]->id;
		$dolibarrInvoice->date = $order["date_add"];
		$dolibarrInvoice->total = $total;
		$dolibarrInvoice->total_net = $total_net;
		$dolibarrInvoice->total_vat = $total_vat;
		if ($order['delivery_number'] != 0) {
			$dolibarrInvoice->date_livraison = $order['delivery_date'];
		}
		$dolibarrInvoice->lines = $lines;
		
		if ($exists["result"]->result_code == 'NOT_FOUND')
		{
			// Create new invoice
			echo "Create new invoice : <br>";
			$result = $dolibarr->createInvoice($dolibarrInvoice);
			if ($result["result"]->result_code == 'KO')
			{
				echo "Erreur de synchronisation : ".$result["result"]->result_label;
			}
		} else
		{
			if (strpos(Configuration::get('dolibarr_version'), '3.6.') !== FALSE) {
				echo "<br />Dolibarr version 3.6 can't update invoices, skip update. Please consider updating Dolibarr to have a full synchronisation.";
			} else {
				// Update invoice
				echo "update invoice<br>";
				$oldInvoice = $exists["invoice"];
				$dolibarrInvoice->id = $oldInvoice->id;
				$result = $dolibarr->updateInvoice($dolibarrInvoice);
				if ($result["result"]->result_code == 'KO')
				{
					echo "Erreur de synchronisation : ".$result["result"]->result_label;
				}
			}
		}
	}

	// mark as paid

}


/** Retrieve client delivery address
 * @param dolibarr
 */

function retrieveDeliveryAddress($dolibarr, $addressDeliveryId) {
	$address = $dolibarr->getContact($addressDeliveryId);
	if ($address["result"]->result_code == 'NOT_FOUND')
    {
		echo "<br />Error : client address doesn't exist. Try to synchronize clients first.";
		return;
	}
	$fk_delivery_address = $address["contact"]->id;
	echo ", ";
	var_dump($address["contact"]->id);
	var_dump($fk_delivery_address);
	return $fk_delivery_address;
}

function addShippingLine($order) {
	$line = new DolibarrOrderLines();
	$line->desc = Configuration::get('delivery_line_label');
	$line->qty = 1;
	$line->unitprice = $order['total_shipping_tax_excl'];
	$line->remise = 0;
	$line->remise_percent = 0;
	$line->total_net = $order['total_shipping_tax_excl'];
	$line->total = $order['total_shipping_tax_incl'];
	$line->total_vat = 	sprintf("%.2f", $order['total_shipping_tax_incl'] - $order['total_shipping_tax_excl']);

	// compute vat_rate
	if ($order['total_shipping_tax_excl'] == 0 || $order['total_shipping_tax_incl'] == 0) {
		$line->vat_rate = 0;
	} else {
		$line->vat_rate = sprintf("%.1f", ($order['total_shipping_tax_incl'] - $order['total_shipping_tax_excl'])/$order['total_shipping_tax_excl']*100);
		if ($line->vat_rate > 19.8 && $line->vat_rate < 20.2) {
			$line->vat_rate = 20;
		} else if ($line->vat_rate > 9.8 && $line->vat_rate < 10.2) {
			$line->vat_rate = 10;
		}
	}

	return $line;
}

if (Tools::isSubmit('id_order'))
{
    $id_order=Tools::getValue('id_order');
    synchroOrder($id_order);
}


?>
