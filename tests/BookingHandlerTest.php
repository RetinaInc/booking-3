<?php

use Vmikki\Booking\BookingHandler;
use Vmikki\Booking\Models\Booking;
use Vmikki\Booking\Models\House;
use Vmikki\Booking\Models\Room;
use Vmikki\Booking\Models\Tenant;

class BookingHandlerTest extends PHPUnit_Framework_TestCase
{
	/** @var BookingHandler */
	private $bookingHandler;

	/** @var PHPUnit_Framework_MockObject_MockObject */
	private $bookingRepositoryMock;

	/** @var PHPUnit_Framework_MockObject_MockObject */
	private $tenantRepositoryMock;

	/** @var PHPUnit_Framework_MockObject_MockObject */
	private $houseRepositoryMock;

	/** @var PHPUnit_Framework_MockObject_MockObject */
	private $roomRepositoryMock;

	/** @var Tenant */
	private $testTenant;

	/** @var Room */
	private $testRoom;

	/** @var House */
	private $testHouse;

	/** @var Booking[] */
	private $testExistingBookings = array();

	protected function setUp()
	{
		parent::setUp();

		$this->initializeTestModels();

		$this->bookingHandler = new BookingHandler(
			$this->getBookingRepositoryMock(),
			$this->getTenantRepositoryMock(),
			$this->getHouseRepositoryMock(),
			$this->getRoomRepositoryMock()
		);
	}

	/**
	 * Create test models.
	 */
	protected function initializeTestModels()
	{
		$this->testTenant = new Tenant(2, 'test tenant');
		$this->testHouse = new House(3, 'test house');
		$this->testRoom = new Room(4, 3, 'test room');
		$startDateTime1 = (new DateTime())->add(new DateInterval('P1M'));
		$endDateTime1 = (new DateTime())->add(new DateInterval('P3M'));
		$startDateTime2 = (new DateTime())->add(new DateInterval('P4M'));
		$endDateTime2 = (new DateTime())->add(new DateInterval('P5M'));
		$this->testExistingBookings = array(
			new Booking(5, $this->testTenant, $this->testRoom, $startDateTime1, $endDateTime1),
			new Booking(11, $this->testTenant, $this->testRoom, $startDateTime2, $endDateTime2)
		);
	}

	private function getBookingRepositoryMock()
	{
		if (!($this->bookingRepositoryMock instanceof PHPUnit_Framework_MockObject_MockObject)) {
			$this->bookingRepositoryMock = $this->getMock('\Vmikki\Booking\Repositories\BookingRepositoryInterface');

			$this->bookingRepositoryMock
				->expects($this->any())
				->method('getById')
				->will($this->returnCallback(function ($param) {
					foreach ($this->testExistingBookings as $booking) {
						if ($param == $booking->getId()) {
							return $booking;
						}
					}
					return null;
				}));

			$this->bookingRepositoryMock
				->expects($this->any())
				->method('getByTenant')
				->will($this->returnCallback(function ($param) {
					$result = array();
					foreach ($this->testExistingBookings as $booking) {
						if ($param == $booking->getTenant()->getId()) {
							$result[] = $booking;
						}
					}
					return $result;
				}));

			$this->bookingRepositoryMock
				->expects($this->any())
				->method('getByRoom')
				->will($this->returnCallback(function ($param) {
					$result = array();
					foreach ($this->testExistingBookings as $booking) {
						if ($param == $booking->getRoom()->getId()) {
							$result[] = $booking;
						}
					}
					return $result;
				}));
		}

		return $this->bookingRepositoryMock;
	}

	private function getTenantRepositoryMock()
	{
		if (!($this->tenantRepositoryMock instanceof PHPUnit_Framework_MockObject_MockObject)) {
			$this->tenantRepositoryMock = $this->getMock('\Vmikki\Booking\Repositories\TenantRepositoryInterface');

			$this->tenantRepositoryMock
				->expects($this->any())
				->method('getById')
				->will($this->returnCallback(function ($param) {
					return $param == $this->testTenant->getId() ? $this->testTenant : null;
				}));
		}

		return $this->tenantRepositoryMock;
	}

	private function getHouseRepositoryMock()
	{
		if (!($this->houseRepositoryMock instanceof PHPUnit_Framework_MockObject_MockObject)) {
			$this->houseRepositoryMock = $this->getMock('\Vmikki\Booking\Repositories\HouseRepositoryInterface');

			$this->houseRepositoryMock
				->expects($this->any())
				->method('getById')
				->will($this->returnCallback(function ($param) {
					return $param == $this->testHouse->getId() ? $this->testHouse : null;
				}));
		}

		return $this->houseRepositoryMock;
	}

	private function getRoomRepositoryMock()
	{
		if (!($this->roomRepositoryMock instanceof PHPUnit_Framework_MockObject_MockObject)) {
			$this->roomRepositoryMock = $this->getMock('\Vmikki\Booking\Repositories\RoomRepositoryInterface');

			$this->roomRepositoryMock
				->expects($this->any())
				->method('getById')
				->will($this->returnCallback(function ($param) {
					return $param == $this->testRoom->getId() ? $this->testRoom : null;
				}));
		}

		return $this->roomRepositoryMock;
	}

	/** @expectedException \Vmikki\Booking\TenantNotFoundException */
	public function testBookRoomWithNonExistingTenant()
	{
		$this->bookingHandler->bookRoom(-2, $this->testRoom->getId(), new DateTime(), (new DateTime())->add(new DateInterval('P1M')));
	}

	/** @expectedException \Vmikki\Booking\TenantNotFoundException */
	public function testBookRoomWithNonExistingRoom()
	{
		$this->bookingHandler->bookRoom($this->testRoom->getId(), -2, new DateTime(), (new DateTime())->add(new DateInterval('P1M')));
	}

