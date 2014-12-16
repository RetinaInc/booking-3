<?php

use Vmikki\Booking\Models\Room;

class RoomTest extends PHPUnit_Framework_TestCase
{
	public function testConstruct()
	{
		$room = new Room(1, 2, 'name');
		$this->assertEquals(1, $room->getId());
		$this->assertEquals(2, $room->getHouseId());
		$this->assertEquals('name', $room->getName());
	}

	public function invalidDataProvider()
	{
		return array(
			array('not numeric', 'not numeric', 'name'),
			array(true, 2, 'name'),
			array(1, 'not numeric', 'name'),
			array(3, 'not numeric', 'name'),
			array(3, 3, null),
			array(3, 2, '')
		);
	}

	/**
	 * @param int    $id
	 * @param int    $houseId
	 * @param string $name
	 *
	 * @dataProvider invalidDataProvider
	 * @expectedException InvalidArgumentException
	 */
	public function testWithInvalidData($id, $houseId, $name)
	{
		$room = new Room($id, $houseId, $name);
	}
}