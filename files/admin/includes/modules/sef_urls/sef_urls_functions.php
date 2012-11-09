<?PHP

function InsertConfiguration($key, $value, $group = '', $sort = '', $function = '', $use_function = '')
{
	$function = str_replace('\'', '\\\'', $function);
	$use_function = str_replace('\'', '\\\'', $use_function);
	xtc_db_query(
			'INSERT INTO
				' .TABLE_CONFIGURATION. '
			(
				configuration_id,
				configuration_key,
				configuration_value,
				configuration_group_id,
				sort_order,
				set_function,
				use_function,
				date_added
			) 
			VALUES
			(
				\'\',
				\''.$key.'\',
				\''.$value.'\',
				\''.$group.'\',
				\''.$sort.'\',
				\''.$function.'\',
				\''.$use_function.'\',
				now()
			)');
	define($key, $value);
}

function BuildQuery($parameters)
{
	if(count($parameters) > 0)
		return '?'.parseParametersFromArrayToString($parameters);
	else
		return '';
}

function SplitDisplayCount($query_numrows, $max_rows_per_page, $current_page_number, $text_output)
{
	$to_num = ($max_rows_per_page * $current_page_number);
	if ($to_num > $query_numrows)
		$to_num = $query_numrows;
	$from_num = ($max_rows_per_page * ($current_page_number - 1));
	
	if ($to_num == 0)
		$from_num = 0;
	else
		$from_num++;


	return sprintf($text_output, $from_num, $to_num, $query_numrows);
}

function SplitPageResults($current_page_number, $max_rows_per_page, &$sql_query, &$query_num_rows) {
	if (empty($current_page_number))
		$current_page_number = 1;
	$numResult = xtc_db_query($sql_query);
	$query_num_rows = xtc_db_num_rows($numResult);
	
	$num_pages = ceil($query_num_rows / $max_rows_per_page);
	if ($current_page_number > $num_pages) {
		$current_page_number = $num_pages;
	}
	$offset = ($max_rows_per_page * ($current_page_number - 1));
	if ($offset < 0) $offset=0; 
	$sql_query .= " limit " . $offset . ", " . $max_rows_per_page;
}


function SplitDisplayLinks($query_numrows, $max_rows_per_page, $max_page_links, $current_page_number, $parameters = '', $page_name = 'page')
{
	if ( xtc_not_null($parameters) && (substr($parameters, -1) != '&') )
		$parameters .= '&';
	
	// calculate number of pages needing links
	$num_pages = ceil($query_numrows / $max_rows_per_page);
	
	$pages_array = array();
	for ($i=1; $i<=$num_pages; $i++)
	{
		$pages_array[] = array('id' => $i, 'text' => $i);
	}
	
	if ($num_pages > 1)
	{
		$display_links = xtc_draw_form('pages', basename($_SERVER['PHP_SELF']), '', 'get');
	
		if ($current_page_number > 1)
		{
			$display_links .= '<a href="' . xtc_href_link(basename($_SERVER['PHP_SELF']), $parameters . $page_name . '=' . ($current_page_number - 1), 'NONSSL') . '" class="splitPageLink">' . PREVNEXT_BUTTON_PREV . '</a>&nbsp;&nbsp;';
		}
		else
		{
			$display_links .= PREVNEXT_BUTTON_PREV . '&nbsp;&nbsp;';
		}
	
		$display_links .= sprintf(TEXT_RESULT_PAGE, xtc_draw_pull_down_menu($page_name, $pages_array, $current_page_number, 'onChange="this.form.submit();"'), $num_pages);
	
		if (($current_page_number < $num_pages) && ($num_pages != 1))
		{
			$display_links .= '&nbsp;&nbsp;<a href="' . xtc_href_link(basename($_SERVER['PHP_SELF']), $parameters . $page_name . '=' . ($current_page_number + 1), 'NONSSL') . '" class="splitPageLink">' . PREVNEXT_BUTTON_NEXT . '</a>';
		}
		else
		{
			$display_links .= '&nbsp;&nbsp;' . PREVNEXT_BUTTON_NEXT;
		}
		
		if ($parameters != '')
		{
			if (substr($parameters, -1) == '&') $parameters = substr($parameters, 0, -1);
				$pairs = explode('&', $parameters);
			while (list(, $pair) = each($pairs))
			{
				list($key,$value) = explode('=', $pair);
				$display_links .= xtc_draw_hidden_field(rawurldecode($key), rawurldecode($value));
			}
		}
	
		if (SID)
			$display_links .= xtc_draw_hidden_field(session_name(), session_id());
		
		$display_links .= '</form>';
	}
	else
	{
		$display_links = sprintf(TEXT_RESULT_PAGE, $num_pages, $num_pages);
	}
	
	return $display_links;
}


function parseParametersFromArrayToString($arrParams)
{
	$arr = array();
	if(is_array($arrParams))
	{
		foreach ($arrParams as $key => $val)
		{
			$arr[] = urlencode($key)."=".urlencode($val);
		}
	}
	return implode($arr, "&");
}

function GetParametersByPageId($pageId)
{
	$arrParams = array();
	$result = xtc_db_query('SELECT * FROM ' .TABLE_CW_SEF_PARAMETERS. ' WHERE page_id = \'' . $pageId . '\'');
	while($param = xtc_db_fetch_array($result))
	{
		$splits = explode('=', $param['parameter']);
		if(is_null($param['languages_id']))
			$param['languages_id'] = 0;
		$arrParams[ $param['languages_id'] ][ $splits[0] ] = $splits[1];
	}
	
	return $arrParams;
}

function UpdateConfiguration($key, $value = false, $group = false, $sort = false, $function = false, $use_function = false)
{
	$sql =	'UPDATE
				' .TABLE_CONFIGURATION. '
			SET ';
	if($value !== false)
		$sql .= 'configuration_value = \'' . $value . '\',';
				
	if($group !== false)
		$sql .= 'configuration_group_id = \'' . $group . '\',';
				
	if($sort !== false)
		$sql .= 'sort_order = \'' . $sort . '\',';
				
	if($function !== false)
		$sql .= 'set_function = \'' . $function . '\',';
				
	if($use_function !== false)
		$sql .= 'use_function = \'' . $use_function . '\',';
		
	$sql = substr($sql, 0, -1);	
	
	$sql .= ' WHERE configuration_key = \'' . $key . '\'';			
	xtc_db_query($sql);
			
}

function DeleteConfiguration($key)
{
	xtc_db_query('DELETE FROM ' .TABLE_CONFIGURATION. ' WHERE configuration_key = \'' . $key . '\'');
}

function GetConfiguration($key)
{
	$result = xtc_db_query('SELECT * FROM ' .TABLE_CONFIGURATION. ' WHERE configuration_key = \'' . $key . '\'');
	return xtc_db_fetch_array($result);
}

function addPrameters($parameters, $pageId, $languageId)
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

function FindeUrl($module, $moduleEntryId, $languageId)
{
	$result = xtc_db_query('SELECT * FROM `' . TABLE_CW_SEF_PAGES . '` WHERE url_module = \'' . $module . '\' AND url_module_entry_id = \'' . $moduleEntryId . '\' AND languages_id = \'' . $languageId . '\'');
	return xtc_db_fetch_array($result);
}

function FindUrlByAlias($alias, $languageId)
{
	$result = xtc_db_query('SELECT * FROM `' . TABLE_CW_SEF_PAGES . '` WHERE url_alias = \'' . $alias . '\' AND languages_id = \'' . $languageId . '\'');
	return xtc_db_fetch_array($result);
}


function getAutoIncrement($table, $field)
{
	$result = xtc_db_query('SELECT max('. $field . ') AS max FROM ' . $table );
	$row = xtc_db_fetch_array($result);
	return $row['max']+1;
}

function refreshAllUrls()
{
	$modules = glob(PATH_TO_MODULES.'*.php');
	foreach($modules as $module)
	{
		require_once $module;
		$file =  basename($module);
		$class = str_replace('.php', '', $file);
		$object = new $class();
		if($object->isInstalled())
		{
			$object->updateUrls();
		}
	}

}

?>
