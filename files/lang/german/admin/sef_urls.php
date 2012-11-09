<?PHP
/**
::Header::
 */

// General:
define('HEADING_TITLE', 'SEF URL Module');

# Filename:
define('SEF_URLS_FILE_ACTION_INSTALL', 'Installieren');
define('SEF_URLS_FILE_ACTION_MODULES', 'SEF URLs Module verwalten');
define('SEF_URLS_FILE_ACTION_CONFIG', 'Konfiguration');
define('SEF_URLS_FILE_ACTION_DEFAULT', 'Hauptseite');
define('SEF_URLS_FILE_ACTION_UNINSTALL', 'Modul deinstallieren');
define('SEF_URLS_FILE_ACTION_MANUAL', 'Manuell URLs anlegen');
define('SEF_URLS_ADMIN_DEMO_IS_ACTIVE', 'SEF URLs Demo ist aktiv!');
define('SEF_URLS_ADMIN_DEMO_ACTIVATE', 'Aktivieren Sie die SEF URLs nur f&uuml;r den Admin. (Demo Modus)');
define('SEF_URLS_ADMIN_DEMO_IS_ACTIVE', 'SEF URLs Demo ist aktiv!');
define('SEF_URLS_ADMIN_DEMO_DEACTIVATE', 'Deaktivieren Sie die Demo.');
define('SEF_URLS_ADMIN_DEMO_IS_NOT_ACTIVE', 'Die Demo ist nicht aktiv.');
define('SEF_URLS_ADMIN_REFRESH_ALL', 'Alle URLs neu erneuern.');



// Install:
define('HEADING_TITLE_INSTALL', 'Installation');
define('TEXT_INSTALL_WARNING', 'Bevor Sie die Installation starten, sichern Sie die Datenbank. (zum Beispiel mit phpMyAdmin) Dananch starten Sie bitte die Installation.');
define('TEXT_UNINSTALL_WARNING', 'Sind Sie sicher, dass Sie das gesamte Modul deinstallieren m&ouml;chten? Dies Operation ist nicht wiederherstellbar!');
define('SEF_URLS_BUTTON_INSTALL_START', 'Installation starten');




// Manual:
define('HEADING_TITLE_MANUAL', 'Manuell URLs anlegen');
define('TEXT_DISPLAY_NUMBER_OF_PAGES', 'Angezeigt werden <b>%d</b> bis <b>%d</b> (von insgesamt <b>%d</b> URLs)');
define('SEF_URLS_DISPLAY_ALL_MODULES', 'Alle anzeigen');
define('SEF_URLS_DISPLAY_ONLY_MANUAL', 'Nur manuell angelegte');
define('SEF_URL_MANUAL_FILTER_TEXT', 'Filter');
define('SEF_URLS_FILTER_MODULES_TITLE', 'Module:');
define('SEF_URLS_FILTER_LANGUAGES_TITLE', 'Sprachen:');
define('SEF_URLS_MANUAL_LISTING_HEADER_URL', 'URL');
define('SEF_URLS_MANUAL_LISTING_HEADER_ALIAS', 'URL Alias / Ersatz URL (Standard URL)');
define('SEF_URLS_MANUAL_NEW_ELEMENT', 'Neue URL anlegen');
define('BUTTON_NEW_URL', 'Neue URL');
define('SEF_URLS_MANUAL_EDIT_ALL_LANGUAGES', 'Sprachunabh&auml;ngig');
define('SEF_URLS_MANUAL_EDIT_TEXT_DEFAULT', 'URL Benutzen:');
define('SEF_URLS_MANUAL_EDIT_TEXT_ALIAS', 'Alias URL:');
define('SEF_URLS_MANUAL_EDIT_TEXT_NORMAL_URL', 'Ursp&uuml;ngliche URL:');


// Config:
define('HEADING_TITLE_CONFIG', 'Konfiguration');
#Configuration Values:
define('SEF_URLS_ACTIVATE_TITLE', 'SEF URLs Modul Aktivieren');
define('SEF_URLS_ACTIVATE_DESC', 'Wollen Sie das SEF URLs Modul aktivieren?');
define('SEF_URLS_MULTILINGUAL_TITLE', 'Multisprach Funktionalit&auml;t');
define('SEF_URLS_MULTILINGUAL_DESC', 'Wollen Sie die Multisprach Funktionalität benutzen. (http://www.IhrShop.ch/de/...)');
define('SEF_URLS_XTC_SEF_URLS_TITLE', 'xt:Commerce SEF URLS');
define('SEF_URLS_XTC_SEF_URLS_DESC', 'Falls Sie vorhin die xt:Commerce SEF URLs verwendet haben, setzten Sie diesen wert auf True, falls nicht bitte abschalten.');
define('SEF_URLS_GARBAGE_COLLECTOR_FACTOR_TITLE', 'Garbage Collector');
define('SEF_URLS_GARBAGE_COLLECTOR_FACTOR_DESC', 'Um &Auml;nderungen an den URLs in die Datenbank aufzunehmen, m&uuml;ssen die Module regelm&auml;ssig erneuert werden. Dies &uuml;bernimmt der Garbage Collector. Mit diesem Wert k&ouml;nnen Sie festlegen, wie oft dieser aufgerufen werden soll. Geben Sie einen Wert zwischen 1 und 0 ein. (1 = 100%, 0 = 0%) Falls Sie diese Funktion nicht nutzen m&ouml;chten, geben Sie einfach 0 ein, dann wird der Garbage Collector nie ausgef&uuml;hrt!');


// Modules:
define('TABLE_HEADING_MODULES', 'Module');
define('TABLE_HEADING_FILENAME', 'Dateiname:');
define('TABLE_HEADING_ACTION', 'Aktion:');

define('TABLE_FOOTER_DIRECTORY', 'Modul Ordner:');

// new to v2.2.0
define('HEADING_TITLE_UPDATE', 'Update');
define('HEADING_TITLE_UNINSTALL', 'Deinstallation');
define('SEF_URLS_BUTTON_UPDATE_START', 'Update starten');
define('TEXT_UPDATE_WARNING', 'Bevor Sie das Update starten, sichern Sie die Datenbank. (zum Beispiel mit phpMyAdmin) Dananch starten Sie bitte das Update.');
define('SEF_URLS_CURRENT_VERSION_TEXT', 'Aktuelle Version:');
define('SEF_URLS_CACHE_ACTIVE_TITLE', 'Cache aktivieren');
define('SEF_URLS_CACHE_ACTIVE_DESC', 'Wollen Sie den Cache f&uuml;r die SEF URLs aktivieren? Falls Sie den xt:Commerce Cache aktiviert haben, kann es aus Performance Gründen sein, dass Sie den SEF URL Cache deaktivieren! Am Einfachsten testen Sie dies in Ihrem Shop aus, was besser ist.');



?>