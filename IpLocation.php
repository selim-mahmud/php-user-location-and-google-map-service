<?php

namespace GoogleMapService;

use Cache\CacheManager;

class IpLocation
{
    /**
     * default IP Location data
     */
    const IP = '204.45.156.178';
    const COUNTRY_ISO_CODE = 'US';
    const COUNTRY_NAME = 'United States';
    const CITY_NAME = 'Denver';
    const STATE_ABBR = 'CO';
    const STATE_NAME = 'Colorado';
    const ZIPCODE = '80202';
    const LATITUDE = 39.7525;
    const LONGITUDE = -104.9995;
    const TIMEZONE = 'America/Denver';

    const EXPIRATION_TIME = 1440; // in minutes (24 hours)

    /**
     * Current location array.
     *
     * @var array
     */
    protected $location;

    /**
     * @var GeoLocationApiInterface
     */
    protected $geoLocationApiInterface;

    /**
     * @var CacheManager
     */
    protected $cacheManager;


    /**
     * IpLocation constructor.
     *
     * @param GeoLocationApiInterface $ipApi
     */
    public function __construct(GeoLocationApiInterface $geoLocationApiInterface, CacheManager $cacheManager)
    {
        $this->geoLocationApiInterface = $geoLocationApiInterface;
        $this->cacheManager = $cacheManager;
    }

    /**
     * Get the location from the provided IP.
     *
     * @param string|null $ip
     *
     * @return array
     */
    public function getLocation($ip = null)
    {
        // Get location data
        $this->location = $this->find($ip);
        return $this->location;
    }

    /**
     * Find location from IP.
     *
     * @param string|null $ip
     *
     * @return array
     */
    private function find($ip = null)
    {
        // Check if the ip is not local or empty
        if ($this->isValidIp($ip)) {

        	$location = $this->cacheManager->get($ip);

        	//if no cache record found, it returns false
        	//if expired cache record found, it returns false
        	//if active cache record found, it returns unserialized value
        	if($location){
        		return $location;
        	}

            //if nothing found in cache record, it will get new location from Geo IP service and store in cache
            $location = $this->geoLocationApiInterface->locate($ip);
            if ($location) { //if found from ip location service
            	$this->cacheManager->set($ip, $location, self::EXPIRATION_TIME);
                return $location;
            }
        }

        return $this->getDefaultLocation();
    }

    /**
     * get user's state from ip location
     *
     * @param string|null $ip
     * @return string
     */
    public function getUserState($ip = null)
    {
        $location = $this->getLocation($ip);

        if(array_key_exists($location[GeoLocationApiInterface::STATE_ABBR_KEY_NAME], $GLOBALS["global_states"])){
        	return $location[GeoLocationApiInterface::STATE_NAME_KEY_NAME];
        }

        return self::STATE_NAME;
    }

    /**
     * get user's abbreviated state name from ip location
     *
     * @param string|null $ip
     * @return string
     */
    public function getUserStateAbbr($ip = null)
    {
        $location = $this->getLocation($ip);

        if(array_key_exists($location[GeoLocationApiInterface::STATE_ABBR_KEY_NAME], $GLOBALS["global_states"])){
        	return $location[GeoLocationApiInterface::STATE_ABBR_KEY_NAME];
        }

        return self::STATE_ABBR;
    }


    /**
     * get user's county from ip location
     *
     * @param string|null $ip
     * @return string
     */
    public function getUserCounty($ip = null)
    {
        // the "IpAPI" service does not return "county" level data: 'County', 'Region' (state), 'City' only
        return "COUNTY IS NOT SUPPORTED";
    }


    /**
     * get user's city from ip location
     *
     * @param string|null $ip
     * @return string
     */
    public function getUserCity($ip = null)
    {
        $location = $this->getLocation($ip);

        if(array_key_exists($location[GeoLocationApiInterface::STATE_ABBR_KEY_NAME], $GLOBALS["global_states"])){
        	return $location[GeoLocationApiInterface::CITY_KEY_NAME];
        }

        return self::CITY_NAME;
    }


    /**
     * get user's zipcode from ip location
     *
     * @param string|null $ip
     * @return string
     */
    public function getUserZipcode($ip = null)
    {
        $location = $this->getLocation($ip);

        if(array_key_exists($location[GeoLocationApiInterface::STATE_ABBR_KEY_NAME], $GLOBALS["global_states"])){
        	return $location[GeoLocationApiInterface::ZIPCODE_KEY_NAME];
        }

        return self::ZIPCODE;
    }


    /**
     * Get the client IP address.
     *
     * @return string
     */
    public function getClientIP()
    {

        $ip = '';
	    if (isset($_SERVER['HTTP_CLIENT_IP']) && $this->isValidIp($_SERVER['HTTP_CLIENT_IP']))
	        $ip = $_SERVER['HTTP_CLIENT_IP'];
	    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $this->isValidIp($_SERVER['HTTP_X_FORWARDED_FOR']))
	        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	    else if(isset($_SERVER['HTTP_X_FORWARDED']) && $this->isValidIp($_SERVER['HTTP_X_FORWARDED']))
	        $ip = $_SERVER['HTTP_X_FORWARDED'];
	    else if(isset($_SERVER['HTTP_FORWARDED_FOR']) && $this->isValidIp($_SERVER['HTTP_FORWARDED_FOR']))
	        $ip = $_SERVER['HTTP_FORWARDED_FOR'];
	    else if(isset($_SERVER['HTTP_FORWARDED']) && $this->isValidIp($_SERVER['HTTP_FORWARDED']))
	        $ip = $_SERVER['HTTP_FORWARDED'];
	    else if(isset($_SERVER['REMOTE_ADDR']) && $this->isValidIp($_SERVER['REMOTE_ADDR']))
	        $ip = $_SERVER['REMOTE_ADDR'];
	    else
	        $ip = '127.0.0.0';

	    return $ip;
    }

    /**
     * Checks if the ip is valid.
     *
     * @param string $ip
     * @return bool
     */
    private function isValidIp($ip)
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP,
                FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)
            && !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE)
        ) {
            return false;
        }

        return true;
    }

    /**
     * Get the default location
     *
     * @return array
     */
    public function getDefaultLocation()
    {
        return [
            GeoLocationApiInterface::IP_KEY_NAME => self::IP,
            GeoLocationApiInterface::COUNTRY_ISO_CODE_KEY_NAME => self::COUNTRY_ISO_CODE,
            GeoLocationApiInterface::COUNTRY_KEY_NAME => self::COUNTRY_NAME,
            GeoLocationApiInterface::CITY_KEY_NAME => self::CITY_NAME,
            GeoLocationApiInterface::STATE_ABBR_KEY_NAME => self::STATE_ABBR,
            GeoLocationApiInterface::STATE_NAME_KEY_NAME => self::STATE_NAME,
            GeoLocationApiInterface::ZIPCODE_KEY_NAME => self::ZIPCODE,
            GeoLocationApiInterface::LATITUDE_KEY_NAME => self::LATITUDE,
            GeoLocationApiInterface::LONGITUDE_KEY_NAME => self::LONGITUDE,
            GeoLocationApiInterface::TIMEZONE_KEY_NAME => self::TIMEZONE,
        ];
    }


}
