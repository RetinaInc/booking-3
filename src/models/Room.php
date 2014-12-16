<?php

namespace Vmikki\Booking\Models;

use InvalidArgumentException;

/**
 * Class Room
 *
 * This class stores room data.
 *
 * @package Vmikki\Booking\Models
 */
class Room
{
	/** @var int */
	private $id;

	/** @var int */
	private $houseId;

	/** @var string */
	private $name;

	public function __construct($id, $houseId, $name)
	{
		if (!is_numeric($id) && !is_null($id)) {
			throw new InvalidArgumentException('Room ID must be numeric or null');
		}

		if (!is_numeric($houseId) && !is_null($houseId)) {
			throw new InvalidArgumentException('House ID must be numeric or null');
		}

		if (empty($name)) {
			throw new InvalidArgumentException('Room name must be not empty');
		}


		$this->id = $id;
		$this->houseId = $houseId;
		$this->name = $name;
	}


	/**
	 * @return int
	 */
	public function getHouseId()
	{
		return $this->houseId;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}
}
