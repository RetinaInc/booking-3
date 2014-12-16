<?php

namespace Vmikki\Booking\Repositories;
use Vmikki\Booking\Models\Tenant;

/**
 * Interface TenantRepositoryInterface for handling tenant data.
 *
 * @package Vmikki\Booking\Repositories
 */
interface TenantRepositoryInterface
{
	/**
	 * @param int $tenantId
	 *
	 * @return Tenant
	 */
	public function getById($tenantId);
}
