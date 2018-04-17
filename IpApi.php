<?php

namespace GoogleMapService;

use GuzzleHttp\Client;

class IpApi implements GeoLocationApiInterface
{


    // parameter names for location that comes from ip-api.com
    const API_COUNTRY_ISO_CODE_KEY_NAME = 'countryCode';
    const API_COUNTRY_KEY_NAME = 'country';
    const API_CITY_KEY_NAME = 'city';
    const API_STATE_ABBR_KEY_NAME = 'region';
    const API_STATE_NAME_KEY_NAME = 'regionName';
    const API_ZIPCODE_KEY_NAME = 'zip';
    const API_LATITUDE_KEY_NAME = 'lat';
    const API_LONGITUDE_KEY_NAME = 'lon';
    const API_TIMEZONE_KEY_NAME = 'timezone';


    /**
     * {@inheritdoc}
     */
    public function locate($ip)
    {
        $url = $this->buildURL($ip);

        // Http Client Connection
        try{
        	$client = new Client();
        	$response = $client->request('GET', $url);
        }catch(Exception $e){
        	return null;
        }

        if ($response->getStatusCode() === 200) {
            // Parse body content
            $result = json_decode($response->getBody(), true);
        } else {
            return null;
        }

        if ($result === null || !isset($result['status'])) {
            return null;
        }

        if ($result['status'] === 'fail') {
            return null;
        }

        return [
            self::IP_KEY_NAME => $ip,
            self::COUNTRY_ISO_CODE_KEY_NAME => $result[self::API_COUNTRY_ISO_CODE_KEY_NAME],
            self::COUNTRY_KEY_NAME => $result[self::API_COUNTRY_KEY_NAME],
            self::CITY_KEY_NAME => $result[self::API_CITY_KEY_NAME],
            self::STATE_ABBR_KEY_NAME => $result[self::API_STATE_ABBR_KEY_NAME],
            self::STATE_NAME_KEY_NAME => $result[self::API_STATE_NAME_KEY_NAME],
            self::ZIPCODE_KEY_NAME => $result[self::API_ZIPCODE_KEY_NAME],
            self::LATITUDE_KEY_NAME => $result[self::API_LATITUDE_KEY_NAME],
            self::LONGITUDE_KEY_NAME => $result[self::API_LONGITUDE_KEY_NAME],
            self::TIMEZONE_KEY_NAME => $result[self::API_TIMEZONE_KEY_NAME]
        ];
    }


    /**
     * Builds API request url.
     *
     * @param string $ip
     * @return string
     */
    public function buildURL($ip)
    {

        $url = 'http://ip-api.com/json/' . $ip;

        if (defined('IPAPI_KEY')) {
            $url = 'https://pro.ip-api.com/json/' . $ip . '?key=' . IPAPI_KEY;
        }

        return $url;
    }

}