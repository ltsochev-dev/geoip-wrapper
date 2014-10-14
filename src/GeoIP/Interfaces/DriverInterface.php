<?php namespace Zcard\GeoIP\Interfaces;

interface Driver {

	/**
	 * Creates a driver instance
	 * 
	 * @param array $config
	 * @return void
	 */
	public function __construct(array $config);

	/**
	 * Pulls the city from a MaxMind
	 * binary database
	 * 
	 * @param string $ipAddress
	 * @return mixed
	 */
	public function ip2city($ipAddress);

	/**
	 * Pulls the country from a MaxMind
	 * binary database
	 * 
	 * @param string $ipAddress
	 * @return mixed
	 */
	public function ip2country($ipAddress);
}