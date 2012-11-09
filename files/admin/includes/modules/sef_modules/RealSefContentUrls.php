<?php 
/**
::Header::
 */

require_once PATH_TO_SEF_MAIN_DIR.'class.SefModule.php';

class RealSefContentUrls extends SefModule
{
	protected $categories;
	
	public function __construct()
	{
		
	}

	public function getTitle()
	{
		return SEF_MODULES_REAL_SEF_CONTENT_URLS_TITLE;
	}
	
	public function getDescription()
	{
		return SEF_MODULES_REAL_SEF_CONTENT_URLS_DESC;
	}
	
	public function updateUrls()
	{
		$versions = $this->getCurrentVersionOfAllEntries();
		$result = xtc_db_query('SELECT
									content_title,
									languages_id,
									content_id,
									content_group
								FROM 
									' . TABLE_CONTENT_MANAGER .'
								');
		
		while($row = xtc_db_fetch_array($result))
		{
			// Normal Content:
			$params = 'coID='.$row['content_group'];
			$urlAlias = $this->cleanUrl($row['content_title']).SEF_MODULES_REAL_SEF_CONTENT_URLS_FILE_EXT;
			
			$moduleEntryId = 'content_'.$row['content_group'];
			
			if($versions[ $moduleEntryId ][ $row['languages_id'] ] == 0)
				$version = 1;
			else
				$version = $versions[ $moduleEntryId ][ $row['languages_id'] ]+1;

			$this->addUrl(
					FILENAME_CONTENT, 
					$params,
					$row['languages_id'],
					$urlAlias,
					true,
					$moduleEntryId,
					$version
				);
			
			// Popup Content:
			$params = 'coID='.$row['content_group'];
			$urlAlias = 'popup/'.$this->cleanUrl($row['content_title']).SEF_MODULES_REAL_SEF_CONTENT_URLS_FILE_EXT;
			
			$moduleEntryId = 'popup_content_'.$row['content_group'];
			
			if($versions[ $moduleEntryId ][ $row['languages_id'] ] == 0)
				$version = 1;
			else
				$version = $versions[ $moduleEntryId ][ $row['languages_id'] ]+1;

			$this->addUrl(
					FILENAME_POPUP_CONTENT, 
					$params,
					$row['languages_id'],
					$urlAlias,
					true,
					$moduleEntryId,
					$version
				);
			
			
		}
	}
	
	public function install()
	{
		InsertConfiguration('SEF_MODULES_REAL_SEF_CONTENT_URLS_ACTIVE', 'True', 6, 1, 'xtc_cfg_select_option(array(\'True\', \'False\'), ');
		InsertConfiguration('SEF_MODULES_REAL_SEF_CONTENT_URLS_FILE_EXT', '.html', 6, 2, '');
		
		// add the urls
		$this->updateUrls();
	}
	
	public function getConfigurationKeys()
	{
		return array('SEF_MODULES_REAL_SEF_CONTENT_URLS_ACTIVE', 'SEF_MODULES_REAL_SEF_CONTENT_URLS_FILE_EXT');
	}
	
	
	public function uninstall()
	{
		xtc_db_query('DELETE FROM '.TABLE_CONFIGURATION." WHERE configuration_key IN ('".implode("', '", $this->getConfigurationKeys())."')");
		
		xtc_db_query('DELETE FROM ' . TABLE_CW_SEF_PAGES.' WHERE url_module = \''.get_class($this).'\'');
	}
	
	public function isInstalled()
	{
		return defined(SEF_MODULES_REAL_SEF_CONTENT_URLS_ACTIVE) ? true : false;
	}
	
	public function isActive()
	{
		return defined(SEF_MODULES_REAL_SEF_CONTENT_URLS_ACTIVE) && SEF_MODULES_REAL_SEF_CONTENT_URLS_ACTIVE == 'True' ? true : false;
	}
	
}
