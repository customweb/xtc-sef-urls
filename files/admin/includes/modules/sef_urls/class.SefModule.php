<?php
/**
::Header::
 */

abstract class SefModule
{
	abstract public function updateUrls();
	
	abstract public function getTitle();
	
	abstract public function getDescription();
	
	abstract public function getConfigurationKeys();
	
	abstract public function install();
	
	abstract public function uninstall();
	
	abstract public function isInstalled();
	
	protected function getCurrentVersionOfAllEntries()
	{
		$result = xtc_db_query('SELECT url_module_entry_id, url_module_version, languages_id, url_module FROM `' . TABLE_CW_SEF_PAGES . '` WHERE url_module = \'' . get_class($this) . '\'');
		$arrVersions = array(array());
		while($row = xtc_db_fetch_array($result))
		{
			$arrVersions[ $row['url_module_entry_id'] ][ $row['languages_id'] ] = $row['url_module_version'];
		}
		return $arrVersions;
	}
	
	protected function cleanUrl($name)
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
	
	protected function addUrl($pageFile, $parameters, $languageId, $alias, $default, $moduleEntryId, $version = 'NULL', $moduleForSearch = false)
	{
		$default == true ? $default = 1 : $default = 0;
		if($moduleForSearch === false)
		{
			$module = get_class($this);
			$moduleForSearch = get_class($this);
		}
		else
		{
			$module = get_class($this);
			$moduleForSearch = $moduleForSearch;
		}
	
		$result = xtc_db_query('SELECT url_id FROM `' . TABLE_CW_SEF_PAGES . '` WHERE url_alias = \'' . $alias . '\' AND languages_id = \'' . $languageId . '\'');
		if($row = xtc_db_fetch_array($result))
		{
			return $row['url_id'];
		}
		
		if($languageId == 'NULL')
			$sqlLang = ' languages_id IS NULL ';
		else
			$sqlLang = ' languages_id = \'' . $languageId . '\' ';
		
		$sql = '
				SELECT 
					page_id,
					url_module_version
				FROM
					`' . TABLE_CW_SEF_PAGES . '`
				WHERE 
					url_module = \''.$moduleForSearch.'\'
				  AND
					url_module_entry_id = \'' . $moduleEntryId . '\'
				  AND
					' . $sqlLang . '
				ORDER BY 
					url_module_version DESC
				';
		$result = xtc_db_query($sql);
		// if there is allready a url entry for this module_entry_id in the same language!
		if($row = xtc_db_fetch_array($result))
		{
			if($row['url_module_version'] == $version)
				$default = 0;
			else
				xtc_db_query('UPDATE ' .TABLE_CW_SEF_PAGES. ' SET url_default = 0 WHERE page_id = ' . $row['page_id'] . ' AND ' . $sqlLang . '');
			
			xtc_db_query('INSERT INTO ' .TABLE_CW_SEF_PAGES. ' 
							(
								page_file,
								page_id,
								url_alias,
								url_default,
								url_module,
								url_module_entry_id,
								url_module_version,
								languages_id,
								last_mod
							) 
						VALUES
							(
								\''.$pageFile.'\',
								\''.$row['page_id'].'\',
								\''.$alias.'\',
								\''.$default.'\',
								\''.$module.'\',
								\''.$moduleEntryId.'\',
								\''.$version.'\',
								'.$languageId.',
								NOW()
							)');
			
				xtc_db_query('DELETE FROM ' .TABLE_CW_SEF_PARAMETERS. ' WHERE page_id = ' . $row['page_id'] . ' AND ' . $sqlLang . '');
			$this->addPrameters($parameters, $row['page_id'], $languageId);
			
			return xtc_db_insert_id();
		}
		
		$sql = '
				SELECT 
					page_id
				FROM
					`' . TABLE_CW_SEF_PAGES . '`
				WHERE 
					url_module = \''.$moduleForSearch.'\'
				  AND
					url_module_entry_id = \'' . $moduleEntryId . '\'
				';
		$result = xtc_db_query($sql);
		// if there is allready a url entry for this module_entry_id in the other language!
		if($row = xtc_db_fetch_array($result))
		{
			$pageId = $row['page_id'];
			xtc_db_query('UPDATE ' .TABLE_CW_SEF_PAGES. ' SET url_default = 0 WHERE page_id = ' . $pageId . ' AND ' . $sqlLang . '');
			xtc_db_query('DELETE FROM ' .TABLE_CW_SEF_PARAMETERS. ' WHERE page_id = ' . $pageId . ' AND ' . $sqlLang . '');
			$this->addPrameters($parameters, $pageId, $languageId);
		}
		// there is no similar entry
		else
		{
			$pageId = getAutoIncrement(TABLE_CW_SEF_PAGES, 'page_id');
			$this->addPrameters($parameters, $pageId, $languageId);
		}
		xtc_db_query('INSERT INTO ' .TABLE_CW_SEF_PAGES. ' 
						(
							page_file,
							page_id,
							url_alias,
							url_default,
							url_module,
							url_module_entry_id,
							url_module_version,
							languages_id,
							last_mod
						) 
					VALUES
						(
							\''.$pageFile.'\',
							\''.$pageId.'\',
							\''.$alias.'\',
							\''.$default.'\',
							\''.$module.'\',
							\''.$moduleEntryId.'\',
							\''.$version.'\',
							\''.$languageId.'\',
							NOW()
						)');
		
		$urlId = xtc_db_insert_id();
				
		return $urlId;
	}
	
	protected function addPrameters($parameters, $pageId, $languageId)
	{
		// Parse Params:
		if(!is_array($parameters))
		{
			$params = explode('&', $parameters);
		}
		
		if(is_array($params))
		{
			foreach($params as $value)
			{
				xtc_db_query('INSERT INTO '.TABLE_CW_SEF_PARAMETERS . ' (page_id, parameter, languages_id) VALUES (\''.$pageId.'\', \''.$value.'\', '.$languageId.')');
			}
		}
		
	}
	
}
