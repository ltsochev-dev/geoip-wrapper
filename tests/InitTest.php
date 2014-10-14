<?php

use Zcard\GeoIP\Wrapper;

class IPTest extends PHPUnit_Framework_TestCase 
{
	/**
	 * Starting the wrapper without config may raise an
	 * exception for non existing database
	 * 
	 * @expectedException UnexpectedValueException
	 */
	public function testInitNoConfig()
	{
		$app = new Wrapper();
	}

	/**
	 * Trying to open invalid database should fire up
	 * an exception
	 * 
	 * @expectedException MaxMind\Db\Reader\InvalidDatabaseException
	 */
	public function testMaxMindDriverWithExistingFileThatIsNotDatabase()
	{
		$app = new Wrapper(array(
			'driver'	=> 'maxmind',
			'maxmind_db'=> '/etc/passwd',
		));
	}

	public function testInitLowercaseMySQL()
	{
		$app = new Wrapper(array('driver' => 'mysql'));
	}

	public function testInitMixedCaseMySQL()
	{
		$app = new Wrapper(array('driver' => 'MySQL'));
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testBadDriver()
	{
		$app = new Wrapper(array('driver' => 'Bad Driver'));
	}

	/**
	 * This function should test both ipv4 and ipv6
	 * without issues.
	 * 
	 */
	public function testIpAddressChecker()
	{
		$app = new Wrapper(array('driver' => 'test'));

		$ipList = array(
			'127.0.0.1' => true,
			'192.168.0.1' => false,
			'87.255.127.98' => true,
			'008.008.008.008' => false,
			'256.256.256.256' => false,
			'255.255.255.255' => true,
			'0.0.0.0' => true,

			'2607:f0d0:1002:51::4' => true,
			'2607:f0d0:1002:0051:0000:0000:0000:0004' => true,
		);

		foreach($ipList as $ip => $assertion)
		{
			if ( $assertion )
			{
				$this->assertTrue($app->isValidIp($ip), 'Address: ' . $ip);
			}
			else
			{
				$this->assertFalse($app->isValidIp($ip), 'Address: ' . $ip);
			}
		}
	}
}