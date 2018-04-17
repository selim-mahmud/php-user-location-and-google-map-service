<?php

namespace GoogleMapService;

interface GeoLocationApiInterface
{
	
	// key names for location array
    const IP_KEY_NAME = 'ip';
    const COUNTRY_ISO_CODE_KEY_NAME = 'country_iso_code';
    const COUNTRY_KEY_NAME = 'country';
    const CITY_KEY_NAME = 'city';
    const STATE_ABBR_KEY_NAME = 'state_abbr';
    const STATE_NAME_KEY_NAME = 'state_name';
    const ZIPCODE_KEY_NAME = 'zipcode';
    const LATITUDE_KEY_NAME = 'latitude';
    const LONGITUDE_KEY_NAME = 'longitude';
    const TIMEZONE_KEY_NAME = 'timezone';

    /**
     * Determine a location based on the provided IP address.
     *
     * @param string $ip
     *
     * @return null|array
     */
    public function locate($ip);

}