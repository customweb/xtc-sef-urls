<?PHP
/**
::Header::
 */


define('TABLE_CW_SEF_PAGES', 'cw_sef_pages');
define('TABLE_CW_SEF_PARAMETERS', 'cw_sef_parameters');
define('TABLE_CW_SEF_URLS_CACHE', 'cw_sef_urls_cache');
define('PATH_TO_SEF_MAIN_DIR', DIR_FS_DOCUMENT_ROOT.'admin/includes/modules/sef_urls/');

class SefPage
{
	private $pageId;
	
	private $urlId;
	
	private $pageFile;
	
	private $urlAlias;
	
	private $urlDefault = true;
	
	private $urlModule;
	
	private $urlModuleEntryId;
	
	private $lastMod;	
	
	private $pageParameters = false;
	
	private $allParameters;
	
	private $langChange = false;
	
	private static $arrLanguages = false;
	
	private static $arrCacheForThisPage = false;
	
	private static $cacheIsActive = false;
	
	private static $cacheIsModified = false;
	
	private static $cacheId = false;
	
		
	
	public function __construct()
	{
		if(SEF_URLS_MULTILINGUAL == 'True' && $arrLanguages == false)
		{
			$result = xtc_db_query('SELECT languages_id, code FROM ' . TABLE_LANGUAGES);
			while($row = xtc_db_fetch_array($result))
			{
				self::$arrLanguages[$row['languages_id']] = $row['code'];
			}
		}
		
		if(SEF_URLS_CACHE_ACTIVE == 'True' && self::$arrCacheForThisPage == false)
		{
			self::$cacheIsActive = true;
			$sql = 'SELECT * FROM ' . TABLE_CW_SEF_URLS_CACHE . ' WHERE site_url = \'' . md5($_SERVER['REQUEST_URI']) . '\'';
			$result = xtc_db_query($sql);
			if($row = xtc_db_fetch_array($result))
			{
				self::$arrCacheForThisPage = unserialize($row['cache']);
				self::$cacheId = $row['cache_id'];
			}
			else
			{
				self::$arrCacheForThisPage = array();
			}
		}
		
		$numarg = func_num_args();
		// call through the xtc_href_link
		if($numarg == 5)
		{
			//$this->urlAlias = func_get_arg(0).'?'.func_get_arg(1);
			$page = func_get_arg(0);
			$parameters = func_get_arg(1);
			$this->setPageDataFromLinkRequest($page, $parameters);
		}
		// call with the page ID
		elseif(is_int(func_get_arg(0)))
		{
			$this->pageId = func_get_arg(0);
		}
		// call with the request URI
		elseif(is_string(func_get_arg(0)))
		{
			$this->setPageDataFromRequestURL(func_get_arg(0));
			// for the GarbageCollector
			if(rand(1, 1000)/1000 <= SEF_URLS_GARBAGE_COLLECTOR_FACTOR)
				$this->runGarbageCollector();
		}
		// no $_GET['q'] is set!
		else
		{
			$path = str_replace(DIR_WS_CATALOG, '', $_SERVER['REQUEST_URI']);
			if(substr($path, 0, 1) == '/')
				$path = substr($path, 1);
			$this->setPageDataFromRequestURL($path);
		}
		
	}
	
