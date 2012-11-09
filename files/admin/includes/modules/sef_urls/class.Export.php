<?PHP
/**
::Header::
 */

defined( '_VALID_XTC' ) or die( 'Direct Access to this location is not allowed.' );

require_once DIR_FS_CATALOG.'inc/xtc_get_category_path.inc.php';
require_once DIR_FS_CATALOG.'inc/xtc_get_parent_categories.inc.php';


class Export
{
	var $title;
	var $code;
	var $description;
	var $enabled;
	var $CAT = array();
	var $PARENT = array();
	var $schema;
	var $varSchema;
	var $maxDescLength = 197;
	
	function Export()
	{
		//$this->code = get_class($this);
		// Require lang file:
		require_once DIR_FS_LANGUAGES.$_SESSION['language'].'/admin/includes/modules/export/' . $this->code . '.php';
		
	}
	
	function cleanUrl($name)
	{
		$search_array =  array('ä',  'Ä',  'ö',	 'Ö',  'ü',  'Ü',  '&auml;', '&Auml;', '&ouml;', '&Ouml;', '&uuml;', '&Uuml;', '&szlig;', 'ß',  'è', 'é', 'ê', 'à', 'â', 'á', 'É', 'È', 'Ê', 'Á', 'À', 'Â');
		$replace_array = array('ae', 'Ae', 'oe', 'Oe', 'ue', 'Ue', 'ae',     'Ae',     'oe',     'Oe',     'ue',     'Ue',     'ss',      'ss', 'e', 'e', 'e', 'a', 'a', 'a', 'E', 'E', 'E', 'A', 'A', 'A');
		$name = str_replace($search_array,$replace_array,$name);   	
		
		$replace_param = '/[^a-zA-Z0-9]/';
		$name = preg_replace($replace_param,'-',$name);    
		//$search = array('ä', 'ö', 'ü', 'è', 'à', 'é', 'ß', "'", ' ', '&');
		//$replace = array('ae', 'oe', 'ue', 'e', 'a', 'e', 'ss', '', '-', '-');
		//$url = str_ireplace($search, $replace, $url);
		
		return $name;
	}
	
