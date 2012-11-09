<?PHP
/**
::Header::
 */



switch($_GET['action'])
{
	case 'install':
		$class = $_GET['module'];
		require_once PATH_TO_MODULES.$class.'.php';
		$object = new $class();
		$object->install();
		header('Location: ' . xtc_href_link(FILENAME_SEF_URLS, 'file='.$_GET['file'] . '&module=' . $class) );
		break;
	case 'uninstall':
		$class = $_GET['module'];
		require_once PATH_TO_MODULES.$class.'.php';
		$object = new $class();
		$object->uninstall();
		header('Location: ' . xtc_href_link(FILENAME_SEF_URLS, 'file='.$_GET['file'] . '&module=' . $class) );
		break;
	case 'save':
		$class = $_GET['module'];
		while (list($key, $value) = each($_POST['configuration']))
		{
			xtc_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . $value . "' where configuration_key = '" . $key . "'");
		}
		header('Location: ' . xtc_href_link(FILENAME_SEF_URLS, 'file='.$_GET['file'] . '&module=' . $class) );
		break;
}



?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>"> 
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/modules/sef_urls/sef_urls_stylesheet.css">
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

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
                <td>
                	<?PHP
					require_once DIR_FS_ADMIN.DIR_WS_MODULES.'sef_urls/sef_urls_menu.php';
					?>
                    <table border="0" width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td valign="top">
                            	<table border="0" width="100%" cellspacing="0" cellpadding="2">
                                    <tr class="dataTableHeadingRow">
                                        <td colspan="2" class="dataTableHeadingContent"><?php echo TABLE_HEADING_MODULES; ?></td>
                                        <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_FILENAME; ?></td>
                                        <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
                                    </tr>
<?PHP
// List Modules:
$modules = glob(PATH_TO_MODULES.'*.php');
foreach($modules as $module)
{
	require_once $module;
	$file =  basename($module);
	$class = str_replace('.php', '', $file);
	$object = new $class();
	
	// Require lang file:
	require_once DIR_FS_LANGUAGES.$_SESSION['language'].'/admin/includes/modules/sef_modules/'.$file;
	
	if(!isset($_GET['module']))
		$_GET['module'] = $class;
	
	if($_GET['module'] == $class)
	{
		echo '<tr class="dataTableRowSelected" onclick="document.location.href=\''. xtc_href_link(FILENAME_SEF_URLS, 'file='.$_GET['file'] . '&module=' . $class .'&action=edit') . '\'">';
			echo '<td class="dataTableContent" width="20px">';
				echo '<a href="'. xtc_href_link(FILENAME_SEF_URLS, 'file='.$_GET['file'] . '&module=' . $class .'&action=edit') . '">';
					echo xtc_image(DIR_WS_IMAGES . 'icons/preview.gif', IMAGE_ICON_INFO);
				echo '</a>';
			echo '</td>';
			echo '<td class="dataTableContent">' . $object->getTitle() . '</td>';
			echo '<td class="dataTableContent">' . $file . '</td>';
			echo '<td align="right" class="dataTableContent">';
				echo '<a href="'. xtc_href_link(FILENAME_SEF_URLS, 'file='.$_GET['file'] . '&module=' . $class .'&action=edit') . '">';
					echo xtc_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', IMAGE_ICON_INFO);
				echo '</a>';
			echo '</td>';
		echo '</tr>';
	}
	else
	{
		echo '<tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\''. xtc_href_link(FILENAME_SEF_URLS, 'file='.$_GET['file'] . '&module=' . $class ) . '\'">';
			echo '<td class="dataTableContent" width="20px">';
				echo '<a href="'. xtc_href_link(FILENAME_SEF_URLS, 'file='.$_GET['file'] . '&module=' . $class .'&action=edit') . '">';
					echo xtc_image(DIR_WS_IMAGES . 'icons/preview.gif', IMAGE_ICON_INFO);
				echo '</a>';
			echo '</td>';
			echo '<td class="dataTableContent">' . $object->getTitle() . '</td>';
			echo '<td class="dataTableContent">' . $file . '</td>';
			echo '<td align="right" class="dataTableContent">';
				echo '<a href="'. xtc_href_link(FILENAME_SEF_URLS, 'file='.$_GET['file'] . '&module=' . $class .'') . '">';
					echo xtc_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO);
				echo '</a>';
			echo '</td>';
		echo '</tr>';
	}
	
}
echo '<tr>';
	echo '<td colspan="3" class="smallText">';
		echo '<b>'.TABLE_FOOTER_DIRECTORY . '</b> ' . PATH_TO_MODULES;
	echo '</td>';
