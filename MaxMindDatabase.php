<?php

namespace GoogleMapService;

use GeoIp2\Database\Reader;

class MaxMindDatabase implements GeoLocationApiInterface
{

    /**
     * path of maxmind database file
     *
     * @var string
     */
    protected $databaseFile;


    /**
     * language parameter
     *
     * @var array
     */
    protected $locales;


    /**
     * MaxMindDatabase constructor.
     *
     */
    public function __construct()
    {
        $this->databaseFile = SYSTEMROOT . 'secure/GeoIP2-City.mmdb';
        $this->locales = ['en'];
    }


    /**
     * {@inheritdoc}
     */
    public function locate($ip)
    {

        if (file_exists($this->databaseFile) === false) {

            return null;

        } else {

            try {

                $reader = new Reader($this->databaseFile, $this->locales);
                $record = $reader->city($ip);

            } catch (Exception $e) {

                return null;

            }


        }

        return [
            self::IP_KEY_NAME                =>  $ip,
            self::COUNTRY_ISO_CODE_KEY_NAME  =>  $record->country->isoCode,
            self::COUNTRY_KEY_NAME           =>  $record->country->name,
            self::CITY_KEY_NAME              =>  $record->city->name,
            self::STATE_ABBR_KEY_NAME        =>  $record->mostSpecificSubdivision->isoCode,
            self::STATE_NAME_KEY_NAME        =>  $record->mostSpecificSubdivision->name,
            self::ZIPCODE_KEY_NAME           =>  $record->postal->code,
            self::LATITUDE_KEY_NAME          =>  $record->location->latitude,
            self::LONGITUDE_KEY_NAME         =>  $record->location->longitude,
            self::TIMEZONE_KEY_NAME          =>  $record->location->timeZone
        ];
    }


}