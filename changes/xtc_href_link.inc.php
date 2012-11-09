
	/**
	 * add for customweb SEF URLs
	 */
	if (SEF_URLS_ACTIVATE == 'True' or $_SESSION['sef_urls_activate_temporary'])
	{
		if ($connection == 'NONSSL')
		{
			$link = HTTP_SERVER . DIR_WS_CATALOG;
		}
		elseif ($connection == 'SSL')
		{
			if (ENABLE_SSL == true)
			{
				$link = HTTPS_SERVER . DIR_WS_CATALOG;
			}
			else
			{
				$link = HTTP_SERVER . DIR_WS_CATALOG;
			}
		}
		require_once DIR_WS_CLASSES.'class.SefPage.php';
		$sefPage = new SefPage($page, $parameters, $connection, $add_session_id, $search_engine_safe);
		return $link . $sefPage->getUrlAlias();
	}
	// end add