echo '</tr>';
echo '</table>';
echo '</td>';

$heading = array();
$contents = array();
if(isset($_GET['module']))
{
	$class = $_GET['module'];
	$object = new $class();
	if(!$object->isInstalled() && $_GET['action'] == 'edit')
		$_GET['action'] = '';
	
	switch ($_GET['action'])
	{
		case 'edit':
			$heading[] = array('text' => '<b>' . $object->getTitle() . '</b>');
			$arrKeys = $object->getConfigurationKeys();
			if(is_array($arrKeys))
			{
				foreach($arrKeys as $key)
				{
					$value = GetConfiguration($key);
					$keys .= '<b>' . constant($key.'_TITLE') . '</b><br />' . constant($key.'_DESC').'<br />';
					if ($value['set_function'])
					{
						eval('$keys .= ' . $value['set_function'] . "'" . $value['configuration_value'] . "', '" . $key . "');");
					}
					else
					{
						$keys .= xtc_draw_input_field('configuration[' . $key . ']', $value['configuration_value']);
					}
					$keys .= '<br /><br />';
				}
			}
			$keys = substr($keys, 0, strrpos($keys, '<br /><br />'));
			$contents = array('form' => xtc_draw_form('modules', FILENAME_SEF_URLS, 'file=' . $_GET['file'] . '&module=' . $_GET['module'] . '&action=save'));
			$contents[] = array('text' => $keys);
			$contents[] = array(
							'align' => 'center', 
							'text' => '<br />
									<input type="submit" class="button" value="' . BUTTON_UPDATE . '"/>
									<a class="button" href="' . xtc_href_link(FILENAME_SEF_URLS, 'file=' . $_GET['file'] . '&module=' . $_GET['module']) . '">' . BUTTON_CANCEL . '</a>
								');
			break;
		default:
			$heading[] = array('text' => '<b>' . $object->getTitle() . '</b>');
			if($object->isInstalled())
			{
				$arrKeys = $object->getConfigurationKeys();
				if(is_array($arrKeys))
				{
					foreach($arrKeys as $key)
					{
						$value = GetConfiguration($key);
						$keys .= '<b>' . constant($key.'_TITLE') . '</b><br />';
						if (!empty($value['use_function']))
						{
							$use_function = constant($key);
							if (ereg('->', $use_function))
							{
								$class_method = explode('->', $use_function);
								if (!is_object(${$class_method[0]}))
								{
									include(DIR_WS_CLASSES . $class_method[0] . '.php');
									${$class_method[0]} = new $class_method[0]();
								}
								$keys .= xtc_call_function($class_method[1], $value['configuration_value'], ${$class_method[0]});
							}
							else
							{
								$keys .= xtc_call_function($use_function, $value['value']);
							}
						}
						else
						{
							if(strlen($value['configuration_value']) > 30)
								$keys .=  substr($value['configuration_value'],0,30) . ' ...';
							else
								$keys .=  $value['configuration_value'];
						}
						$keys .= '<br /><br />';
					}
				}
				$keys = substr($keys, 0, strrpos($keys, '<br /><br />'));
				$contents[] = array(
								'align' => 'center', 
								'text' => '
										<a class="button" href="' . xtc_href_link(FILENAME_SEF_URLS, 'file='.$_GET['file'] . '&module=' . $_GET['module'] . '&action=uninstall') . '">' . BUTTON_MODULE_REMOVE . '</a>
										<a class="button" href="' . xtc_href_link(FILENAME_SEF_URLS, 'file='.$_GET['file'] . '&module=' . $_GET['module'] . '&action=edit') . '">' . BUTTON_EDIT . '</a>'
								);
				$contents[] = array('text' => $object->getDescription() );
				$contents[] = array('text' => '<br />' . $keys);
			}
			else
			{
				
				$contents[] = array(
							'align' => 'center', 
							'text' => '<a class="button" href="' . xtc_href_link(FILENAME_SEF_URLS, 'file='.$_GET['file'] . '&module=' . $_GET['module'] . '&action=install') . '">' . BUTTON_MODULE_INSTALL . '</a>'
						);
				$contents[] = array('text' => $object->getDescription() );
			}
			
			break;
	}
}

echo '<td width="25%" valign="top">';
	$box = new box;
	echo $box->infoBox($heading, $contents);
echo '</td>';

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
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>