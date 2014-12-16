<?php

namespace Vmikki\Booking\Models;

use InvalidArgumentException;

/**
 * Class House
 *
 * This class stores house data.
 *
 * @package Vmikki\Booking\Models
 */
class House
{
	/** @var int */
	private $id;

	/** @var string */
	private $name;

	function __construct($id, $name)
	{
		if (!is_numeric($id) && !is_null($id)) {
			throw new InvalidArgumentException('House ID must be numeric or null');
		}

		if (empty($name)) {
			throw new InvalidArgumentException('House name must be not empty');
		}

		$this->id = $id;
		$this->name = $name;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
}