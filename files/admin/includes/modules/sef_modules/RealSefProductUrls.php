<?php 
/**
::Header::
 */

require_once PATH_TO_SEF_MAIN_DIR.'class.SefModule.php';
require_once DIR_FS_CATALOG.'inc/xtc_get_category_path.inc.php';
require_once DIR_FS_CATALOG.'inc/xtc_get_parent_categories.inc.php';

class RealSefProductUrls extends SefModule
{
	protected $categories;
	
	public function __construct()
	{
		
	}

	public function getTitle()
	{
		return SEF_MODULES_REAL_SEF_PRODUCT_URLS_TITLE;
	}
	
	public function getDescription()
	{
		return SEF_MODULES_REAL_SEF_PRODUCT_URLS_DESC;
	}
	
	public function updateUrls()
	{
		$categories = $this->buildCategoriesTree();
		$versions = $this->getCurrentVersionOfAllEntries();
		$result = xtc_db_query('SELECT
									p.products_id,
									products_name,
									language_id,
									products_last_modified,
									products_date_added,
									categories_id
								FROM 
									' . TABLE_PRODUCTS .' AS p,
									' . TABLE_PRODUCTS_DESCRIPTION . ' AS pd ,
									' . TABLE_PRODUCTS_TO_CATEGORIES . ' AS cat
								WHERE 
									p.products_id = pd.products_id 
								  AND
									p.products_id = cat.products_id
								  AND
									cat.categories_id != 0
								');
		
		while($row = xtc_db_fetch_array($result))
		{
			$params = xtc_product_link($row['products_id'], $row['products_name']);
			$urlAlias = $categories[$row['categories_id']][$row['language_id']]['path'] . $this->cleanUrl($row['products_name']).SEF_MODULES_REAL_SEF_PRODUCT_URLS_FILE_EXT;
			
			$moduleEntryId = 'product_'.$row['products_id'];
			
			if($versions[ $moduleEntryId ][ $row['language_id'] ] == 0)
				$version = 1;
			else
				$version = $versions[ $moduleEntryId ][ $row['language_id'] ]+1;

			$this->addUrl(
					FILENAME_PRODUCT_INFO, 
					$params,
					$row['language_id'],
					$urlAlias,
					true,
					$moduleEntryId,
					$version
				);
			
		}
	}
	
	public function install()
	{
		InsertConfiguration('SEF_MODULES_REAL_SEF_PRODUCT_URLS_ACTIVE', 'True', 6, 1, 'xtc_cfg_select_option(array(\'True\', \'False\'), ');
		InsertConfiguration('SEF_MODULES_REAL_SEF_PRODUCT_URLS_FILE_EXT', '.html', 6, 2, '');
		
		// add the urls
		$this->updateUrls();
	}
	
	public function getConfigurationKeys()
	{
		return array('SEF_MODULES_REAL_SEF_PRODUCT_URLS_ACTIVE', 'SEF_MODULES_REAL_SEF_PRODUCT_URLS_FILE_EXT');
	}
	
	
	public function uninstall()
	{
		xtc_db_query('DELETE FROM '.TABLE_CONFIGURATION." WHERE configuration_key IN ('".implode("', '", $this->getConfigurationKeys())."')");
		
		xtc_db_query('DELETE FROM ' . TABLE_CW_SEF_PAGES.' WHERE url_module = \''.get_class($this).'\'');
	}
	
	public function isInstalled()
	{
		return defined(SEF_MODULES_REAL_SEF_PRODUCT_URLS_ACTIVE) ? true : false;
	}
	
	public function isActive()
	{
		return defined(SEF_MODULES_REAL_SEF_PRODUCT_URLS_ACTIVE) && SEF_MODULES_REAL_SEF_PRODUCT_URLS_ACTIVE == 'True' ? true : false;
	}
	
	protected function buildCategoriesTree()
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
	
	protected function getCatPath($id, $langId)
	{
		if($this->categories[$id][$langId]['parent_id'] == 0)
			return $this->cleanUrl($this->categories[$id][$langId]['categories_name']). '/';
		else
			return $this->getCatPath($this->categories[$id][$langId]['parent_id'], $langId) . $this->cleanUrl($this->categories[$id][$langId]['categories_name']) . '/';
	}
}
