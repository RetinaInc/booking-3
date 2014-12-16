<?php

namespace Vmikki\Booking\Repositories;

use Vmikki\Booking\Models\Booking;

/**
 * Interface BookingRepositoryInterface for handling booking data.
 *
 * @package Vmikki\Booking\Repositories
 */
interface BookingRepositoryInterface
{
	/**
	 * @param int $bookingId
	 * @return null|Booking
	 */
	public function getById($bookingId);

	/**
	 * @param Booking $booking
	 * @return void
	 */
	public function save(Booking $booking);

	/**
	 * @param int $houseId
	 * @return Booking[]
	 */
	public function getByHouse($houseId);

	/**
	 * @param int $roomId
	 * @return Booking[]
	 */
	public function getByRoom($roomId);

	/**
	 * @param int $tenantId
	 * @return Booking[]
	 */
	public function getByTenant($tenantId);
}
