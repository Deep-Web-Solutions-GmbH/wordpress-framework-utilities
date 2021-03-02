<?php

namespace DeepWebSolutions\Framework\Utilities\AdminNotices;

/**
 * Describes an admin-notices-store-factory-aware instance.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\AdminNotices
 */
interface AdminNoticesStoreFactoryAwareInterface {
	/**
	 * Gets the current admin notices store factory instance set on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  AdminNoticesStoreFactory
	 */
	public function get_admin_notices_store_factory(): AdminNoticesStoreFactory;

	/**
	 * Sets an admin notices store factory instance on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   AdminNoticesStoreFactory        $store_factory          Admin notices store factory instance to use from now on.
	 */
	public function set_admin_notices_store_factory( AdminNoticesStoreFactory $store_factory ): void;
}
