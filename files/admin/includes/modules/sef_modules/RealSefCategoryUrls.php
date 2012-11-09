<?php 
/**
::Header::
 */

require_once 'RealSefProductUrls.php';
require_once DIR_FS_CATALOG.'inc/xtc_category_link.inc.php';

class RealSefCategoryUrls extends RealSefProductUrls
{
	protected $categories;
	
	public function __construct()
	{
		
	}

	public function getTitle()
	{
		return SEF_MODULES_REAL_SEF_CATEGORY_URLS_TITLE;
	}
	
	public function getDescription()
	{
		return SEF_MODULES_REAL_SEF_CATEGORY_URLS_DESC;
	}
	
	public function updateUrls()
	{
		$categories = $this->buildCategoriesTree();
		$versions = $this->getCurrentVersionOfAllEntries();
		foreach($categories as $id => $catData)
		{
			foreach($catData as $lanuageId => $data)
			{
				$moduleEntryId = 'category_'.$id;
				$urlAlias = $data['path'];
				
				$params = xtc_category_link($id, $data['categories_name']);
				
				if($versions[ $moduleEntryId ][ $lanuageId ] == 0)
					$version = 1;
				else
					$version = $versions[ $moduleEntryId ][ $lanuageId ]+1;
				$this->addUrl(
						'index.php', 
						$params,
						$lanuageId,
						$urlAlias,
						true,
						$moduleEntryId,
						$version
				);
			}
		}
	}
	
	public function install()
	{
		InsertConfiguration('SEF_MODULES_REAL_SEF_CATEGORY_URLS_ACTIVE', 'True', 6, 1, 'xtc_cfg_select_option(array(\'True\', \'False\'), ');
		
		// add the urls
		$this->updateUrls();
	}
	
	public function getConfigurationKeys()
	{
		return array('SEF_MODULES_REAL_SEF_CATEGORY_URLS_ACTIVE');
	}
	
	
	public function uninstall()
	{
		xtc_db_query('DELETE FROM '.TABLE_CONFIGURATION." WHERE configuration_key IN ('".implode("', '", $this->getConfigurationKeys())."')");
		
		xtc_db_query('DELETE FROM ' . TABLE_CW_SEF_PAGES.' WHERE url_module = \''.get_class($this).'\'');
		
	}
	
	public function isInstalled()
	{
		return defined(SEF_MODULES_REAL_SEF_CATEGORY_URLS_ACTIVE) ? true : false;
	}
	
	public function isActive()
	{
		return defined(SEF_MODULES_REAL_SEF_CATEGORY_URLS_ACTIVE) && SEF_MODULES_REAL_SEF_CATEGORY_URLS_ACTIVE == 'True' ? true : false;
	}
}
