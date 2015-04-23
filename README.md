prestashopToDolibarr
====================

A Prestashop module to synchronise clients, products, orders and invoices to Dolibarr.
Dolibarr API is used, so you can have a prestashop and a dolibarr intalled on a different server.

To make it work you need :
* PrestaShop 1.5.x and 1.6.x
* Dolibarr >= 3.6.3 to synchronize clients and products
* Dolibarr >= 3.7.0 to synchronize orders
* php-soap module on your serveur


You need to active Dolibarr webservice to allow prestashop to communicate with your dolibarr instance.

Originally based on deprecated "all4doli" module by Presta 2 Doli.
