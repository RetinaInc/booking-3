<?php

namespace Vmikki\Booking\Models;

use DateTime;
use InvalidArgumentException;

/**
 * Class Booking
 *
 * This class stores booking data.
 *
 * @package Vmikki\Booking\Models
 */
class Booking
{
	/** @var int */
	private $id;

	/** @var Tenant */
	private $tenant;

	/** @var Room */
	private $room;

	/** @var DateTime */
	private $startDate;

	/** @var DateTime */
	private $endDate;

	public function __construct($id, Tenant $tenant, Room $room, DateTime $startDate, DateTime $endDate)
	{
		if (!is_numeric($id) && !is_null($id)) {
			throw new InvalidArgumentException('Booking ID must be numeric or null');
		}

		$this->id = $id;
		$this->tenant = $tenant;
		$this->room = $room;
		$this->startDate = $startDate;
		$this->endDate = $endDate;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return Tenant
	 */
	public function getTenant()
	{
		return $this->tenant;
	}

	/**
	 * @return Room
	 */
	public function getRoom()
	{
		return $this->room;
	}

	/**
	 * @return DateTime
	 */
	public function getStartDate()
	{
		return $this->startDate;
	}

	/**
	 * @return DateTime
	 */
	public function getEndDate()
	{
		return $this->endDate;
	}

	/**
	 * @param DateTime $startDate
	 */
	public function setStartDate(DateTime $startDate)
	{
		$this->startDate = $startDate;
	}

	/**
	 * @param DateTime $endDate
	 */
	public function setEndDate(DateTime $endDate)
	{
		$this->endDate = $endDate;
	}
}
