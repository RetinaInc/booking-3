<?php

namespace Vmikki\Booking\Repositories;
use Vmikki\Booking\Models\House;

/**
 * Interface HouseRepositoryInterface for handling house data.
 *
 * @package Vmikki\Booking\Repositories
 */
interface HouseRepositoryInterface
{
	/**
	 * @param int $roomId
	 *
	 * @return House
	 */
	public function getById($roomId);
}
