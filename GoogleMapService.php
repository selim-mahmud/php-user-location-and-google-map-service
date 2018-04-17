<?php

namespace GoogleMapService;

class GoogleMapService
{
    /**
     * default map setting
     */
    const GOOGLE_STATIC_MAP_ROOT_URL = 'https://maps.googleapis.com/maps/api/staticmap?';
    const FORMAT = 'jpg';
    const SCALE = '2';
    const SIZE = '640x350';

    /**
     * default city address
     */
    const CITY_NAME = 'Denver';
    const STATE_NAME = 'Colorado';
    const COUNTRY_NAME = 'United States';


    /**
     * GoogleMapService constructor.
     *
     */
    public function __construct()
    {
        //
    }


    /**
     * get google's static map of user's city
     *
     * @param IpLocation $ipLocation
     * @return string
     */
    public function getUserStaticCityMapUrl(IpLocation $ipLocation)
    {
        $userIp = $ipLocation->getClientIP();
        $location = $ipLocation->getLocation($userIp);;
        $cityAddressForMap = $this->getCityAddressForMap($location['city'], $location['state_name'], $location['country']);

        // initial/ universal settings
        $configParams = $this->getDefaultMapParams();

        // specific style tweaks
        $configParams['style'] = 'element:labels|lightness:50';
        $configParams['maptype'] = 'roadmap';
        $configParams['zoom'] = '13';

        // set the location
        $configParams['center'] = $cityAddressForMap;

        $urlParams = http_build_query($configParams);

        return self::GOOGLE_STATIC_MAP_ROOT_URL . $urlParams;
    }


    /**
     * get google's static map from a full address
     *
     * @param string $fullAddress
     * @return string
     */
    public function getGoogleStaticFullAddressMapUrl($fullAddress)
    {
        // initial/ universal settings
        $configParams = $this->getDefaultMapParams();

        // specific style tweaks
        $configParams['style'] = 'element:labels|visibility:off';
        $configParams['maptype'] = 'satellite';
        $configParams['zoom'] = '18';

        // set the location
        $configParams['center'] = $this->getFullAddressForMap($fullAddress);

        $urlParams = http_build_query($configParams);

        return self::GOOGLE_STATIC_MAP_ROOT_URL . $urlParams;
    }

    /**
     * get google's static map from latitude, longitude
     *
     * @param string $latitude
     * @param string $longitude
     * @return string
     */
    public function getGoogleStaticMapUrlFromLatLon($latitude, $longitude)
    {
        // initial/ universal settings
        $configParams = $this->getDefaultMapParams();

        // specific style tweaks
        $configParams['style'] = 'element:labels|visibility:off';
        $configParams['maptype'] = 'satellite';
        $configParams['zoom'] = '18';

        // set the location
        $configParams['center'] = $latitude . ',' . $longitude;

        $urlParams = http_build_query($configParams);

        return self::GOOGLE_STATIC_MAP_ROOT_URL . $urlParams;
    }


    /**
     * return city address suitable for google map url
     *
     * @param $city
     * @param $state
     * @param $country
     * @return string
     */
    protected function getCityAddressForMap($city, $state, $country)
    {
        return $city . ',' . $state . ',' . $country;
    }


    /**
     * return default map settings
     *
     * @return array
     */
    protected function getDefaultMapParams()
    {
        return $defaultMapParams = [
            'format' => self::FORMAT,
            'scale' => self::SCALE,
            'size' => self::SIZE,
            'key' => GOOGLE_MAPS_PLACES_API_KEY //defined in system.defines.php
        ];
    }

    /**
     * return full address suitable for google map url
     *
     * @param $fullAddress
     * @return string
     */
    protected function getFullAddressForMap($fullAddress)
    {
        $fullAddress = str_replace(', ', ',', $fullAddress); //remove space after comma (,)
        return $fullAddress;
    }


    /**
     * return default city address suitable for google map url
     *
     * @return string
     */
    protected function getDefaultCityAddressForMap()
    {
        return $this->getCityAddressForMap(self::CITY_NAME, self::STATE_NAME, self::COUNTRY_NAME);
    }
}