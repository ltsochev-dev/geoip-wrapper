<?php namespace Zcard\GeoIP;

use InvalidArgumentException;
use UnexpectedValueException;

class Wrapper {

	/**
	 * The GeoIP Wrapper version
	 * 
	 * @var string
	 */
	const VERSION = '1.0.0';

	/**
	 * User-Defined settings container
	 * 
	 * @var array
	 */
	private $config = array();

	/**
	 * Default GeoIP Wrapper Settings
	 * 
	 * @property-read string $driver 
	 * @property-read string $maxmind_db The absolute path to the GeoIP2 City database
	 * 
	 * @var array
	 */
	private $defaultSettings = array(
		'driver'		=> 'maxmind',
		'maxmind_db'	=> '/usr/local/share/GeoIP/GeoIP2-City.mmdb',
	);

	/**
	 * List of available embedded drivers
	 * 
	 * @var array
	 */
	private $drivers = array(
		'maxmind'	=> 'Zcard\\GeoIP\\Drivers\\MaxMindDB',
		'mysql'		=> 'Zcard\\GeoIP\\Drivers\\MySQL',
		'test'		=> 'Zcard\\GeoIP\\Drivers\\TestDriver',
	);

	/**
	 * Instance of the currently selected driver
	 * 
	 * @var Driver
	 */
	private $driver;

	/**
	 * Creates a new GeoIP Wrapper instance
	 * 
	 * @param array $config
	 * @return void
	 */
	public function __construct(array $config = array())
	{
		$this->config = $config;
		$this->initDriver();
	}

	/**
	 * Retrieves the city of the given IP address
	 * 
	 * @throws InvalidArgumentException
	 * @param string $ipAddress
	 * @return Zcard\GeoIP\Result
	 */
	public function getLocation($ipAddress)
	{
		$ipAddress = trim($ipAddress);

		if ( !$this->isValidIp($ipAddress) )
		{
			throw new InvalidArgumentException("Invalid IP Address given to getLocation method");
		}

		return $this->createResult($this->driver->ip2city($ipAddress));
	}

	/**
	 * Retrieves the city of the given IP address
	 * but does not wrap the driver output in Result object.
	 * Use with clarity!
	 * 
	 * @throws InvalidArgumentException
	 * @param string $ipAddress
	 * @return mixed
	 */
	public function getLocationRaw($ipAddress)
	{
		$ipAddress = trim($ipAddress);
		
		if ( !$this->isValidIp($ipAddress) )
		{
			throw new InvalidArgumentException("Invalid IP Address given to getLocation method");
		}

		return $this->driver->ip2city($ipAddress);
	}

	/**
	 * Retrieves the instance of the current
	 * wrapper driver
	 * 
	 * @return mixed
	 */
	public function getDriver()
	{
		return $this->driver;
	}

	/**
	 * Checks whether a given IP Address is valid or
	 * not. Supports IPv4 and IPv6
	 * 
	 * @param string $ipAddress IPv4 or IPv6 IP Address
	 * @return bool
	 */
	public function isValidIp($ipAddress)
	{
		return (bool)filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE);
	}

	/**
	 * Initializes the driver provided by the settings
	 * 
	 * @return void
	 */
	private function initDriver()
	{
		if ( !is_null($this->driver) )
		{
			return;
		}

		$driverName = strtolower($this->getSetting('driver'));

		if ( !array_key_exists($driverName, $this->drivers) )
		{
			throw new InvalidArgumentException("Non-existing driver passed to GeoIP Wrapper ('{$driverName}').");
		}

		$this->driver = new $this->drivers[$driverName]($this->getSettings());
	}
	
	/**
	 * Retrieve a setting or, incase its not found,
	 * use a default setting.
	 * 
	 * @param mixed $key
	 * @return mixed
	 */
	private function getSetting($key)
	{
		$setting = NULL;

		if ( isset($this->config[$key]) && !empty($this->config[$key]) )
		{
			return $this->config[$key];
		}

		// Check for default setting of $key type
		if ( isset($this->defaultSettings[$key]) )
		{
			return $this->defaultSettings[$key];
		}

		return NULL;
	}

	/**
	 * Retrieves all the settings from the wrapper
	 * 
	 * @return array
	 */
	private function getSettings()
	{
		$settings = array();
		foreach($this->defaultSettings as $key => $value)
		{
			$settings[$key] = $this->getSetting($key);
		}

		return $settings;
	}

	/**
	 * Creates an instance of Result object
	 * by finding out the type of passed variable
	 * and creating the object from that.
	 * 
	 * @throws UnexpectedValueException
	 * @param mixed $data
	 * @return Zcard\GeoIP\Result
	 */
	private function createResult($data)
	{
		$dataType = gettype($data);

		switch ($dataType) 
		{
			case 'array':
				return new Result($data);
				break;
			case 'object':
				return $this->createFromObject($data);
				break;
			default:
				throw new UnexpectedValueException("Unexpected result value. (#1)");
				break;
		}
	}

	/**
	 * Creates an instance of Result object by
	 * parsing the passed object into the method.
	 * 
	 * @throws UnexpectedValueException
	 * @param mixed $data
	 * @return Zcard\GeoIP\Result
	 */
	private function createFromObject($data)
	{
		$class = get_class($data);
		
		switch ($class) 
		{
			case 'GeoIp2\Model\City':
				return Result::createFromMaxMindCity($data);
				break;
			case 'GeoIp2\Model\Country':
				return Result::createFromMaxMindCountry($data);
				break;
			case 'Database': // @todo
				return Result::createFromPdo($data);
				break;
			default:
				throw new UnexpectedValueException("Unexpected result value. (#2)");
				break;
		}
	}
}