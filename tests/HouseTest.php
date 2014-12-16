<?php

use Vmikki\Booking\Models\House;

class HouseTest extends PHPUnit_Framework_TestCase
{
	public function testConstruct()
	{
		$house = new House(2, 'house name');
		$this->assertEquals(2, $house->getId());
		$this->assertEquals('house name', $house->getName());
	}

	public function invalidDataProvider()
	{
		return array(
			array('not numeric', 'name'),
			array(true, 'name'),
			array(3, ''),
			array(3, null)
		);
	}

	/**
	 * @param int    $id
	 * @param string $name
	 *
	 * @dataProvider invalidDataProvider
	 * @expectedException InvalidArgumentException
	 */
	public function testWithInvalidData($id, $name)
	{
		$house = new House($id, $name);
	}
}
