<?php namespace Zcard\GeoIP\Drivers;

use Zcard\GeoIP\Interfaces\Driver;
use Zcard\GeoIP\Result;

class TestDriver implements Driver
{
	public function __construct(array $config = array())
	{

	}

	public function ip2location($ipAddress)
	{
		return new Result(array(
			'countryShort'	=> 'BG',
			'countryStr'	=> 'Bulgaria',
			'countryId'		=> 732800,
			'cityCode'		=> 4000,
			'cityStr'		=> 'Plovdiv',
			'cityId'		=> 728193,
			'lng'			=> 42.1500000,
			'lat'			=> 24.7500000
		));
	}
}