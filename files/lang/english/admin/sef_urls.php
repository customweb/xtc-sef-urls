<?PHP
/**
::Header::
 */

// General:
define('HEADING_TITLE', 'SEF URL module');

# Filename:
define('SEF_URLS_FILE_ACTION_INSTALL', 'Install');
define('SEF_URLS_FILE_ACTION_MODULES', 'manage SEF URLs module');
define('SEF_URLS_FILE_ACTION_CONFIG', 'configuration');
define('SEF_URLS_FILE_ACTION_DEFAULT', 'main page');
define('SEF_URLS_FILE_ACTION_UNINSTALL', 'deinstall');
define('SEF_URLS_FILE_ACTION_MANUAL', 'add URLs manually');
define('SEF_URLS_ADMIN_DEMO_IS_ACTIVE', 'The SEF module is active');
define('SEF_URLS_ADMIN_DEMO_ACTIVATE', 'Switch to Demo Mode (module is only visible for the administrator)');
define('SEF_URLS_ADMIN_DEMO_IS_ACTIVE', 'SEF URLs demo is active!');
define('SEF_URLS_ADMIN_DEMO_DEACTIVATE', 'deactivate the demo mode.');
define('SEF_URLS_ADMIN_DEMO_IS_NOT_ACTIVE', 'The demo mode is not activated.');
define('SEF_URLS_ADMIN_REFRESH_ALL', 'renew all URLS.');



// Install:
define('HEADING_TITLE_INSTALL', 'Install');
define('TEXT_INSTALL_WARNING', 'Please, befor you install secure your database with you database client (phpMyAdmin). Afterwards you can start the installation.');
define('TEXT_UNINSTALL_WARNING', 'Are you sure to deinstall the module? All preferences will be lost');
define('SEF_URLS_BUTTON_INSTALL_START', 'start installation');



// Manual:
define('HEADING_TITLE_MANUAL', 'Add URLs manually');
define('TEXT_DISPLAY_NUMBER_OF_PAGES', ' Display: <b>%d</b> to <b>%d</b> (of total: <b>%d</b> URLs)');
define('SEF_URLS_DISPLAY_ALL_MODULES', 'Show all');
define('SEF_URLS_DISPLAY_ONLY_MANUAL', 'Show only manually added');
define('SEF_URL_MANUAL_FILTER_TEXT', 'Filter');
define('SEF_URLS_FILTER_MODULES_TITLE', 'Module:');
define('SEF_URLS_FILTER_LANGUAGES_TITLE', 'Language:');
define('SEF_URLS_MANUAL_LISTING_HEADER_URL', 'URL');
define('SEF_URLS_MANUAL_LISTING_HEADER_ALIAS', 'URL Alias / Replacement URL (Standard URL)');
define('SEF_URLS_MANUAL_NEW_ELEMENT', 'Add new URL');
define('BUTTON_NEW_URL', 'New URL');
define('SEF_URLS_MANUAL_EDIT_ALL_LANGUAGES', 'language independend');
define('SEF_URLS_MANUAL_EDIT_TEXT_DEFAULT', 'use URL:');
define('SEF_URLS_MANUAL_EDIT_TEXT_ALIAS', 'Alias URL:');
define('SEF_URLS_MANUAL_EDIT_TEXT_NORMAL_URL', 'native URL:');


// Config:
define('HEADING_TITLE_CONFIG', 'Configuration');
#Configuration Values:
define('SEF_URLS_ACTIVATE_TITLE', 'Activate the SEF Modul');
define('SEF_URLS_ACTIVATE_DESC', 'Are you sure to activate the SEF Module?');
define('SEF_URLS_MULTILINGUAL_TITLE', 'Multilanguage');
define('SEF_URLS_MULTILINGUAL_DESC', 'Do you want to use mulitlanguage. (http://www.IhrShop.ch/de/...)');
define('SEF_URLS_XTC_SEF_URLS_TITLE', 'xt:Commerce SEF URLS');
define('SEF_URLS_XTC_SEF_URLS_DESC', 'If you used the native xt:commere SEF Module set this option to true.');
define('SEF_URLS_GARBAGE_COLLECTOR_FACTOR_TITLE', 'Garbage Collector');
define('SEF_URLS_GARBAGE_COLLECTOR_FACTOR_DESC', ' The Garbage Collector will automatically update your URL Database. Here you can insert a number between 1 and 0, which sets how often the garbage collector will update your URL Database. If you dont want to use this function set the number to 0');



// Modules:
define('TABLE_HEADING_MODULES', 'Module');
define('TABLE_HEADING_FILENAME', 'Folder:');
define('TABLE_HEADING_ACTION', 'Action:');

define('TABLE_FOOTER_DIRECTORY', 'module folder:');


// new to v2.2.0
define('HEADING_TITLE_UPDATE', 'Update');
define('HEADING_TITLE_UNINSTALL', 'Deinstallation');
define('SEF_URLS_BUTTON_UPDATE_START', 'Start Update');
define('TEXT_UPDATE_WARNING', 'Please, befor you update secure your database with you database client (phpMyAdmin). Afterwards you can start the update.');
define('SEF_URLS_CURRENT_VERSION_TEXT', 'Current version:');
define('SEF_URLS_CACHE_ACTIVE_TITLE', 'Cache activate');
define('SEF_URLS_CACHE_ACTIVE_DESC', 'Do you want to activate the SEF URLs cache?');


?>