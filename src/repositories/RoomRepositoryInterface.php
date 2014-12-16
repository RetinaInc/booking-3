<?php

namespace Vmikki\Booking\Repositories;
use Vmikki\Booking\Models\Room;

/**
 * Interface RoomRepositoryInterface for handling room data.
 *
 * @package Vmikki\Booking\Repositories
 */
interface RoomRepositoryInterface
{
	/**
	 * @param int $roomId
	 *
	 * @return Room
	 */
	public function getById($roomId);
}
