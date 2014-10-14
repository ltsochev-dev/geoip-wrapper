<?php namespace Zcard\GeoIP\Drivers;

use PDO;
use PDOException;
use Zcard\GeoIP\Interfaces\Driver;

class MySQL implements Driver {

	protected $pdo;

	public function __construct(array $config = array())
	{

	}
	
	public function ip2city($ipAddress)
	{

	}
	
	public function ip2country($ipAddress)
	{

	}
}