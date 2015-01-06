<?php
/*<xsd:complexType name="product"><xsd:all><xsd:element name="id" type="xsd:string"/><xsd:element name="ref" type="xsd:string"/><xsd:element name="ref_ext" type="xsd:string"/><xsd:element name="type" type="xsd:string"/><xsd:element name="label" type="xsd:string"/><xsd:element name="description" type="xsd:string"/><xsd:element name="date_creation" type="xsd:dateTime"/><xsd:element name="date_modification" type="xsd:dateTime"/><xsd:element name="note" type="xsd:string"/><xsd:element name="status_tobuy" type="xsd:string"/><xsd:element name="status_tosell" type="xsd:string"/><xsd:element name="barcode" type="xsd:string"/><xsd:element name="barcode_type" type="xsd:string"/><xsd:element name="country_id" type="xsd:string"/><xsd:element name="country_code" type="xsd:string"/><xsd:element name="customcode" type="xsd:string"/><xsd:element name="price_net" type="xsd:string"/><xsd:element name="price" type="xsd:string"/><xsd:element name="price_min_net" type="xsd:string"/><xsd:element name="price_min" type="xsd:string"/><xsd:element name="price_base_type" type="xsd:string"/><xsd:element name="vat_rate" type="xsd:string"/><xsd:element name="vat_npr" type="xsd:string"/><xsd:element name="localtax1_tx" type="xsd:string"/><xsd:element name="localtax2_tx" type="xsd:string"/><xsd:element name="stock_alert" type="xsd:string"/><xsd:element name="stock_real" type="xsd:string"/><xsd:element name="stock_pmp" type="xsd:string"/><xsd:element name="canvas" type="xsd:string"/><xsd:element name="import_key" type="xsd:string"/><xsd:element name="dir" type="xsd:string"/><xsd:element name="images" type="tns:ImagesArray"/></xsd:all></xsd:complexType>*/

class DolibarrProduct {
    public $id;
	public $ref; // nom
	public $ref_ext;
    public $type;
    public $label;
    public $description;
	public $date_creation = ""; // dateTime
	public $date_modification = ""; // dateTime
    public $note = "Synchronized from PrestaShop";
    public $status_tobuy;
    public $status_tosell;
    public $barcode;
    public $barcode_type;
    public $country_id;
    public $country_code;
    public $customcode;
    public $price_net;
    public $price;
    public $price_min_net;
    public $price_min;
    public $price_base_type;
    public $vat_rate;
    public $vat_npr;
    public $localtax1_tx;
    public $localtax2_tx;
    public $stock_alert;
    public $stock_real;
    public $stock_pmp;
    public $canvas;
    public $import_key;
    public $dir;
    public $images;
?>
