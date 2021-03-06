<?php
/**
 * DotBoost Technologies Inc.
 * DotKernel Application Framework
 *
 * @category   DotKernel
 * @package    DotLibrary
 * @copyright  Copyright (c) 2009-2014 DotBoost Technologies Inc. (http://www.dotboost.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version    $Id: Geoip.php 785 2014-03-10 22:06:35Z julian $
 */

/**
 * Geo IP related stuff
 * @category   DotKernel
 * @package    DotLibrary
 * @subpackage DotGeoip
 * @author     DotKernel Team <team@dotkernel.com>
 */

class Dot_Geoip
{
	/**
	 * Constructor
	 * @access public
	 * @return dot_Geoip
	 */
	public function __construct()
	{
		$this->option = Zend_Registry::get('option');
		$this->config = Zend_Registry::get('configuration');
	}
	/**
	 * Get the country by IP
	 * Return an array with : short name, like 'us', long name, like 'United States and response like 'OK' or <error_message> '
	 * @access public
	 * @param string $ip
	 * @return array
	 */
	public function getCountryByIp($ip)
	{
		$country = array(0 => 'unknown',1 => 'NA','response' => 'OK');
		if (Dot_Kernel::validIp($ip)!="public")
		{
			return $country;
		}
		if(extension_loaded('geoip') == FALSE)
		{
			// GeoIp extension is not active
			$api = new Dot_Geoip_Country();
			$geoipPath = $this->config->resources->geoip->path;
			if(file_exists($geoipPath))
			{
				$country = $api->getCountryByAddr($geoipPath, $ip);
			}
			else
			{
				$country['response'] = 'Warning: ' . $this->option->warningMessage->modGeoIp;
			}
		}
		if(function_exists('geoip_db_avail') && geoip_db_avail(GEOIP_COUNTRY_EDITION) && 'unknown' == $country[0])
		{
			//if GeoIP.dat file exists
			$countryCode= geoip_country_code_by_name($ip);
			$countryName = geoip_country_name_by_name($ip);
			$country[0] = $countryCode != FALSE  ? $countryCode : 'unknown';
			$country[1] = $countryName != FALSE  ? $countryName : 'NA';
		}
		if(function_exists('geoip_db_avail') && geoip_db_avail(GEOIP_CITY_EDITION_REV0) && 'unknown' == $country[0])
		{
			//if GeoIPCity.dat file exists
			$record = geoip_record_by_name($ip);
			if(!empty($record))
			{
				$countryCode = $record['country_code'];
				$countryName = $record['country_name'];
				$country[0] = $countryCode != FALSE  ? $countryCode : 'unknown';
				$country[1] = $countryName != FALSE  ? $countryName : 'NA';
			}
		}
		if('unknown' == $country[0])
		{
			// GeoIp extension is active, but .dat files are missing
			$api = new Dot_Geoip_Country();
			$geoipPath = $this->config->resources->geoip->path;
			if(file_exists($geoipPath))
			{
				$country = $api->getCountryByAddr($geoipPath, $ip);
			}
			else
			{
				$country['response'] = 'Warning: ' . $this->option->warningMessage->modGeoIp;
			}
		}
		return $country;
	}
}