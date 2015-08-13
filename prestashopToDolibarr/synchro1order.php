<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include('../../config/config.inc.php');
include('stringUtils.php');
include('dolibarr/DolibarrApi.php');

function synchroOrder($id_order)
{
	echo "Synchronier order : $id_order<br>"; 
	

	// Retrieve order information
	$order = Db::getInstance()->GetRow("select * from "._DB_PREFIX_."orders where id_order='".$id_order."'");
	$id_customer=$order['id_customer'];
	$id_address_delivery=$order['id_address_delivery'];
	$date_order=$order['date_add'];
	$total=$order['total_paid'];
	$total = sprintf("%.2f",$total);
	$total_net=$order['total_paid_tax_excl'];
	$total_vat = $total - $total_net;
	$total_vat=sprintf("%.2f",$total_vat);

	// delivery
	$total_shipping_TTC = $order['total_shipping_tax_incl'];
	$total_shipping_TTC=sprintf("%.2f",$total_shipping_TTC);
	$total_shipping_HT = $order['total_shipping_tax_excl'];
	$total_shipping_HT=sprintf("%.2f",$total_shipping_HT);
	$carrier_tax_rate = $order['carrier_tax_rate'];
	$carrier_tax_rate=sprintf("%.2f",$carrier_tax_rate);
	$delivery_number = $order['delivery_number'];
	$delivery_date = $order['delivery_date'];

	$total_shipping_TVA = $total_shipping_TTC - $total_shipping_HT;
	$total_shipping_TVA=sprintf("%.2f",$total_shipping_TVA);
	$payment_type = $order['module']; // instead of payment which is localized
	$valid=$order['valid'];
	
	if ($valid == 0) {
		echo "order is not valid. skip it.";
		return TRUE;
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
			$invoice_status = 2; // paid
			$order_status = 1; // validated
			break;
		case 3: // En cours de préparation
			$create_invoice = true;
			$invoice_status = 2; // paid
			$order_status = 2;  // In delivery (not sent yet)
			break;
		case 4: // In delivery
			$create_invoice = true;
			$invoice_status = 2; // paid
			$order_status = 3; // closed

			break;
		
		case 5: // delivered
		case 35: // delivered
		case 37: // delivered
			$create_invoice = true;
			$invoice_status = 2; // paid
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
		echo "<br />Warning : order isn't valid. Status ==".$order_status;
		return TRUE;
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
	$client = $dolibarr->getUser("PSUSER-".$id_customer);
	if ($client["result"]->result_code == 'NOT_FOUND')
    {
		echo "<br />Error : client doesn't exist. Try to synchronize clients first.";
		return FALSE;
	}
	
	// Retrieve delivery address
	$fk_delivery_address = retrieveDeliveryAddress($dolibarr, $id_address_delivery);
	if ($fk_delivery_address == false) {
		return FALSE;
	}

	// Check if already exists in Dolibarr
	$exists = $dolibarr->getOrder($id_order);

	// Create order
	$dolibarrOrder = new DolibarrOrder();
	$dolibarrOrder->ref_ext = $id_order;
	$dolibarrOrder->thirdparty_id = $client["thirdparty"]->id;
	$dolibarrOrder->fk_delivery_address = (int)$fk_delivery_address;
	$dolibarrOrder->date = $order["date_add"];
	if ($delivery_number != "0") {
		$dolibarrOrder->date_livraison = $delivery_date;
	}
	$dolibarrOrder->status = 1; // we start with status validated, we will update status after if needed
	/*$dolibarrOrder->total = $total;
    $dolibarrOrder->total_net = $total_net;
    $dolibarrOrder->total_vat = $total_v;*/
	$dolibarrOrder->lines = $lines;

	if ($exists["result"]->result_code == 'NOT_FOUND')
    {
		// Create new order
		echo "Create new order : ";
		var_dump($dolibarrOrder);
		$result = $dolibarr->createOrder($dolibarrOrder);
		
		echo $result["result"]->result_code . "<br/>" ;

		if ($result["result"]->result_code != 'OK')
        {
			echo "<br />Erreur de synchronisation : ".$result["result"]->result_label;
			return FALSE;
		}
	}

	// update it now to have a correct status
    
	if (version_compare(Configuration::get('dolibarr_version'), '3.7.0') == -1) {
		echo "<br />Dolibarr version < 3.7 can't update orders, skip update. Please consider updating Dolibarr to 3.7.x to have a full synchronisation.";
	} else {
		// Update order status
		echo "<br />update order >status = ".$order_status;
		$oldOrder = $exists["order"];
		$dolibarrOrder->status = $order_status;

		$result = $dolibarr->updateOrder($dolibarrOrder);
		
		echo $result["result"]->result_code . "<br/>" ;

		if ($result["result"]->result_code != 'OK')
		{
			echo "<br />Erreur de synchronisation : ".$result["result"]->result_label;
			return FALSE;
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
		if ($delivery_number != "0") {
			$dolibarrInvoice->date_livraison = $delivery_date;
		}

		if ($invoice_status == 1 || $invoice_status == 2) {
			// if order is validated or paid, we mark invoice as validated first
			$dolibarrInvoice->status=1;
		}
		if ($payment_type == "paypal") {
			$dolibarrInvoice->payment_mode_id = 50; // ONLINE PAYMENT
		} else if ($payment_type == "cheque") {
			$dolibarrInvoice->payment_mode_id = 7; // CHEQUE
		} else {
			echo "Payment mode not mapped : ".$payment_type. "-> please report this issue, thanks ! ";
			return FALSE;
		}
		$dolibarrInvoice->lines = createInvoiceLines($lines);
		
		if ($exists["result"]->result_code == 'NOT_FOUND')
		{
			// Create new invoice
			echo "<br>Create new invoice : ";
			$result = $dolibarr->createInvoice($dolibarrInvoice);
			echo $result["result"]->result_code . "<br/>" ;

			if ($result["result"]->result_code != 'OK')
			{
				echo "Erreur de synchronisation : ".$result["result"]->result_label;
				return FALSE;
			}
		}

		if (version_compare(Configuration::get('dolibarr_version'), '3.7.1') == -1) {
			echo "<br />Your version of Dolibarr can't mark invoice as paid, please do it manually. This will be fixed in next version (maybe 3.7.1)";
			return TRUE;
		} else {
			// update invoice status
			echo "<br>update invoice status : ";

			$dolibarrInvoice->status = $invoice_status;
			$result = $dolibarr->updateInvoice($dolibarrInvoice);
			
			echo $result["result"]->result_code . "<br/>" ;

			if ($result["result"]->result_code == 'KO')
			{
				echo "Erreur de synchronisation : ".$result["result"]->result_label;
				return FALSE;
			} else if ($result["result"]->result_code == 'NOT_FOUND')
			{
				echo "Invoice not found : ".$result["result"]->result_label;
				return FALSE;
			}
		}
	}

	return TRUE;
}


/** Retrieve client delivery address
 * @param dolibarr
 */

function retrieveDeliveryAddress($dolibarr, $addressDeliveryId) {
	$address = $dolibarr->getContact($addressDeliveryId);
	if ($address["result"]->result_code == 'NOT_FOUND')
    {
		echo "<br />Error : client address doesn't exist. Try to synchronize clients first.";
		return false;
	}
	$fk_delivery_address = $address["contact"]->id;
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

function createInvoiceLines($order_lines) {
	$count = 0;
	foreach ($order_lines as $order_line) {
		$line = new DolibarrInvoiceLine();
		$line->desc = $order_line->desc;
		$line->vat_rate = $order_line->vat_rate;
		$line->qty = $order_line->qty;
		$line->unitprice = $order_line->unitprice;
		$line->total_net = $order_line->total_net;
		$line->total_vat = $order_line->total_vat;
		$line->total = $order_line->total;
		$line->date_start = ""; // dateTime
		$line->date_end = ""; // dateTime
		$line->payment_mode_id = ""; // unused
		$line->product_id = "";
		$line->product_ref = "";
		$line->product_label = "";
		$line->product_desc = "";
		
		$lines[$count] = $line;
		$count++;
	}
	
	return $lines;
}

if (Tools::isSubmit('id_order'))
{
    $id_order=Tools::getValue('id_order');
    synchroOrder($id_order);
}


?>