	function process($file)
	{
		@xtc_set_time_limit(0);
		require_once DIR_FS_CATALOG.DIR_WS_CLASSES . 'xtcPrice.php';
		$xtPrice = new xtcPrice($_POST['currencies'],$_POST['status']);
		$arrCategories = $this->buildCategoriesTree();

		$groupcheck = '';
		if(GROUP_CHECK == 'true')
			$groupcheck = ' AND group_permission_'.$_POST['status'].' = 1 ';

		$export_query =xtc_db_query("SELECT
							p.products_id,
							pd.products_name,
							pd.products_description,
							pd.products_short_description,
							p.products_model,
							p.products_image,
							p.products_price,
							p.products_status,
							p.products_ean,
							p.products_weight,
							p.products_date_available,
							p.products_shippingtime,
							p.products_discount_allowed,
							pd.products_meta_keywords,
							p.products_tax_class_id,
							p.products_date_added,
							pd.language_id,
							m.manufacturers_name
						FROM
							" . TABLE_PRODUCTS . " p LEFT JOIN
							" . TABLE_MANUFACTURERS . " m
							ON p.manufacturers_id = m.manufacturers_id LEFT JOIN
							" . TABLE_PRODUCTS_DESCRIPTION . " pd
							ON p.products_id = pd.products_id AND
							pd.language_id = '".$_SESSION['languages_id']."' LEFT JOIN
							" . TABLE_SPECIALS . " s
							ON p.products_id = s.products_id
						WHERE
							p.products_status = 1
						   " . $groupcheck . "
						ORDER BY
							p.products_date_added DESC,
							pd.products_name");


		$output = $this->schema;
		while ($products = xtc_db_fetch_array($export_query))
		{
			$categorie_query = xtc_db_query("SELECT categories_id	FROM ".TABLE_PRODUCTS_TO_CATEGORIES." WHERE products_id = '".$products['products_id']."'");
			$categorie_data = xtc_db_fetch_array($categorie_query);
			$products['categories_id'] = $categorie_data['categories_id'];

			$price = $xtPrice->xtcGetPrice($products['products_id'], false, 1, $products['products_tax_class_id'], '');
		
			
			// remove trash
			$products_description = strip_tags($products['products_description']);         
			$products_description = str_replace(";",", ", $products_description);
			$products_description = str_replace("'",", ",$products_description);
			$products_description = str_replace("\n"," ",$products_description);
			$products_description = str_replace("\r"," ",$products_description);
			$products_description = str_replace("\t"," ",$products_description);
			$products_description = str_replace("\v"," ",$products_description);
			$products_description = str_replace("&quot,"," \"",$products_description);
			$products_description = str_replace("&qout,"," \"",$products_description);
			$products_description = str_replace(chr(13)," ",$products_description);
			
			$products_description = trim($products_description);
			if(strlen($products_description) > $this->maxDescLength)
				$products_description = substr($products_description, 0, $this->maxDescLength).'...';
		
			// remove trash
			$products_short_description = strip_tags($products['products_short_description']);         
			$products_short_description = str_replace(";",", ", $products_short_description);
			$products_short_description = str_replace("'",", ",$products_short_description);
			$products_short_description = str_replace("\n"," ",$products_short_description);
			$products_short_description = str_replace("\r"," ",$products_short_description);
			$products_short_description = str_replace("\t"," ",$products_short_description);
			$products_short_description = str_replace("\v"," ",$products_short_description);
			$products_short_description = str_replace("&quot,"," \"",$products_short_description);
			$products_short_description = str_replace("&qout,"," \"",$products_short_description);
			$products_short_description = str_replace(chr(13)," ",$products_short_description);
			
			$products_short_description = trim($products_short_description);
			if(strlen($products_short_description) > $this->maxDescLength)
				$products_short_description = substr($products_short_description, 0, $this->maxDescLength).'...';
			
			// Categorie as String
			$categorieAsString = $this->getCatPath($products['categories_id'], $products['language_id'], ' > ');
			$categorieAsString = substr($categorieAsString, 0, -3);
		
		
			if ($products['products_image'] != '')
				$products_image_url = HTTP_CATALOG_SERVER . DIR_WS_CATALOG_ORIGINAL_IMAGES .$products['products_image'];
			else
				$products_image_url = '';
		
			// create content ('$product_url'."\t" .'$product_name'."\t".'$product_description'."\t".'$product_image_url'."\t".'$category'."\t".'$price'."\t".'$manufacturer'. "\n";)
			if(!empty($_POST['campaign']))
				$camp = '?'.$_POST['campaign'];
			else
				$camp = '';
				
			if(SEF_URLS_MULTILINGUAL == 'False')
				$product_url = HTTP_CATALOG_SERVER . DIR_WS_CATALOG . $arrCategories[$products['categories_id']][$products['language_id']]['path'] . $this->cleanUrl($products['products_name']).'.html'.$camp;
			else
				$product_url = HTTP_CATALOG_SERVER . DIR_WS_CATALOG . $_SESSION['language_code'] . '/' . $arrCategories[$products['categories_id']][$products['language_id']]['path'] . $this->cleanUrl($products['products_name']).'.html'.$camp;

			$product_id = $products['products_id'];
			$product_name = $products['products_name'];
			
			$product_ean = $products['products_ean'];
			$product_weight = $products['products_weight'];
			
			$product_image_url = $products_image_url;
			$price = number_format($price,2,'.','');
			$shipping_time = xtc_get_shipping_status_name($products['products_shippingtime']);
			$product_description = $products_description;
			$category = $arrCategories[$products['categories_id']][$products['language_id']]['path'];
			$category = substr($category, 0, -1);
			$category = str_replace('/', ' > ', $category);
			$manufacturer = $products['manufacturers_name'];
			eval('$output .= "' . $this->varSchema . '";');
			
		
		}
		
		// create File
		$fp = fopen(DIR_FS_DOCUMENT_ROOT.'export/' . $file, "w+");
		fputs($fp, $output);
		fclose($fp);
		
		
		switch ($_POST['export'])
		{
			case 'yes':
				// send File to Browser
				$extension = substr($file, -3);
				$fp = fopen(DIR_FS_DOCUMENT_ROOT.'export/' . $file,"rb");
				$buffer = fread($fp, filesize(DIR_FS_DOCUMENT_ROOT.'export/' . $file));
				fclose($fp);
				header('Content-type: application/x-octet-stream');
				header('Content-disposition: attachment; filename=' . $file);
				echo $buffer;
				exit;
			
			break;
		}
		
	}
	
	function display()
	{
		$customers_statuses_array = xtc_get_customers_statuses();
		
		// build Currency Select
		$curr='';
		$currencies=xtc_db_query("SELECT code FROM ".TABLE_CURRENCIES);
		while ($currencies_data=xtc_db_fetch_array($currencies))
		{
			$curr.=xtc_draw_radio_field('currencies', $currencies_data['code'],true).$currencies_data['code'].'<br>';
		}
		
		$campaign_array = array(array('id' => '', 'text' => TEXT_NONE));
		$campaign_query = xtc_db_query("select campaigns_name, campaigns_refID from ".TABLE_CAMPAIGNS." order by campaigns_id");
		while ($campaign = xtc_db_fetch_array($campaign_query))
		{
			$campaign_array[] = array ('id' => 'refID='.$campaign['campaigns_refID'].'&', 'text' => $campaign['campaigns_name'],);
		}
		
		return array('text' =>  	EXPORT_STATUS_TYPE.'<br>'.
									EXPORT_STATUS.'<br>'.
									xtc_draw_pull_down_menu('status',$customers_statuses_array, '1').'<br>'.
									CURRENCY.'<br>'.
									CURRENCY_DESC.'<br>'.
									$curr.
									CAMPAIGNS.'<br>'.
									CAMPAIGNS_DESC.'<br>'.
									xtc_draw_pull_down_menu('campaign',$campaign_array).'<br>'.                               
									EXPORT_TYPE.'<br>'.
									EXPORT.'<br>'.
									xtc_draw_radio_field('export', 'no',false).EXPORT_NO.'<br>'.
									xtc_draw_radio_field('export', 'yes',true).EXPORT_YES.'<br>'.
									'<br>' . xtc_button(BUTTON_EXPORT) .
									xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_MODULE_EXPORT, 'set=' . $_GET['set'] . '&module=geizhals')));
		
	
	}
	
	function buildCategoriesTree()
	{
		$this->categories = array();
		$result = xtc_db_query('SELECT c.categories_id, language_id, categories_name, parent_id FROM `' . TABLE_CATEGORIES . '` AS c, `' . TABLE_CATEGORIES_DESCRIPTION . '` AS cd WHERE c.categories_id = cd.categories_id');
		while($row = xtc_db_fetch_array($result))
		{
			$this->categories[$row['categories_id']][$row['language_id']] = $row;
		}
		foreach($this->categories as $id => $data)
		{
			foreach($data as $languageId => $dataPerLang)
			{
				$this->categories[$id][$languageId]['path'] = $this->getCatPath($id, $languageId);
			}
		}
		return $this->categories;
	}
	
	function getCatPath($id, $langId, $seperator = '/')
	{
		if($this->categories[$id][$langId]['parent_id'] == 0)
			return $this->cleanUrl($this->categories[$id][$langId]['categories_name']). $seperator;
		else
			return $this->getCatPath($this->categories[$id][$langId]['parent_id'], $langId) . $this->cleanUrl($this->categories[$id][$langId]['categories_name']) . $seperator;
	}
	
	function remove()
	{
		xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
	}
}

?>