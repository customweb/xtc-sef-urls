/**
 * add for SEF Module (by customweb GmbH)
 */
} // close the open tag! (add also by customweb GmbH)
if ((SEF_URLS_ACTIVATE == 'True' or $_SESSION['sef_urls_activate_temporary']) and $GLOBALS['gotSpezialFile'] != true)
{
	require_once DIR_WS_CLASSES.'class.SefPage.php';
	$sefPage = new SefPage($_GET['q']);
	
	// If it is needed it throw a 301 Redirect
	$sefPage->redirectIfNeeded();
	
	$_GET = $sefPage->getAllParameters();
	
	if($_SERVER['SCRIPT_FILENAME'] == '/')
	{
		$_SERVER['SCRIPT_FILENAME'] = 'index.php';
	}
	
	if($file = $sefPage->getRequireFile())
	{
		$PHP_SELF = $file;
		require $file;
		die();
	}	
}
// end add for SEF Module (by customweb GmbH)
 

