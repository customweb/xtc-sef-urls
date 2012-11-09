<?php
/**
::Header::
 */

require_once DIR_FS_ADMIN.'includes/modules/sef_urls/class.Export.php';

class PreisroboterSefUrl extends Export
{
	function PreisroboterSefUrl()
	{
		$this->code 		= 'PreisroboterSefUrl';
		$this->Export();
		$this->title 		= MODULE_PREISROBOTER_SEF_URL_TEXT_TITLE;
		$this->description 	= MODULE_PREISROBOTER_SEF_URL_TEXT_DESCRIPTION;
		$this->sort_order 	= MODULE_PREISROBOTER_SEF_URL_SORT_ORDER;
		$this->enabled 		= ((MODULE_PREISROBOTER_SEF_URL_STATUS == 'True') ? true : false);
		$this->schema 		= '';
		// Artikel-Nr.*|Artikelname*|Preis*|Deeplink*|Bild-URL|Kurzbeschr.|Versandk.|Lieferzt.|EAN|PZN|Hersteller|Hersteller-ArtNr
		$this->varSchema	= 
					'$product_id'."|" . 
					'$product_name' . "|" . 
					'$price' . "|" . 
					'$product_url' . "|" . 
					'$product_image_url' . "|" . 
					'$product_description' . "|" . 
					'' . "|" . 
					'$shipping_time'."|" . 
					''."|" . 
					'' . "|" . 
					'$manufacturer' . "|" . 
					'' . "|\n";
	}
	
	function check()
	{
		if (!isset($this->_check))
		{
			$check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PREISROBOTER_SEF_URL_STATUS'");
			$this->_check = xtc_db_num_rows($check_query);
		}
		return $this->_check;
	}

	function install()
	{
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PREISROBOTER_SEF_URL_FILE', 'preisroboter_sef_urls.txt',  '6', '1', '', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PREISROBOTER_SEF_URL_STATUS', 'True',  '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
	}
	
	function keys()
	{
		return array('MODULE_PREISROBOTER_SEF_URL_STATUS','MODULE_PREISROBOTER_SEF_URL_FILE');
	}
}
?>