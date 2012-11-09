<?PHP
/**
::Header::
 */

// For Admin demo:
if(isset($_GET['demo']) && $_GET['demo'] == '1')
{
	$_SESSION['sef_urls_activate_temporary'] = true;
}
elseif(isset($_GET['demo']) && $_GET['demo'] == '0')
{
	$_SESSION['sef_urls_activate_temporary'] = false;
}
if(isset($_GET['refresh']) && $_GET['refresh'] == '1')
{
	refreshAllUrls();
}

// Menu:
echo '<ul class="tab">';
if(defined('SEF_URLS_INSTALLED_VERSION') && SEF_URLS_CURRENT_VERSION == SEF_URLS_INSTALLED_VERSION)
{
	echo '<li ';
		if($_GET['file'] == 'modules' or !isset($_GET['file']))
			echo ' class="currentTab"';
		echo '><a href="'. xtc_href_link(FILENAME_SEF_URLS, 'file=modules') . '">' . SEF_URLS_FILE_ACTION_MODULES . '</a>';
	echo '</li>';
	
	echo '<li ';
		if($_GET['file'] == 'manual')
			echo ' class="currentTab"';
		echo '><a href="'. xtc_href_link(FILENAME_SEF_URLS, 'file=manual') . '">' . SEF_URLS_FILE_ACTION_MANUAL . '</a>';
	echo '</li>';
	
	echo '<li ';
		if($_GET['file'] == 'config')
			echo ' class="currentTab"';
		echo '><a href="'. xtc_href_link(FILENAME_SEF_URLS, 'file=config') . '">' . SEF_URLS_FILE_ACTION_CONFIG . '</a>';
	echo '</li>';
	
	/*echo '<li ';
		if($_GET['file'] == '' or !isset($_GET['file']) )
			echo ' class="currentTab"';
		echo '><a href="'. xtc_href_link(FILENAME_SEF_URLS, '') . '">' . SEF_URLS_FILE_ACTION_DEFAULT . '</a>';
	echo '</li>';*/
	
	echo '<li ';
		if($_GET['file'] == 'uninstall' )
			echo ' class="currentTab"';
		echo '><a href="'. xtc_href_link(FILENAME_SEF_URLS, 'file=uninstall') . '">' . SEF_URLS_FILE_ACTION_UNINSTALL . '</a>';
	echo '</li>';
	
	echo '<li style="text-align:right; margin-left: 30px; border:none; background:none;" >';
		if($_SESSION['sef_urls_activate_temporary'] == true)
		{
			echo xtc_image(DIR_WS_IMAGES . 'icon_status_green.gif', SEF_URLS_ADMIN_DEMO_IS_ACTIVE);
			echo '&nbsp;&nbsp;';
			echo '<a href="' . xtc_href_link(FILENAME_SEF_URLS, 'file='.$_GET['file'] . '&demo=0').'">';
			echo xtc_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', SEF_URLS_ADMIN_DEMO_DEACTIVATE);
			echo '</a>';
		}
		else
		{
			echo '<a href="' . xtc_href_link(FILENAME_SEF_URLS, 'file='.$_GET['file'] . '&demo=1').'">';
			echo  xtc_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', SEF_URLS_ADMIN_DEMO_ACTIVATE);
			echo '</a>';
			echo '&nbsp;&nbsp;';
			echo  xtc_image(DIR_WS_IMAGES . 'icon_status_red.gif', SEF_URLS_ADMIN_DEMO_IS_NOT_ACTIVE);
		}
	echo '</li>';
	
	echo '<li style="text-align:right; margin-left: 30px; border:none; background:none;" >';
		echo '<a href="' . xtc_href_link(FILENAME_SEF_URLS, 'file='.$_GET['file'] . '&refresh=1').'">';
		echo  xtc_image(PATH_TO_SEF_MAIN_DIR. 'images/refresh.png', SEF_URLS_ADMIN_REFRESH_ALL);
		echo '</a>';
	echo '</li>';
	
	echo '<li style="text-align:right; margin-left: 30px; border:none; background:none; font-size: 10px; font-family:Arial;" >';
		echo SEF_URLS_CURRENT_VERSION_TEXT . ' ' . SEF_URLS_INSTALLED_VERSION;
	echo '</li>';
}
else
{
	echo '<li ';
		if($_GET['file'] == 'install' or !isset($_GET['file']) )
			echo ' class="currentTab"';
		echo '><a href="'. xtc_href_link(FILENAME_SEF_URLS, 'file=install') . '">' . SEF_URLS_FILE_ACTION_INSTALL . '</a>';
	echo '</li>';
}
	
echo '</ul>';
	
	
		

?>