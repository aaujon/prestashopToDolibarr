<?php
/*<xsd:complexType name="invoice"><xsd:all><xsd:element name="id" type="xsd:string"/><xsd:element name="ref" type="xsd:string"/><xsd:element name="ref_ext" type="xsd:string"/><xsd:element name="thirdparty_id" type="xsd:int"/><xsd:element name="fk_user_author" type="xsd:string"/><xsd:element name="fk_user_valid" type="xsd:string"/><xsd:element name="date" type="xsd:date"/><xsd:element name="date_due" type="xsd:date"/><xsd:element name="date_creation" type="xsd:dateTime"/><xsd:element name="date_validation" type="xsd:dateTime"/><xsd:element name="date_modification" type="xsd:dateTime"/><xsd:element name="type" type="xsd:int"/><xsd:element name="total_net" type="xsd:double"/><xsd:element name="total_vat" type="xsd:double"/><xsd:element name="total" type="xsd:double"/><xsd:element name="note_private" type="xsd:string"/><xsd:element name="note_public" type="xsd:string"/><xsd:element name="status" type="xsd:int"/><xsd:element name="close_code" type="xsd:string"/><xsd:element name="close_note" type="xsd:string"/><xsd:element name="project_id" type="xsd:string"/><xsd:element name="lines" type="tns:LinesArray2"/></xsd:all></xsd:complexType>

xsd:complexType name="line"><xsd:all><xsd:element name="id" type="xsd:string"/><xsd:element name="type" type="xsd:int"/><xsd:element name="desc" type="xsd:string"/><xsd:element name="vat_rate" type="xsd:double"/><xsd:element name="qty" type="xsd:double"/><xsd:element name="unitprice" type="xsd:double"/><xsd:element name="total_net" type="xsd:double"/><xsd:element name="total_vat" type="xsd:double"/><xsd:element name="total" type="xsd:double"/><xsd:element name="date_start" type="xsd:date"/><xsd:element name="date_end" type="xsd:date"/><xsd:element name="product_id" type="xsd:int"/><xsd:element name="product_ref" type="xsd:string"/><xsd:element name="product_label" type="xsd:string"/><xsd:element name="product_desc" type="xsd:string"/></xsd:all></xsd:complexType>*/

class DolibarrInvoiceLines {
    public $id;
	public $type; // nom
	public $desc;
    public $vate_rate;
    public $qty;
    public $unitprice;
    public $total_net;
    public $total_vat;
    public $total;
	public $date_start = ""; // dateTime
	public $date_end = ""; // dateTime
	public $payment_mode_id = ""; // unused
	public $product_id = "";
	public $product_ref = "";
	public $product_label = "";
	public $product_desc = "";
}

class DolibarrInvoice {
    public $id;
	public $ref; // nom
	public $ref_ext;
    public $thirdparty_id;
    public $fk_user_author;
    public $fk_user_valid;
	public $date = ""; // dateTime
	public $date_due = ""; // dateTime
	public $date_creation = ""; // dateTime
	public $date_validation = ""; // dateTime
	public $date_modification = ""; // dateTime
	//payment_mode_id
    public $type = "";
    public $total_net;
    public $total_vat;
    public $total;
    public $note_private = "Synchronised from Prestashop";
    public $note_public = "";
    public $status = 2; // 1 = validated, 2 = paid
    public $close_code = null; // null mean totally paid
    public $close_note;
    public $project_id;
    public $lines = array(); // array of DolibarrInvoiceLines
}

?>
