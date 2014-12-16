<?php

use Vmikki\Booking\Models\Booking;
use Vmikki\Booking\Models\Room;
use Vmikki\Booking\Models\Tenant;

class BookingTest extends PHPUnit_Framework_TestCase
{
	public function testConstruct()
	{
		$tenant = new Tenant(11, 'tenant name');
		$room = new Room(4, 1, 'room name');
		$startDate = new DateTime();
		$endDate = (new DateTime())->add(new DateInterval('P1M'));
		$booking = new Booking(123, $tenant, $room, $startDate, $endDate);

		$this->assertEquals(123, $booking->getId());
		$this->assertEquals($tenant, $booking->getTenant());
		$this->assertEquals($startDate, $booking->getStartDate());
		$this->assertEquals($endDate, $booking->getEndDate());
	}

	public function testSetDates()
	{
		$booking = new Booking(
			123, new Tenant(1, 'test'), new Room(1, 2, 'test'),  new DateTime(), (new DateTime())->add(new DateInterval('P1M'))
		);

		$newStartDate = (new DateTime())->add(new DateInterval('P1D'));
		$booking->setStartDate($newStartDate);
		$this->assertEquals($newStartDate, $booking->getStartDate());

		$newEndDate = (new DateTime())->add(new DateInterval('P2M'));
		$booking->setEndDate($newEndDate);
		$this->assertEquals($newEndDate, $booking->getEndDate());
	}
}
