<?PHP
/**
::Header::
 */

switch($_GET['install'])
{
	case 'new':
		InsertConfiguration('SEF_URLS_INSTALLED_VERSION', SEF_URLS_CURRENT_VERSION);
	
		InsertConfiguration('SEF_URLS_ACTIVATE', 'False', CONFIGURATION_GROUP_ID, 10, 'xtc_cfg_select_option(array(\'True\', \'False\'), ');
		InsertConfiguration('SEF_URLS_MULTILINGUAL', 'True', CONFIGURATION_GROUP_ID, 15, 'xtc_cfg_select_option(array(\'True\', \'False\'), ');
		InsertConfiguration('SEF_URLS_GARBAGE_COLLECTOR_FACTOR', '0.001', CONFIGURATION_GROUP_ID, 25, '');
		InsertConfiguration('SEF_URLS_CACHE_ACTIVE', 'False', CONFIGURATION_GROUP_ID, 12, 'xtc_cfg_select_option(array(\'True\', \'False\'), ');
		
		// Set the old xtc Sef URLs to false and safe it in the new value
		SEARCH_ENGINE_FRIENDLY_URLS == 'true' ? $xtcSefUrls = 'True' : $xtcSefUrls = 'False';
		InsertConfiguration('SEF_URLS_XTC_SEF_URLS', $xtcSefUrls, CONFIGURATION_GROUP_ID, 20, 'xtc_cfg_select_option(array(\'True\', \'False\'), ');
		UpdateConfiguration('SEARCH_ENGINE_FRIENDLY_URLS', 'false');
		
		// Add mysql tables:
		$sql = "DROP TABLE IF EXISTS cw_sef_pages;";
		xtc_db_query($sql);
		$sql = "
			CREATE TABLE `cw_sef_pages` (
			  `url_id` int(10) NOT NULL auto_increment,
			  `page_file` text NOT NULL,
			  `page_id` int(10) NOT NULL,
			  `url_alias` varchar(255) default NULL,
			  `url_default` smallint(1) NOT NULL default '0',
			  `url_module` varchar(100) default NULL,
			  `url_module_entry_id` varchar(255) NOT NULL default '0',
			  `url_module_version` int(5) default NULL,
			  `languages_id` int(10) default NULL,
			  `last_mod` datetime NOT NULL default '1970-01-01 00:00:00',
			  PRIMARY KEY  (`url_id`),
			  UNIQUE KEY `url_alias` (`url_alias`,`languages_id`),
			  KEY `site_id` (`page_id`),
			  KEY `url_module_entry_id` (`url_module_entry_id`),
			  KEY `url_module` (`url_module`)
			) TYPE=MyISAM";
		xtc_db_query($sql);

		$sql = "DROP TABLE IF EXISTS  cw_sef_parameters;";
		xtc_db_query($sql);
		$sql = "
			CREATE TABLE `cw_sef_parameters` (
			  `parameter_id` int(11) NOT NULL auto_increment,
			  `page_id` int(11) NOT NULL,
			  `parameter` text NOT NULL,
			  `languages_id` int(10) default NULL,
			  PRIMARY KEY  (`parameter_id`),
			  KEY `sites_id` (`page_id`),
			  FULLTEXT KEY `parameters` (`parameter`)
			) TYPE=MyISAM";
		xtc_db_query($sql);
		
		// For cache mechanism:
		$sql = "DROP TABLE IF EXISTS  cw_sef_urls_cache;";
		xtc_db_query($sql);
		$sql = "CREATE TABLE `cw_sef_urls_cache` (
				  `cache_id` int(11) NOT NULL auto_increment,
				  `site_url` varchar(255) NOT NULL,
				  `cache` text NOT NULL,
				  PRIMARY KEY  (`cache_id`),
				  UNIQUE KEY `site_url` (`site_url`)
				) TYPE=MyISAM";
		xtc_db_query($sql);
		
		header('Location: ' . FILENAME_SEF_URLS);
		break;
		
	case 'update_to_2.2.1':
		
		// For cache mechanism:
		$sql = "DROP TABLE IF EXISTS  cw_sef_urls_cache;";
		xtc_db_query($sql);
		$sql = "CREATE TABLE `cw_sef_urls_cache` (
				  `cache_id` int(11) NOT NULL auto_increment,
				  `site_url` varchar(255) NOT NULL,
				  `cache` text NOT NULL,
				  PRIMARY KEY  (`cache_id`),
				  UNIQUE KEY `site_url` (`site_url`)
				) TYPE=MyISAM";
		xtc_db_query($sql);
		
		// For modules:
		if(!defined(SEF_MODULES_REAL_SEF_CONTENT_URLS_FILE_EXT))
			InsertConfiguration('SEF_MODULES_REAL_SEF_CONTENT_URLS_FILE_EXT', '.html', 6, 2, '');
			
		if(!defined(SEF_MODULES_REAL_SEF_PRODUCT_URLS_FILE_EXT))
			InsertConfiguration('SEF_MODULES_REAL_SEF_PRODUCT_URLS_FILE_EXT', '.html', 6, 2, '');
			
		if(!defined(SEF_URLS_CACHE_ACTIVE))
			InsertConfiguration('SEF_URLS_CACHE_ACTIVE', 'False', CONFIGURATION_GROUP_ID, 12, 'xtc_cfg_select_option(array(\'True\', \'False\'), ');
		
		
		UpdateConfiguration('SEF_URLS_INSTALLED_VERSION', '2.2.2');
	
		header('Location: ' . FILENAME_SEF_URLS);
		
		break;
		
	case 'uninstall':
		DeleteConfiguration('SEF_URLS_INSTALLED_VERSION');
	
		DeleteConfiguration('SEF_URLS_ACTIVATE');
		DeleteConfiguration('SEF_URLS_MULTILINGUAL');
		DeleteConfiguration('SEF_URLS_GARBAGE_COLLECTOR_FACTOR');
		DeleteConfiguration('SEF_URLS_XTC_SEF_URLS');
		
		header('Location: ' . FILENAME_SEF_URLS);
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


if($_GET['file'] == 'uninstall')
{
	echo '<h1>'. HEADING_TITLE_UNINSTALL. '</h1>';
	echo TEXT_UNINSTALL_WARNING;
	echo '<br />';
	
	echo '<a class="button" href="' . xtc_href_link(FILENAME_SEF_URLS, 'file=install&install=uninstall') . '">' . SEF_URLS_FILE_ACTION_UNINSTALL . '</a>';
}
elseif(defined(SEF_URLS_INSTALLED_VERSION) && SEF_URLS_CURRENT_VERSION != SEF_URLS_INSTALLED_VERSION)
{
	echo '<h1>'. HEADING_TITLE_UPDATE. '</h1>';
	echo TEXT_UPDATE_WARNING;
	echo '<br />';
	
	echo '<a class="button" href="' . xtc_href_link(FILENAME_SEF_URLS, 'file=install&install=update_to_2.2.1') . '">' . SEF_URLS_BUTTON_UPDATE_START . '</a>';
}
else
{
	echo '<h1>'. HEADING_TITLE_INSTALL. '</h1>';
	echo TEXT_INSTALL_WARNING;
	echo '<br />';
	
	echo '<a class="button" href="' . xtc_href_link(FILENAME_SEF_URLS, 'file=install&install=new') . '">' . SEF_URLS_BUTTON_INSTALL_START . '</a>';
}

?>
                </td>
            </tr>
       </table>
   </td>

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