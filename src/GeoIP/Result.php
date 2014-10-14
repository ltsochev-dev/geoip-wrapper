<?php namespace Zcard\GeoIP;

use GeoIp2\Model\City;
use GeoIp2\Model\Country;

class Result {

	private $countryShort;
	private $countryStr;
	private $countryId;
	private $ipAddress;
	private $cityCode;
	private $cityStr;
	private $cityId;
	private $lng;
	private $lat;

	/**
	 * Creates an instance of Result
	 * 
	 * @param array $rawData
	 * @return void
	 */
	public function __construct(array $rawData = array())
	{
		foreach($rawData as $key => $value)
		{
			if ( property_exists($this, $key) && is_null($this->{$key}) )
			{
				$this->{$key} = $value;
			}
		}
	}

	/**
	 * Retrieves latitude and longitude of
	 * the found location
	 * 
	 * @return array
	 */
	public function getCoordinates()
	{
		return array('lat' => $this->lat, 'lng' => $this->lng);
	}

	/**
	 * Provides getter access to the private
	 * class properties
	 * 
	 * @param mixed $key
	 * @return mixed
	 */
	public function __get($key)
	{
		return $this->{$key};
	}

	/**
	 * Creates an instance from GeoIp2\Model\City
	 * model object
	 * 
	 * @param GeoIp2\Model\City $model;
	 * @return Zcard\GeoIP\Result
	 */
	public static function createFromMaxMindCity(City $model)
	{
		return new static(array(
			'countryShort'	=> $model->country->isoCode,
			'countryStr'	=> $model->country->names['en'],
			'countryId'		=> $model->country->geonameId,
			'ipAddress'		=> $model->traits->ipAddress,
			'cityCode'		=> $model->postal->code,
			'cityStr'		=> $model->city->name,
			'cityId'		=> $model->city->geonameId,
			'lat'			=> $model->location->latitude,
			'lng'			=> $model->location->longitude,
		));
	}

	/**
	 * Creates an instance from GeoIp2\Model\Country
	 * model object
	 * 
	 * @param GeoIp2\Model\Country $model;
	 * @return Zcard\GeoIP\Result
	 */
	public static function createFromMaxMindCountry(Country $model)
	{

	}

	/**
	 * Currently a placeholder for the MySQL driver
	 * @todo Create a MySQL driver
	 */
	public static function createFromPdo($pdoResult)
	{

	}

}