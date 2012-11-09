<?PHP
/**
::Header::
 */


if(isset($_POST['save']))
{
	/*
	if()
	{
		if($_GET['item'] == 'new'])
		{
			//$sql = 'INSERT INTO `' . TABLE_CW_SEF_PAGES . '` (page_file) VALUES
			
		}
	}*/
	$_POST['query_url'][0] = trim($_POST['query_url'][0]);
	foreach($_POST['query_url'] as $langId => $page_url)
	{
		// if language is independent then set no other languages
		if(!empty($_POST['query_url'][0]) && $langId != 0)
			continue;
		$splits = explode('?', $page_url);
		$languageData[$langId]['page_file'] = $splits[0];
		$languageData[$langId]['parameters'] = trim($splits[1]);
	}
	
	if(is_array($_POST['url']))
	{
		foreach($_POST['url'] as $id => $data)
		{
			if(isset($languageData[ $data['languages_id'] ]) && $data['languages_id'] != 0)
			{
				$page_file = $languageData[ $data['languages_id'] ]['page_file'];
				$languages_id = $data['languages_id'];
			}
			else
			{
				$page_file = $languageData[ 0 ]['page_file'];
				$languages_id = 'NULL';
			}
			
			if(!isset($_POST['url_add'][$id]))
			{
				xtc_db_query('
						UPDATE 
							`' . TABLE_CW_SEF_PAGES . '`
						SET 
							url_alias = \'' . $data['url_alias'] . '\',
							url_default = \'' . $data['url_default'] . '\',
							page_file = \'' . $page_file . '\',
							last_mod = NOW()
						WHERE 
							url_id = \'' . $id . '\'');
			}
			// new url: 
			elseif(!isset($_POST['url_delete'][$id]))
			{
				if($_GET['item'] == 'new')
				{
					$_GET['item'] = getAutoIncrement(TABLE_CW_SEF_PAGES, 'page_id');
				}
				xtc_db_query('
						INSERT INTO 
							`' . TABLE_CW_SEF_PAGES . '`
						(
							page_file,
							page_id,
							url_alias,
							url_default,
							languages_id,
							last_mod
						)
						VALUES
						(
							\'' . $page_file . '\',
							\'' . $_GET['item'] . '\',
							\'' . $data['url_alias'] . '\',
							\'' . $data['url_default'] . '\',
							' . $languages_id . ',
							NOW()
						) 
						');
			}
		}
	}
	
	// Update all old page_file && parameters
	if($_GET['item'] != 'new' && !empty($_POST['query_url'][0]))
	{
		xtc_db_query('
				UPDATE 
					`' . TABLE_CW_SEF_PAGES . '`
				SET 
					page_file = \'' . $languageData[0]['page_file'] . '\',
					last_mod = NOW()
				WHERE 
					page_id = \'' . $_GET['item'] . '\'');
				
		// Deletes all parameters 
		xtc_db_query('DELETE FROM `'.TABLE_CW_SEF_PARAMETERS.'` WHERE page_id = ' . $_GET['item']);

		if(!empty($languageData[0]['parameters']))
		{
			// Add the parameters again:
			addPrameters($languageData[0]['parameters'], $_GET['item'], 'NULL');
		}
		
	}
	elseif($_GET['item'] != 'new' && empty($_POST['query_url'][0]))
	{
		foreach($languageData as $languageId => $data)
		{
			if($languageId == 0)
				continue;
			xtc_db_query('
					UPDATE 
						`' . TABLE_CW_SEF_PAGES . '`
					SET 
						page_file = \'' . $languageData[$languageId]['page_file'] . '\',
						last_mod = NOW()
					WHERE 
						page_id = \'' . $_GET['item'] . '\'
					  AND
						languages_id = \'' . $languageId . '\'
						');
			
			// Deletes all parameters 
			xtc_db_query('DELETE FROM `'.TABLE_CW_SEF_PARAMETERS.'` WHERE page_id = ' . $_GET['item'] . ' AND languages_id = ' . $languageId . '');
	
			if(!empty($languageData[$languageId]['parameters']))
			{
				// Add the parameters again:
				addPrameters($languageData[$languageId]['parameters'], $_GET['item'], $languageId);
			}
		}
	}
	if(is_array($_POST['url_delete']))
	{
		foreach($_POST['url_delete'] as $id)
		{
			if(!isset($_POST['url_add'][$id]))
			{
				xtc_db_query('DELETE FROM `' . TABLE_CW_SEF_PAGES . '` WHERE url_id = \'' . $id . '\'');
			}
		}
	}
	header('Location: ' . xtc_href_link(FILENAME_SEF_URLS, xtc_get_all_get_params(array('refresh', 'action', 'item')) .'&item='.$_GET['item'] ) );
}


if(!isset($_POST['show_module']) && isset($_GET['show_module']))
	$_POST['show_module'] = $_GET['show_module'];
elseif(!isset($_POST['show_module']))
	$_POST['show_module'] = 'null';
	
switch($_POST['show_module'])
{
	case 'null':
		$search .= ' url_module IS NULL';
		$_GET['show_module'] = $_POST['show_module'];
		break;
	case 'all_modules':
		$_GET['show_module'] = $_POST['show_module'];
		break;
	default:
		$search .= ' url_module = \'' . $_POST['show_module'] . '\'';
		$_GET['show_module'] = $_POST['show_module'];
}

if(isset($_GET['show_language']) && !isset($_POST['show_language']))
	$_POST['show_language'] = $_GET['show_language'];
	


?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>"> 
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/modules/sef_urls/sef_urls_stylesheet.css" />
<script type="text/javascript" src="includes/modules/sef_urls/prototype.js"></script>
<script type="text/javascript" src="includes/modules/sef_urls/url_add.js"></script>
<style type="text/css">
.line{
	height: 25px;
}
</style>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
	<tr>
        <td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
            <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
            <!-- left_navigation //-->
            <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
            <!-- left_navigation_eof //-->
            </table>
        </td>
        <!-- body_text //-->
        <td class="boxCenter" width="100%" valign="top">
        	<table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
                <td width="100%">
                	<table border="0" width="100%" cellspacing="0" cellpadding="0">
                        <tr> 
                            <td width="80" rowspan="2"><?php echo xtc_image(DIR_WS_ICONS.'heading_modules.gif'); ?></td>
                            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
                        </tr>
                        <tr> 
                            <td class="main" valign="top">customweb GmbH</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="main">
<?PHP
require_once DIR_FS_ADMIN.DIR_WS_MODULES.'sef_urls/sef_urls_menu.php';


if($_GET['action'] == 'edit')
{
	
	// Start Listing:
	echo '<form action="' . xtc_href_link(FILENAME_SEF_URLS, xtc_get_all_get_params(array('refresh'))) . '" method="POST">';
	echo '<div style="display:hidden;" id="item_to_delete"></div>';
	echo '<div style="display:hidden;" id="item_to_add"></div>';

	$result = xtc_db_query('SELECT languages_id, code, name FROM ' . TABLE_LANGUAGES);
	$languageArray[0]['code'] = 'all';
	$languageArray[0]['name'] = SEF_URLS_MANUAL_EDIT_ALL_LANGUAGES;
	while($row = xtc_db_fetch_array($result))
	{
		$languageArray[$row['languages_id']]['code'] = $row['code'];
		$languageArray[$row['languages_id']]['name'] = $row['name'];
	}
	
	$data = array();
	if($_GET['item'] != 'new')
	{
		$sql = 'SELECT * FROM `' . TABLE_CW_SEF_PAGES . '` WHERE page_id = ' . $_GET['item'] . ' ORDER BY url_module_version AND url_default DESC';
		$urls_result = xtc_db_query($sql);
		while ($row = xtc_db_fetch_array($urls_result))
		{
			is_null($row['languages_id']) ? $row['languages_id'] = 0 : $row['languages_id'] = $row['languages_id'];
			$data[ $row['languages_id'] ][ $row['url_id'] ] = $row;
		}
	}
	
	if($_GET['item'] != 'new')
		$params = GetParametersByPageId($_GET['item']);
	
	$multiLang = true;
	foreach($languageArray as $languageId => $langData)
	{
		$value = '';
		if($_GET['item'] != 'new')
		{
			$sql = 'SELECT * FROM `' . TABLE_CW_SEF_PAGES . '` WHERE page_id = ' . $_GET['item'];
			if($languageId === 0)
				$sql .= ' AND languages_id IS NULL';
			else
				$sql .= ' AND languages_id = '.$languageId.'';
				
			$urls_result = xtc_db_query($sql);
			if($row = xtc_db_fetch_array($urls_result))
			{
				$query = BuildQuery($params[$languageId]);
				$value = $row['page_file'] . $query;
			}
		}
		$languageArray[$languageId]['input'] = '<input type=\"text\" name=\"query_url['.$languageId.']\" value=\"' . $value .'\" size=\"100\"/>';
	}
	
	// add next autoincrement to javascript
	$sql = 'SHOW TABLE STATUS LIKE \'' . TABLE_CW_SEF_PAGES . '\'';
	$autoincrement = xtc_db_query($sql);
	$row = xtc_db_fetch_array($autoincrement);
	echo '<div id="listing"></div>';
	echo '<script type="text/javascript">';

	echo 'var nextUrlId = '.$row['Auto_increment'] . ';';
	
	echo 'var langTextNormalUrl = \''. SEF_URLS_MANUAL_EDIT_TEXT_NORMAL_URL. '\';';
	echo 'var langTextAlias = \''. SEF_URLS_MANUAL_EDIT_TEXT_ALIAS. '\';';
	echo 'var langTextDefault = \''. SEF_URLS_MANUAL_EDIT_TEXT_DEFAULT. '\';';
	echo 'var langTextAction = \''.TABLE_HEADING_ACTION . '\';';
	
	$langIndex = 0;
	foreach($languageArray as $languageId => $langData)
	{
		echo 'urlData['.$langIndex.'] = new Object(); ';
		echo 'urlData['.$langIndex.']["languages_id"] = "'.$languageId.'"; ';
		echo 'urlData['.$langIndex.']["name"] = "'.$languageArray[$languageId]['name'].'"; ';
		echo 'urlData['.$langIndex.']["input"] = "'.$languageArray[$languageId]['input'].'"; ';
		echo 'urlData['.$langIndex.']["data"] = new Array(); ';
		$urlIndex = 0;
		if(is_array($data[$languageId]))
		{
			foreach($data[$languageId] as $urlData)
			{
				echo 'urlData['.$langIndex.']["data"]['.$urlIndex.'] = new Object(); ';
				echo 'urlData['.$langIndex.']["data"]['.$urlIndex.']["url_alias"] = \'' . $urlData['url_alias'] . '\'; ';
				echo 'urlData['.$langIndex.']["data"]['.$urlIndex.']["url_id"] = \'' . $urlData['url_id'] . '\'; ';
				echo 'urlData['.$langIndex.']["data"]['.$urlIndex.']["languages_id"] = \'' . $urlData['languages_id'] . '\'; ';
				echo 'urlData['.$langIndex.']["data"]['.$urlIndex.']["url_default"] = \'' . $urlData['url_default'] . '\'; ';
				echo 'urlData['.$langIndex.']["data"]['.$urlIndex.']["new_added"] = false; ';
				$urlIndex++;
			}
		}
		$langIndex++;
	}
	
	echo ' loadTable(); ';
	echo '</script>';
	echo '<br />';
	echo '<input type="submit" name="save" class="button" value="'.BUTTON_SAVE.'"> ';
	echo '<a class="button" href="'.xtc_href_link(FILENAME_SEF_URLS, xtc_get_all_get_params(array('refresh', 'action')) ).'">'.BUTTON_CANCEL.'</a>';
	echo '</form>';
}
else
{

	echo '<h2>' . SEF_URL_MANUAL_FILTER_TEXT . '</h2>';
	echo '<form action="' . xtc_href_link(FILENAME_SEF_URLS, xtc_get_all_get_params(array('refresh', 'action'))) . '" method="POST">';
	
	echo '<table>';
		echo '<tr>';
			echo '<td class="main"><b>'.SEF_URLS_FILTER_MODULES_TITLE.'</b></td>';
			echo '<td class="main"><b>'.SEF_URLS_FILTER_LANGUAGES_TITLE.'</b></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td>';
				echo '<select name="show_module" onChange="this.form.submit();">';
				echo '<option value="null"';
				if(empty($_POST['show_module']))
					echo 'selected="selected"';
				echo '>';
					echo SEF_URLS_DISPLAY_ONLY_MANUAL;
				echo '</option>';
				
				echo '<option value="all_modules"';
				if($_POST['show_module'] == 'all_modules')
					echo 'selected="selected"';
				echo '>';
				echo SEF_URLS_DISPLAY_ALL_MODULES;
				echo '</option>';
				$modules = glob(PATH_TO_MODULES.'*.php');
				foreach($modules as $module)
				{
					require_once $module;
					$file =  basename($module);
					$class = str_replace('.php', '', $file);
					$object = new $class();
					// Require lang file:
					require_once DIR_FS_LANGUAGES.$_SESSION['language'].'/admin/includes/modules/sef_modules/'.$class.'.php';
					if($object->isInstalled())
					{
						echo '<option value="' . $class . '"';
						if($_POST['show_module'] == $class)
							echo 'selected="selected"';
						echo '>';
						echo $object->getTitle();
						echo '</option>';
					}
				}
				
				echo '</select>';
				
			echo '</td>';
			echo '<td>';
				echo '<select name="show_language" onChange="this.form.submit();">';
				$result = xtc_db_query('SELECT languages_id, name FROM ' . TABLE_LANGUAGES . ' ORDER BY sort_order');
				while($row = xtc_db_fetch_array($result))
				{
					echo '<option value="' . $row['languages_id'] . '"';
					if($_POST['show_language'] == $row['languages_id'] or !isset($_POST['show_language']))
					{
						echo 'selected="selected"';
						$_POST['show_language'] = $row['languages_id'];
					}
					echo '>';
					echo $row['name'];
					echo '</option>';
				}
				echo '</select>';
			echo '</td>';
		echo '</tr>';
	echo '</table>';
	echo '</form>';
	
	echo '<table border="0" width="100%" cellspacing="0" cellpadding="0">';
	echo '<tr>';
	echo '<td valign="top">';
		// Start Listing:
		echo '<table border="0" width="100%" cellspacing="0" cellpadding="2">';
			echo '<tr class="dataTableHeadingRow">';
				echo '<td class="dataTableHeadingContent">' . SEF_URLS_MANUAL_LISTING_HEADER_URL . '</td>';
				echo '<td class="dataTableHeadingContent">' . SEF_URLS_MANUAL_LISTING_HEADER_ALIAS. '</td>';
				echo '<td class="dataTableHeadingContent" align="right">' . TABLE_HEADING_ACTION. '&nbsp;</td>';
			echo '</tr>';
	
	
	$_GET['show_language'] = $_POST['show_language'];
	if(!empty($search))
		$search = ' AND '. $search;
	$search = ' WHERE (languages_id = \'' . $_POST['show_language'] . '\' OR languages_id IS NULL)' .$search ;
	
	$sql = '
			SELECT
				*
			FROM
				`' . TABLE_CW_SEF_PAGES . '`
				' . $search . '
			GROUP BY 
				page_id
			' . $sort . '
		';
	
	$split = SplitPageResults($_GET['display_page'], '20', $sql, $query_numrows);
	$result = xtc_db_query($sql);
	
	while ($row = xtc_db_fetch_array($result))
	{
		$sql = 'SELECT * FROM `' . TABLE_CW_SEF_PAGES . '` WHERE page_id = ' . $row['page_id'] . ' AND (languages_id = \'' . $row['languages_id'] . '\' OR languages_id IS NULL) AND url_default = 1';
		$urls_result = xtc_db_query($sql);
		$arrUrls = array();
		while ($url = xtc_db_fetch_array($urls_result))
		{
			$arrUrls[ $url['url_id'] ] = $url;
		}
		
		$params = GetParametersByPageId($row['page_id']);
			
		if(!isset($_GET['item']))
			$_GET['item'] = $row['page_id'];
		
		if($_GET['item'] == $row['page_id'])
		{
			$sidebarTitle = $row['url_alias'];
			echo '<tr class="dataTableRowSelected" onclick="document.location.href=\''. xtc_href_link(FILENAME_SEF_URLS, xtc_get_all_get_params(array('item', 'refresh', 'action')) . '&item=' . $row['page_id'] .'&action=edit') . '\'">';
		}
		else
		{
			echo '<tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" 
					onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\''. xtc_href_link(FILENAME_SEF_URLS, xtc_get_all_get_params(array('item', 'refresh', 'action')) . '&item=' . $row['page_id']) . '\'"
				>';
		}
		
		echo '<td class="dataTableContent">';
			echo $row['page_file'] . BuildQuery($params[ $row['languages_id'] ]);
		echo '</td>';
		
		echo '<td class="dataTableContent">';
			foreach($arrUrls as $urlId => $data)
			{
				echo $data['url_alias'];
				echo '<br />';
			}
		echo '</td>';
		
		if($_GET['item'] == $row['page_id'])
		{
			echo '<td align="right" class="dataTableContent">';
				echo '<a href="'. xtc_href_link(FILENAME_SEF_URLS, xtc_get_all_get_params(array('item', 'refresh', 'action')) . '&item=' . $row['page_id'] . '&action=edit') . '">';
					echo xtc_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', IMAGE_ICON_INFO);
				echo '</a>';
			echo '</td>';
		}
		else
		{
			echo '<td align="right" class="dataTableContent">';
				echo '<a href="'. xtc_href_link(FILENAME_SEF_URLS, xtc_get_all_get_params(array('item', 'refresh', 'action')) . '&item=' . $row['page_id']) . '">';
					echo xtc_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO);
				echo '</a>';
			echo '</td>';
		}
		
		echo '</tr>';
		
		
	}
	
	// Footer:
	echo '<tr>';
		echo '<td colspan="4">';
		echo '<table border="0" width="100%" cellspacing="0" cellpadding="2">';
			echo '<tr>';
				echo '<td class="smallText" valign="top">'.SplitDisplayCount($query_numrows, '20', $_GET['display_page'], TEXT_DISPLAY_NUMBER_OF_PAGES) .'</td>';
				echo '<td class="smallText" align="right">'.SplitDisplayLinks($query_numrows, '20', MAX_DISPLAY_PAGE_LINKS, $_GET['display_page'], xtc_get_all_get_params(array('display_page', 'info', 'x', 'y', 'item')), 'display_page') .'</td>';
			echo '</tr>';
		echo '</table>';
		echo '</td>';
	echo '</tr>';
	// End Footer
	
	echo '</table>';
	// end Listing
	
	
	$heading = array();
	$contents = array();
	switch ($_GET['action'])
	{
		default:
			if(isset($_GET['item']))
			{	

				$sql = 'SELECT * FROM `' . TABLE_CW_SEF_PAGES . '` WHERE page_id = ' . $_GET['item'] . ' AND (languages_id = \'' . $_POST['show_language'] . '\' OR languages_id IS NULL) ORDER BY url_module_version AND url_default DESC';
				$urls_result = xtc_db_query($sql);
				$arrUrls = array();
				while ($url = xtc_db_fetch_array($urls_result))
				{
					$alias = $url['url_alias'];
					if(strlen($alias) > 40)
						$postFix = '...';
					
					$history .= '<b>' . $url['last_mod'] . ': </b><br />&nbsp;&nbsp;' . substr($alias, 0, 40) . $postFix . ' <br />';
				}
				$postFix = '';
				if(strlen($sidebarTitle) > 35)
					$postFix = '...';
				$heading[] = array('text' => '<b>' . substr($sidebarTitle, 0, 35) . $postFix .'</b>');
				$contents[] = array(
								'align' => 'center', 
								'text' => '<a class="button" href="' . xtc_href_link(FILENAME_SEF_URLS, xtc_get_all_get_params(array('item', 'refresh', 'action')) . '&item=' . $_GET['item'] . '&action=edit') . '">' . BUTTON_EDIT . '</a>'
								);
				$contents[] = array('text' => '<br />' . $history);
			}
			$contents[] = array(
							'align' => 'center', 
							'text' => '
								<div style="padding-top: 5px; font-weight: bold; width: 90%; border-top: 1px solid Black; margin-top: 5px;">' . SEF_URLS_MANUAL_NEW_ELEMENT . '</div>' . 
								'<a class="button" href="'.xtc_href_link(FILENAME_SEF_URLS, xtc_get_all_get_params(array('item', 'refresh', 'action')) . '&item=new&action=edit').'">'.BUTTON_NEW_URL.'</a>'
							);
			
			break;
	}
	
	echo '<td width="25%" valign="top">';
		$box = new box;
		echo $box->infoBox($heading, $contents);
	echo '</td>';
}


?>
          </tr>
        </table></td>
      </tr>
    </table></td>

<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br />
</body>
</html>