	public function invalidTimeRangeProvider()
	{
		return array(
			array(new DateTime(), new DateTime()), // Invalid
			array((new DateTime())->add(new DateInterval('P1D')), new DateTime()), // Invalid
			array(new DateTime(), (new DateTime())->sub(new DateInterval('P1D'))), // Invalid
			array(new DateTime(), (new DateTime())->add(new DateInterval('P4M'))), // overlapped with existing booking
			array(new DateTime(), (new DateTime())->add(new DateInterval('P2M'))), // overlapped with existing booking
			array(new DateTime(), (new DateTime())->add(new DateInterval('P1M1D'))), // overlapped with existing booking
			array((new DateTime())->add(new DateInterval('P1D')), (new DateTime())->add(new DateInterval('P2M'))), // overlapped with existing booking
			array((new DateTime())->add(new DateInterval('P1M2D')), (new DateTime())->add(new DateInterval('P2M'))), // overlapped with existing booking
			array((new DateTime())->add(new DateInterval('P1M2D')), (new DateTime())->add(new DateInterval('P5M'))), // overlapped with existing booking
		);
	}

	/**
	 * @param DateTime $start
	 * @param DateTime $end
	 *
	 * @dataProvider invalidTimeRangeProvider
	 * @expectedException \Vmikki\Booking\InvalidBookingException
	 */
	public function testBookingWithInvalidTimeRange(DateTime $start, DateTime $end)
	{
		$this->bookingHandler->bookRoom($this->testTenant->getId(), $this->testRoom->getId(), $start, $end);
	}

	public function testBookRoom()
	{
		$startDate = (new DateTime())->add(new DateInterval('P1D'));
		$endDate =(new DateTime())->add(new DateInterval('P1M'));

		$booking = new Booking(null, $this->testTenant, $this->testRoom, $startDate, $endDate);

		$this->getBookingRepositoryMock()
			->expects($this->once())
			->method('save')
			->with($booking)
			->will($this->returnValue($booking));

		$bookResult = $this->bookingHandler->bookRoom(
			$this->testTenant->getId(), $this->testRoom->getId(), $startDate, $endDate
		);

		$this->assertEquals($booking, $bookResult);
	}

	public function invalidBookingStartDateProvider()
	{
		return array(
			array(
				5, (new DateTime())->add(new DateInterval('P8M')), // starts later than it ends
				5, (new DateTime())->add(new DateInterval('P4M19D')), // overlapped with other booking
				5, (new DateTime())->add(new DateInterval('P5M')), // overlapped with other booking
				5, (new DateTime())->add(new DateInterval('P6M')), // overlapped with other booking
				11, (new DateTime())->add(new DateInterval('P10M')), // starts later than it ends
				11, (new DateTime())->add(new DateInterval('P2M')), // overlapped with other booking
				11, (new DateTime())->sub(new DateInterval('P2M')) // overlapped with other booking
			)
		);
	}

	/**
	 * @param int      $bookingId
	 * @param DateTime $start
	 *
	 * @dataProvider invalidBookingStartDateProvider
	 * @expectedException \Vmikki\Booking\InvalidBookingException
	 */
	public function testChangeStartDateWithInvalidTimeRange($bookingId, DateTime $start)
	{
		$this->bookingHandler->changeStartDate($bookingId, $start);
	}

	public function invalidBookingEndDateProvider()
	{
		return array(
			array(
				5, (new DateTime())->add(new DateInterval('P4M1D')), // overlapped with other booking
				5, (new DateTime())->add(new DateInterval('P10M')), // overlapped with other booking
				11, (new DateTime())->add(new DateInterval('P2M')), // overlapped with other booking
				11, (new DateTime())->sub(new DateInterval('P2M')) // overlapped with other booking
			)
		);
	}

	/**
	 * @param int      $bookingId
	 * @param DateTime $end
	 *
	 * @dataProvider invalidBookingEndDateProvider
	 * @expectedException \Vmikki\Booking\InvalidBookingException
	 */
	public function testChangeEndDateWithInvalidTimeRange($bookingId, DateTime $end)
	{
		$this->bookingHandler->changeEndDate($bookingId, $end);
	}

	public function validBookingStartDateProvider()
	{
		return array(
			array(
				5, (new DateTime())->add(new DateInterval('P1D')),
				5, (new DateTime())->add(new DateInterval('P2M4D')),
				11, (new DateTime())->add(new DateInterval('P3M')),
				11, (new DateTime())->add(new DateInterval('P3M11D'))
			)
		);
	}

	/**
	 * @param int      $id
	 * @param DateTime $start
	 *
	 * @dataProvider validBookingStartDateProvider
	 */
	public function testChangeStartDate($id, DateTime $start)
	{
		$this->bookingRepositoryMock
			->expects($this->once())
			->method('save');

		$this->bookingHandler->changeStartDate($id, $start);
	}

	public function validBookingEndDateProvider()
	{
		return array(
			array(
				5, (new DateTime())->add(new DateInterval('P1M1D')),
				5, (new DateTime())->add(new DateInterval('P3M4D')),
				11, (new DateTime())->add(new DateInterval('P3M10D')),
				11, (new DateTime())->add(new DateInterval('P4M1D'))
			)
		);
	}

	/**
	 * @param int      $id
	 * @param DateTime $end
	 *
	 * @dataProvider validBookingEndDateProvider
	 */
	public function testChangeEndDate($id, DateTime $end)
	{
		$this->bookingRepositoryMock
			->expects($this->once())
			->method('save');

		$this->bookingHandler->changeEndDate($id, $end);
	}
}
