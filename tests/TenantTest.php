<?php

use Vmikki\Booking\Models\Tenant;

class TenantTest extends PHPUnit_Framework_TestCase
{
	public function testConstruct()
	{
		$tenant = new Tenant(3, 'tenant name');
		$this->assertEquals(3, $tenant->getId());
		$this->assertEquals('tenant name', $tenant->getName());
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
		$tenant = new Tenant($id, $name);
	}
}
