<?php
/**
::Header::
 */

require_once DIR_FS_ADMIN.'includes/modules/sef_urls/class.Export.php';

class GoogleBaseSefUrl extends Export
{
	function GoogleBaseSefUrl()
	{
		$this->code 		= 'GoogleBaseSefUrl';
		$this->Export();
		$this->title 		= MODULE_FROOGLE_SEF_URL_TEXT_TITLE;
		$this->description 	= MODULE_FROOGLE_SEF_URL_TEXT_DESCRIPTION;
		$this->sort_order 	= MODULE_FROOGLE_SEF_URL_SORT_ORDER;
		$this->enabled 		= ((MODULE_FROOGLE_SEF_URL_STATUS == 'True') ? true : false);
		$this->schema 		= 'link'."\t".'id'."\t".'titel'."\t".'beschreibung'. "\t".'bild_url'."\t".'preis'."\t".'marke'."\t".'ean'."\t".'gewicht'."\t".'zustand'."\n" ;
		$this->varSchema	= '$product_url'."\t" .'$product_id'."\t" .'$product_name'."\t" .'$product_description'."\t" .'$product_image_url'."\t" .'$price'."\t" .'$manufacturer'."\t" .'$product_ean'."\t" .'$product_weight'."\t" .'neu'."\n";
	}
	
	function check()
	{
		if (!isset($this->_check))
		{
			$check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_FROOGLE_SEF_URL_STATUS'");
			$this->_check = xtc_db_num_rows($check_query);
		}
		return $this->_check;
	}

	function install()
	{
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_FROOGLE_SEF_URL_FILE', 'froogle_sef_urls.txt',  '6', '1', '', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_FROOGLE_SEF_URL_STATUS', 'True',  '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
	}
	
	function keys()
	{
		return array('MODULE_FROOGLE_SEF_URL_STATUS','MODULE_FROOGLE_SEF_URL_FILE');
	}
}
?>