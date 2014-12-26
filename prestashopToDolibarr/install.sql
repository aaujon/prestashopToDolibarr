CREATE TABLE IF NOT EXISTS `PREFIX_cron` (
  `id_cron` int(10) unsigned NOT NULL auto_increment,
  `id_module` int(10) unsigned NOT NULL,
  `method` varchar(100) character set utf8 NOT NULL,
  `mhdmd` varchar(255) NOT NULL,
  `last_execution` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_cron`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `PREFIX_cron_url` (
  `id_cron_url` int(10) unsigned NOT NULL auto_increment,
  `url` varchar(255) character set utf8 NOT NULL,
  `mhdmd` varchar(255) NOT NULL,
  `last_execution` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_cron_url`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
