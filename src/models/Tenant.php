<?php

namespace Vmikki\Booking\Models;

use InvalidArgumentException;

/**
 * Class Tenant
 *
 * This class stores tenant data.
 *
 * @package Vmikki\Booking\Models
 */
class Tenant
{
	/** @var int */
	private $id;

	/** @var string */
	private $name;

	function __construct($id, $name)
	{
		if (!is_numeric($id) && !is_null($id)) {
			throw new InvalidArgumentException('Tenant ID must be numeric or null');
		}

		if (empty($name)) {
			throw new InvalidArgumentException('Tenant name must be not empty');
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
