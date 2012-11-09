<?PHP
/**
::Header::
 */


require('includes/application_top.php');
require_once DIR_FS_DOCUMENT_ROOT.'includes/filenames.php';

define('PATH_TO_MODULES', DIR_FS_ADMIN.DIR_WS_MODULES.'sef_modules/');
define('SEF_URLS_CURRENT_VERSION', '2.2.2');
define('CONFIGURATION_GROUP_ID', 100);

// Files:
define('FILENAME_SEF_URLS', 'sef_urls.php');

// Tables:
define('TABLE_CW_SEF_PAGES', 'cw_sef_pages');
define('TABLE_CW_SEF_PARAMETERS', 'cw_sef_parameters');
define('PATH_TO_SEF_MAIN_DIR', DIR_WS_MODULES.'sef_urls/');

// Some Functions:
require_once DIR_FS_ADMIN.PATH_TO_SEF_MAIN_DIR.'sef_urls_functions.php';



switch($_GET['file'])
{
	case 'modules':
		require_once DIR_FS_ADMIN.DIR_WS_MODULES.'sef_urls/sef_urls_modules.php';
		break;
	case 'config':
		require_once DIR_FS_ADMIN.DIR_WS_MODULES.'sef_urls/sef_urls_config.php';
		break;
	case 'manual':
		require_once DIR_FS_ADMIN.DIR_WS_MODULES.'sef_urls/sef_urls_manual.php';
		break;
	case 'install':
		require_once DIR_FS_ADMIN.DIR_WS_MODULES.'sef_urls/sef_urls_install.php';
		break;
	case 'uninstall':
		require_once DIR_FS_ADMIN.DIR_WS_MODULES.'sef_urls/sef_urls_install.php';
		break;
	default:
		if(defined('SEF_URLS_INSTALLED_VERSION') && SEF_URLS_CURRENT_VERSION == SEF_URLS_INSTALLED_VERSION)
			require_once DIR_FS_ADMIN.DIR_WS_MODULES.'sef_urls/sef_urls_modules.php';
		else
			require_once DIR_FS_ADMIN.DIR_WS_MODULES.'sef_urls/sef_urls_install.php';
		break;
}




require(DIR_WS_INCLUDES . 'application_bottom.php');



?>