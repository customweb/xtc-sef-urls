<?php
/**
::Header::
 */

require_once DIR_FS_ADMIN.'includes/modules/sef_urls/class.Export.php';

class BilligerSefUrl extends Export
{
	function BilligerSefUrl()
	{
		$this->code 		= 'BilligerSefUrl';
		$this->Export();
		$this->title 		= MODULE_BILLIGER_SEF_URL_TEXT_TITLE;
		$this->description 	= MODULE_BILLIGER_SEF_URL_TEXT_DESCRIPTION;
		$this->sort_order 	= MODULE_BILLIGER_SEF_URL_SORT_ORDER;
		$this->enabled 		= ((MODULE_BILLIGER_SEF_URL_STATUS == 'True') ? true : false);
		$this->schema 		= 'artikelid;herstelller;bezeichnung;kategorie;beschreibung_kurz;beschreibung_lang;bild_klein;deeplink;preis_val;product_ean';
		$this->varSchema	= 
					'$product_id'.";" . 
					'$manufacturer' . ";" . 
					'$product_name' . ";" . 
					'$categorieAsString'. ";".
					'$product_short_description' . ";" . 
					'$product_description' . ";" . 
					'$product_image_url' . ";" . 
					'$product_url' . ";" . 					
					'".number_format($price,2,\'.\',\'\')."' . ";" . 
					'$product_ean' . "\n";
	}
	
	function check()
	{
		if (!isset($this->_check))
		{
			$check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_BILLIGER_SEF_URL_STATUS'");
			$this->_check = xtc_db_num_rows($check_query);
		}
		return $this->_check;
	}

	function install()
	{
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_BILLIGER_SEF_URL_FILE', 'billiger_sef_urls.csv',  '6', '1', '', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_BILLIGER_SEF_URL_STATUS', 'True',  '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
	}
	
	function keys()
	{
		return array('MODULE_BILLIGER_SEF_URL_STATUS','MODULE_BILLIGER_SEF_URL_FILE');
	}
}
?>