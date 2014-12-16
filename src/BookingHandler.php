<?php

namespace Vmikki\Booking;

use DateTime;
use Vmikki\Booking\Models\Booking;
use Vmikki\Booking\Models\Room;
use Vmikki\Booking\Models\Tenant;
use Vmikki\Booking\Repositories\BookingRepositoryInterface;
use Vmikki\Booking\Repositories\HouseRepositoryInterface;
use Vmikki\Booking\Repositories\RoomRepositoryInterface;
use Vmikki\Booking\Repositories\TenantRepositoryInterface;

/**
 * Class BookingHandler
 * This class is responsible to handle reservations.
 *
 * @package Vmikki\Booking
 */
class BookingHandler
{
	/**
	 * @var BookingRepositoryInterface
	 */
	private $bookingRepositoryInterface;

	/**
	 * @var TenantRepositoryInterface
	 */
	private $tenantRepositoryInterface;

	/**
	 * @var HouseRepositoryInterface
	 */
	private $houseRepositoryInterface;

	/**
	 * @var RoomRepositoryInterface
	 */
	private $roomRepositoryInterface;

	/**
	 * Constructor.
	 *
	 * @param BookingRepositoryInterface $bookingRepositoryInterface
	 * @param TenantRepositoryInterface  $tenantRepositoryInterface
	 * @param HouseRepositoryInterface   $houseRepositoryInterface
	 * @param RoomRepositoryInterface    $roomRepositoryInterface
	 */
	public function __construct(
		BookingRepositoryInterface $bookingRepositoryInterface,
		TenantRepositoryInterface $tenantRepositoryInterface,
		HouseRepositoryInterface $houseRepositoryInterface,
		RoomRepositoryInterface $roomRepositoryInterface
	) {
		$this->bookingRepositoryInterface = $bookingRepositoryInterface;
		$this->tenantRepositoryInterface = $tenantRepositoryInterface;
		$this->houseRepositoryInterface = $houseRepositoryInterface;
		$this->roomRepositoryInterface = $roomRepositoryInterface;
	}

	/**
	 * Book a room.
	 *
	 * @param int      $tenantId    The ID of the tenant.
	 * @param int      $roomId      The ID of the room.
	 * @param DateTime $startDate   The start date of the reservation.
	 * @param DateTime $endDate     The end date of the reservation.
	 *
	 * @throws InvalidBookingException
	 * @throws RoomNotFoundException
	 * @throws TenantNotFoundException
	 *
	 * @return Booking
	 */
	public function bookRoom($tenantId, $roomId, DateTime $startDate, DateTime $endDate)
	{
		$tenant = $this->getTenantById($tenantId);
		$room = $this->getRoomById($roomId);

		$booking = new Booking(null, $tenant, $room, $startDate, $endDate);

		$this->checkBooking($booking);

		$this->bookingRepositoryInterface->save($booking);

		return $booking;
	}

	/**
	 * Change the start date of the reservation.
	 *
	 * @param int      $bookingId
	 * @param DateTime $startDate
	 */
	public function changeStartDate($bookingId, DateTime $startDate)
	{
		$this->updateBooking($bookingId, function($booking) use ($startDate) {
			/** @var Booking $booking */
			$booking->setStartDate($startDate);
		});
	}

	/**
	 * Change the end date of the reservation.
	 *
	 * @param int      $bookingId
	 * @param DateTime $endDate
	 */
	public function changeEndDate($bookingId, $endDate)
	{
		$this->updateBooking($bookingId, function($booking) use ($endDate) {
			/** @var Booking $booking */
			$booking->setEndDate($endDate);
		});
	}

	/**
	 * Update a reservation.
	 *
	 * @param int      $bookingId        The ID of the booking.
	 * @param callable $modifyFunction   The function that should be applied on the Booking model.
	 *
	 * @throws BookingException
	 * @throws InvalidBookingException
	 */
	private function updateBooking($bookingId, \Closure $modifyFunction)
	{
		$booking = $this->getBookingById($bookingId);

		$modifyFunction($booking);

		$this->checkBooking($booking);

		$this->bookingRepositoryInterface->save($booking);
	}

	/**
	 * Check if a booking is valid (there's no overlap between existing reservations of the room and the tenant)
	 *
	 * @param Booking $newBooking   The new Booking instance.
	 *
	 * @throws InvalidBookingException
	 */
	private function checkBooking(Booking $newBooking)
	{
		if ($newBooking->getStartDate() >= $newBooking->getEndDate()) {
			throw new InvalidBookingException('Invalid time range');
		}

		$existingBookingsByTenant = $this->bookingRepositoryInterface->getByTenant($newBooking->getTenant()->getId());
		if ($this->isBookingTimeRangesOverlapped($newBooking, $existingBookingsByTenant)) {
			throw new InvalidBookingException('Tenant booking time ranges are overlapped');
		}

		$roomBookingsByRoom = $this->bookingRepositoryInterface->getByRoom($newBooking->getRoom()->getId());
		if ($this->isBookingTimeRangesOverlapped($newBooking, $roomBookingsByRoom)) {
			throw new InvalidBookingException('Room booking time ranges are overlapped');
		}
	}

	/**
	 * Check if there's an overlap between the new reservation and the existing ones.
	 *
	 * @param Booking   $newBooking         The new Booking instance.
	 * @param Booking[] $existingBookings   An array that contains the related reservations.
	 *
	 * @return bool   Return TRUE if there's an overlap, otherwise return FALSE.
	 */
	private function isBookingTimeRangesOverlapped(Booking $newBooking, array $existingBookings)
	{
		foreach ($existingBookings as $existingBooking) {
			/** @var Booking $existingBooking */
			if ($newBooking->getId() == $existingBooking->getId()) {
				continue;
			}
			if ($newBooking->getStartDate() < $existingBooking->getEndDate() && $existingBooking->getStartDate() < $newBooking->getEndDate()) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get a Room instance by ID or throw a RoomNotFoundException if not exists.
	 *
	 * @param int $roomId   The ID of the room.
	 *
	 * @return Room
	 * @throws RoomNotFoundException
	 */
	private function getRoomById($roomId)
	{
		$room = $this->roomRepositoryInterface->getById($roomId);

		if (!($room instanceof Room)) {
			throw new RoomNotFoundException();
		}
		return $room;
	}

	/**
	 * Get a Tenant instance by ID or throw an TenantNotFoundException if not exists.
	 *
	 * @param int $tenantId   The ID of the tenant.
	 *
	 * @return Tenant
	 * @throws TenantNotFoundException
	 */
	private function getTenantById($tenantId)
	{
		$tenant = $this->tenantRepositoryInterface->getById($tenantId);

		if (!($tenant instanceof Tenant)) {
			throw new TenantNotFoundException();
		}

		return $tenant;
	}

	/**
	 * Get a Booking instance by ID or throw an BookingException if not exists.
	 *
	 * @param int $bookingId   The ID of the booking.
	 *
	 * @return Booking
	 * @throws BookingException
	 */
	private function getBookingById($bookingId)
	{
		$booking = $this->bookingRepositoryInterface->getById($bookingId);
		if (is_null($booking)) {
			throw new BookingException('Booking not found');
		}

		return $booking;
	}
}
