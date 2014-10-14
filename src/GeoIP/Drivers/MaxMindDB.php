<?php namespace Zcard\GeoIP\Drivers;

use Zcard\GeoIP\Interfaces\Driver;
use GeoIp2\Database\Reader;
use InvalidArgumentException;
use UnexpectedValueException;
use BadMethodCallException;

class MaxMindDB implements Driver {

	/**
	 * When initialized, it is a MaxMind GeoIP2
	 * Database Reader
	 * 
	 * @var GeoIp2\Database\Reader
	 */
	protected static $reader;

	/**
	 * Current database type (E.g. City, Country)
	 * 
	 * @var string
	 */
	private $databaseType;

	/**
	 * List of supported databases by the driver
	 * 
	 * @var array
	 */
	private $databaseSupport = array('GeoLite2-City', 'GeoLite2-Country');

	/**
	 * Creates a driver instance
	 * 
	 * @param array $config
	 * @return void
	 */
	public function __construct(array $config = array())
	{
		if ( is_null(static::$reader) )
		{
			if ( !file_exists($config['maxmind_db']) )
			{
				throw new UnexpectedValueException("Could not find the MaxMind database file at the specified location! Check your paths and try again ({$config['maxmind_db']}).");
			}

			static::$reader = new Reader($config['maxmind_db']);
		}

		$this->databaseType = static::$reader->metadata()->databaseType;

		if ( !in_array($this->databaseType, $this->databaseSupport) )
		{
			throw new UnexpectedValueException("Unexpected database type '{$this->databaseType}'!");
		}
	}

	
	/**
	 * Pulls the city from a MaxMind
	 * binary database
	 * 
	 * @param string $ipAddress
	 * @return GeoIp2\Model\City
	 */
	public function ip2city($ipAddress)
	{
		return static::$reader->city($ipAddress);
	}

	/**
	 * Pulls the country from a MaxMind
	 * binary database
	 * 
	 * @param string $ipAddress
	 * @return GeoIp2\Model\Country
	 */
	public function ip2country($ipAddress)
	{
		return static::$reader->country($ipAddress);
	}

	/**
	 * Returns instance of GeoIp2\Database\Reader. 
	 * 
	 * @return GeoIp2\Database\Reader
	 */
	public function getReader()
	{
		return static::$reader;
	}
}