	private function setPageDataFromRequestURL($url)
	{
		if(SEF_URLS_XTC_SEF_URLS == 'True' && strlen(getenv('PATH_INFO')) > 1)
		{
			$PHP_SELF = str_replace(getenv('PATH_INFO'), '', $PHP_SELF);
			$vars = explode('/', substr(getenv('PATH_INFO'), 1));
			for ($i = 0, $n = sizeof($vars); $i < $n; $i +=2)
			{
				$params[ $vars[$i] ] = $vars[$i+1];
			}
			$pageFile = basename($_SERVER['SCRIPT_FILENAME']);
			
			// Workaround for the content section: (content=Title is not need -> so delete it)
			unset($params['content']);
		}
		else
		{
			$splits = explode('?', $url);
			$params = $_GET;
			unset($params['q']);
			$pageFile = $splits[0];
		}

		if(SEF_URLS_MULTILINGUAL == 'True')
		{
			// if there is a de/ in the url:
			if(preg_match('/^([a-z]{2})\/(.*)$/', $pageFile, $result))
			{
				$urlWithoutParameters = $result[2];
				
				// Get Lang:
				$this->languageId = array_search($result[1], self::$arrLanguages);
				$this->languageCode = $result[1];
				if($this->languageId == false)
				{
					$urlWithoutParameters = $pageFile;
				}
			}
			else
			{
				$urlWithoutParameters = $pageFile;
				$this->languageCode = $_SESSION['language_code'];
				$this->languageId = $_SESSION['languages_id'];
			}
		}
		else
		{
			$this->languageCode = $_SESSION['language_code'];
			$this->languageId = $_SESSION['languages_id'];
			$urlWithoutParameters = $pageFile;
		}

		$additionalParameters = self::parseParametersFromStringToArray($params);
		$sql = 'SELECT * FROM ' . TABLE_CW_SEF_PAGES . ' WHERE url_alias = \''.$urlWithoutParameters.'\' AND (languages_id = \''.$this->languageId.'\' OR languages_id IS NULL)';
		// if the url is a default:
		if($this->readDataFromDb($sql))
		{
			$this->allParameters = array_merge($this->pageParameters, $additionalParameters);
			// Reminder to page which not changed except some parameters: (for example language switcher)
			$this->allParameters['current_page'] = $this->pageId;
		}
		// Probably it is not a default OR it is nothing:
		else
		{
			// It is not a default -> so look to set the urlDefault to false
			if($this->setPageDataFromLinkRequest($pageFile, self::parseParametersFromArrayToString($additionalParameters)) )
			{
				$this->urlDefault = false;
			}
			$this->allParameters = array_merge($_GET, $additionalParameters);
		}
		if($_SESSION['languages_id'] != $this->languageId)
			$this->allParameters['language'] = $this->languageCode;
	}
	
	
	private function setPageDataFromLinkRequest($page, $parameters)
	{
		if(self::$cacheIsActive)
		{
			if(is_array($parameters))
				$url = $page.$this->parseParametersFromArrayToString($parameters);
			else
				$url = $page.$parameters;
			
			$cacheUrl = md5($url);
			
			if(isset(self::$arrCacheForThisPage[ $cacheUrl ]))
			{
				$data = self::$arrCacheForThisPage[ $cacheUrl ];
				$this->urlAlias = $data['url_alias'];
				return $data['return'];
			}
		}
				
		// if only http://www.shop.com/index.php is requested:
		if($page == 'index.php' && empty($parameters))
		{
			$this->languageCode = $_SESSION['language_code'];
			$this->languageId = $_SESSION['languages_id'];
			$this->pageFile = '';
			// add for cache:
			if(self::$cacheIsActive)
			{
				$data['url_alias'] = $this->urlAlias;
				$data['return'] = false;
				self::$arrCacheForThisPage[$cacheUrl] = $data;
				self::$cacheIsModified = true;
			}
			return false;
		}
		
		$parameters = self::parseParametersFromStringToArray($parameters);
		
		if(isset($parameters['language']) && SEF_URLS_MULTILINGUAL == 'True')
		{
			$this->languageId = array_search($parameters['language'], self::$arrLanguages);
			$this->languageCode = $parameters['language'];
			unset($parameters['language']);
			$this->langChange = true;
		}
		else
		{
			$this->languageCode = $_SESSION['language_code'];
			$this->languageId = $_SESSION['languages_id'];
		}
		
		// if the requested url is the same as it has been shown allready, only some parameters has changed:
		if((isset($parameters['current_page']) && $parameters['current_page'] != 0 && $page == 'index.php') || $this->langChange)
		{
			$sql = '
					SELECT 
						`'.TABLE_CW_SEF_PAGES.'`.page_id,
						page_file,
						url_id,
						url_alias,
						url_module,
						url_default,
						last_mod,
						languages_id,
						url_module_entry_id
					FROM 
						`'.TABLE_CW_SEF_PAGES.'`
					WHERE 
						page_id = \''.$parameters['current_page'].'\'
					  AND 
						(languages_id = \''.$this->languageId.'\' OR languages_id IS NULL)
					  AND
						`'.TABLE_CW_SEF_PAGES.'`.url_default = \'1\'
				';
		}
		
		// There are parameters to check:
		elseif(count($parameters) > 0)
		{
			foreach($parameters as $name => $value)
			{
				$parametersQuery .= '"'.$name.'='.$value.'" ';
			}
			$sql = '
					SELECT 
						`'.TABLE_CW_SEF_PAGES.'`.page_id,
						page_file,
						url_id,
						url_alias,
						url_module,
						last_mod,
						url_default,
						url_module_entry_id,
						parameter_id,
						`'.TABLE_CW_SEF_PAGES.'`.languages_id,		
						count(parameter_id) AS num 
					FROM 
						`'.TABLE_CW_SEF_PAGES.'`,
						`'.TABLE_CW_SEF_PARAMETERS.'`
					WHERE 
						`'.TABLE_CW_SEF_PAGES.'`.page_id = `'.TABLE_CW_SEF_PARAMETERS.'`.page_id
					  AND
						`'.TABLE_CW_SEF_PAGES.'`.page_file = \''.$page.'\'
					  AND 
						(`'.TABLE_CW_SEF_PAGES.'`.languages_id = \''.$this->languageId.'\' OR `'.TABLE_CW_SEF_PAGES.'`.languages_id IS NULL)
					  AND 
						(`'.TABLE_CW_SEF_PARAMETERS.'`.languages_id = `'.TABLE_CW_SEF_PAGES.'`.languages_id)
					  AND
						`'.TABLE_CW_SEF_PAGES.'`.url_default = \'1\'
					  AND 
						MATCH(parameter) AGAINST (\''. $parametersQuery. '\' IN BOOLEAN MODE)
					GROUP BY 
						`'.TABLE_CW_SEF_PARAMETERS.'`.page_id 
					ORDER BY 
						num DESC
				';
		}
		else
		{
			$sql = '
					SELECT 
						`'.TABLE_CW_SEF_PAGES.'`.page_id,
						page_file,
						url_id,
						url_alias,
						url_module,
						url_default,
						last_mod,
						languages_id,
						url_module_entry_id
					FROM 
						`'.TABLE_CW_SEF_PAGES.'`
					WHERE 
						`'.TABLE_CW_SEF_PAGES.'`.page_file = \''.$page.'\'
					  AND 
						(languages_id = \''.$this->languageId.'\' OR languages_id IS NULL)
				';
		}
		if($this->readDataFromDb($sql))
		{
			// Override all parameters with the same keys
			$diffKeys = array_diff(array_keys($parameters), array_keys($this->pageParameters));
			$sameKeys = array_diff(array_keys($parameters), $diffKeys);
			foreach($sameKeys as $index)
			{
				unset($parameters[$index]);
			}

			$this->allParameters = $parameters;
			$additionalParameters = array_diff($this->allParameters, $this->pageParameters);
			$this->urlAlias = $this->urlAlias.self::buildQuery($additionalParameters);
		
			// add for cache:
			if(self::$cacheIsActive)
			{
				$data['url_alias'] = $this->urlAlias;
				$data['return'] = true;
				self::$arrCacheForThisPage[$cacheUrl] = $data;
				self::$cacheIsModified = true;
			}
			return true;
		}
		
		// no alias -> return normal url like xtc_href_link !
		else
		{
			if($this->langChange)
				$parameters['language'] = $this->languageCode;
				
			$this->urlAlias = $page.self::buildQuery($parameters);
			// add for cache:
			if(self::$cacheIsActive)
			{
				$data['url_alias'] = $this->urlAlias;
				$data['return'] = false;
				self::$arrCacheForThisPage[$cacheUrl] = $data;
				self::$cacheIsModified = true;
			}
			return false;
		}

	}
	
	private function readDataFromDb($sql)
	{
		$result = xtc_db_query($sql);
		if($row = xtc_db_fetch_array($result))
		{
			$this->pageId = $row['page_id'];
			$this->pageFile = $row['page_file'];
			$this->urlId = $row['url_id'];
			$this->urlAlias = $row['url_alias'];
			$this->urlDefault = $row['url_default'] == 0 ? false : true;
			$this->urlModule = $row['url_module'];
			$this->urlModuleEntryId = $row['url_module_entry_id'];
			$this->lastMod = $row['last_mod'];
			if(SEF_URLS_MULTILINGUAL == 'True')
			{
				if(!isset($this->languageId))
				{
					if(!is_null($row['languages_id']))
					{
						$this->languageCode = self::$arrLanguages[$row['languages_id']];
						$this->languageId = $row['languages_id'];
					}
					else
					{
						$this->languageCode = $_SESSION['language_code'];
						$this->languageId = $_SESSION['languages_id'];
					}
				}
				$this->urlAlias = $this->languageCode.'/'.$this->urlAlias;
			}
			$this->pageParameters = $this->getParametersFromDb($this->languageId);
			return true;
		}
		else
		{
			return false;
		}
	}
	
	
	public function getUrlAlias()
	{
		return $this->urlAlias;
	}
	
	
	public function getAllParameters()
	{
		// add for other apps that need to know which file is requested:
		$_SERVER['PHP_SELF'] = '/'.$this->pageFile;
		$_SERVER['SCRIPT_NAME'] = '/'.$this->pageFile;
		return $this->allParameters;
	}
	
	private static function buildQuery($parameters)
	{
		unset($parameters['current_page']);
		if(count($parameters) > 0)
			return '?'.self::parseParametersFromArrayToString($parameters);
		else
			return '';
	}
	
	public function redirectIfNeeded()
	{
		if($this->urlDefault != true)
		{
			$result = xtc_db_query('SELECT url_alias FROM ' . TABLE_CW_SEF_PAGES . ' WHERE page_id = \''.$this->pageId.'\' AND url_default = \'1\' AND languages_id = \'' . $this->languageId . '\'');
			if($row = xtc_db_fetch_array($result))
			{
				if(SEF_URLS_MULTILINGUAL == 'True')
					$langUrl = $this->languageCode.'/';
				else
					$langUrl = '';
				
				$additionalParameters = array_diff($this->allParameters, $this->pageParameters);
				header('HTTP/1.1 301 Moved Permanently');
				header('Location: '.HTTP_SERVER.DIR_WS_CATALOG . $langUrl . $row['url_alias'].self::buildQuery($additionalParameters));
				header('Connection: close');
			}
		}
	}
	
	public function getRequireFile()
	{
		if($GLOBALS['gotSpezialFile'] != true && $this->pageFile != 'index.php')
		{
			$GLOBALS['gotSpezialFile'] = true;
			return $this->pageFile;
		}
		else
		{
			return false;
		}
	}
	
	private static function parseParametersFromStringToArray($string)
	{
		// if it is allready parsed:
		if(is_array($string))
			return $string;
		else
			$string = str_replace('&amp;', '&', $string);
		parse_str($string, $params);
		return $params;
	}
	
	private static function parseParametersFromArrayToString($arrParams)
	{
		$arr = array();
		if(is_array($arrParams))
		{
			foreach ($arrParams as $key => $val)
			{
				$arr[] = urlencode($key)."=".urlencode($val);
			}
		}
		return implode($arr, "&amp;");
	}
	
	private function getParametersFromDb($languageId)
	{
		if($this->pageParameters == false)
		{
			$result = xtc_db_query('SELECT * FROM ' . TABLE_CW_SEF_PARAMETERS . ' WHERE page_id = \''.$this->pageId.'\' AND languages_id = \'' . $languageId .  '\'');
			while($row = xtc_db_fetch_array($result))
			{
				$splits = explode('=', $row['parameter']);
				$this->pageParameters[$splits[0]] = $splits[1];
			}
			if($this->pageParameters == false)
				$this->pageParameters = array();
			return $this->pageParameters;
		}
		else
		{
			return $this->pageParameters;
		}
	}
	
	
	public function runGarbageCollector()
	{
		$modules = glob(DIR_FS_DOCUMENT_ROOT.'admin/includes/modules/sef_modules/*.php');
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
		
		// cleanup cache:
		xtc_db_query('TRUNCATE TABLE `' . TABLE_CW_SEF_URLS_CACHE . '`');
	}
	
	public function __destruct()
	{
		if(self::$cacheIsActive && self::$cacheIsModified)
		{
			// add
			if(self::$cacheId == false)
			{
				xtc_db_query('INSERT INTO ' . TABLE_CW_SEF_URLS_CACHE . ' (site_url, cache) VALUES(\'' . md5($_SERVER['REQUEST_URI']) . '\', \'' . serialize(self::$arrCacheForThisPage) . '\')');
				self::$cacheId = xtc_db_insert_id();
			}
			// update
			else
			{
				xtc_db_query('UPDATE ' . TABLE_CW_SEF_URLS_CACHE . ' SET cache = \'' . serialize(self::$arrCacheForThisPage) . " WHERE cache_id = " . self::$cacheId . '\'');
			}
		}
	}
	
}


function getAutoIncrement($table, $field)
{
	$result = xtc_db_query('SELECT max('. $field . ') AS max FROM ' . $table );
	$row = xtc_db_fetch_array($result);
	return $row['max']+1;
}



?>
