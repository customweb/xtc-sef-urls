<?php 
/**
::Header::
 */

require_once 'RealSefProductUrls.php';

class RealSefReviewUrls extends RealSefProductUrls
{
	protected $categories;
	
	public function __construct()
	{
		
	}

	public function getTitle()
	{
		return SEF_MODULES_REAL_SEF_REVIEW_URLS_TITLE;
	}
	
	public function getDescription()
	{
		return SEF_MODULES_REAL_SEF_REVIEW_URLS_DESC;
	}
	
	public function updateUrls()
	{
		$categories = $this->buildCategoriesTree();
		$versions = $this->getCurrentVersionOfAllEntries();
		$result = xtc_db_query('SELECT
									p.products_id,
									products_name,
									language_id,
									products_last_modified,
									products_date_added,
									categories_id
								FROM 
									' . TABLE_PRODUCTS .' AS p,
									' . TABLE_PRODUCTS_DESCRIPTION . ' AS pd ,
									' . TABLE_PRODUCTS_TO_CATEGORIES . ' AS cat
								WHERE 
									p.products_id = pd.products_id 
								  AND
									p.products_id = cat.products_id
								  AND
									cat.categories_id != 0
								');
		
		while($row = xtc_db_fetch_array($result))
		{
			// Write Reviews:
			$params = xtc_product_link($row['products_id'], $row['products_name']);
			$urlAlias = $categories[$row['categories_id']][$row['language_id']]['path'] . $this->cleanUrl($row['products_name']).'/write-review';
			
			$moduleEntryId = 'review_write_'.$row['products_id'];
			
			if($versions[ $moduleEntryId ][ $row['language_id'] ] == 0)
				$version = 1;
			else
				$version = $versions[ $moduleEntryId ][ $row['language_id'] ]+1;
			$this->addUrl(
					FILENAME_PRODUCT_REVIEWS_WRITE, 
					$params,
					$row['language_id'],
					$urlAlias,
					true,
					$moduleEntryId,
					$version
				);
			
			// List Reviews:
			$params = 'products_id='.$row['products_id'];
			$urlAlias = $categories[$row['categories_id']][$row['language_id']]['path'] . $this->cleanUrl($row['products_name']).'/review';
			
			$moduleEntryId = 'review_'.$row['products_id'];
			
			if($versions[ $moduleEntryId ][ $row['language_id'] ] == 0)
				$version = 1;
			else
				$version = $versions[ $moduleEntryId ][ $row['language_id'] ]+1;
			$this->addUrl(
					FILENAME_PRODUCT_REVIEWS, 
					$params,
					$row['language_id'],
					$urlAlias,
					true,
					$moduleEntryId,
					$version
				);
			
		}
		
		$result = xtc_db_query('SELECT
									p.products_id,
									reviews_id,
									products_name,
									customers_name,
									language_id,
									products_last_modified,
									products_date_added,
									categories_id
								FROM 
									' . TABLE_PRODUCTS .' AS p,
									' . TABLE_REVIEWS .' AS reviews,
									' . TABLE_PRODUCTS_DESCRIPTION . ' AS pd ,
									' . TABLE_PRODUCTS_TO_CATEGORIES . ' AS cat
								WHERE 
									p.products_id = pd.products_id 
								  AND
									reviews.products_id = p.products_id
								  AND
									p.products_id = cat.products_id
								  AND
									cat.categories_id != 0
								');
		
		while($row = xtc_db_fetch_array($result))
		{
			// Reviews:
			$params = 'products_id='.$row['products_id'].'&reviews_id='.$row['reviews_id'];
			$urlAlias = $categories[$row['categories_id']][$row['language_id']]['path'] . $this->cleanUrl($row['products_name']).'/reviews/'.$this->cleanUrl($row['customers_name']).'-'.$row['reviews_id'].'.html';
			
			$moduleEntryId = 'review_info_'.$row['reviews_id'];
			
			if($versions[ $moduleEntryId ][ $row['language_id'] ] == 0)
				$version = 1;
			else
				$version = $versions[ $moduleEntryId ][ $row['language_id'] ]+1;
			$this->addUrl(
					FILENAME_PRODUCT_REVIEWS_INFO, 
					$params,
					$row['language_id'],
					$urlAlias,
					true,
					$moduleEntryId,
					$version
				);
			
		}
	}
	
	public function install()
	{
		InsertConfiguration('SEF_MODULES_REAL_SEF_REVIEW_URLS_ACTIVE', 'True', 6, 1, 'xtc_cfg_select_option(array(\'True\', \'False\'), ');
		
		// add the urls
		$this->updateUrls();
	}
	
	public function getConfigurationKeys()
	{
		return array('SEF_MODULES_REAL_SEF_REVIEW_URLS_ACTIVE');
	}
	
	
	public function uninstall()
	{
		xtc_db_query('DELETE FROM '.TABLE_CONFIGURATION." WHERE configuration_key IN ('".implode("', '", $this->getConfigurationKeys())."')");
		
		xtc_db_query('DELETE FROM ' . TABLE_CW_SEF_PAGES.' WHERE url_module = \''.get_class($this).'\'');
	}
	
	public function isInstalled()
	{
		return defined(SEF_MODULES_REAL_SEF_REVIEW_URLS_ACTIVE) ? true : false;
	}
	
	public function isActive()
	{
		return defined(SEF_MODULES_REAL_SEF_REVIEW_URLS_ACTIVE) && SEF_MODULES_REAL_SEF_REVIEW_URLS_ACTIVE == 'True' ? true : false;
	}
}
