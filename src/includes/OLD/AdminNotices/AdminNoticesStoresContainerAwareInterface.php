<?php

namespace DeepWebSolutions\Framework\Utilities\AdminNotices;

\defined( 'ABSPATH' ) || exit;

/**
 * Describes an admin-notices-stores-container-aware instance.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\AdminNotices
 */
interface AdminNoticesStoresContainerAwareInterface {
	/**
	 * Gets the current admin notices store factory instance set on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  AdminNoticesStoresContainer
	 */
	public function get_admin_notices_store_factory(): AdminNoticesStoresContainer;

	/**
	 * Sets an admin notices store factory instance on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   AdminNoticesStoresContainer $store_factory Admin notices store factory instance to use from now on.
	 */
	public function set_admin_notices_store_factory( AdminNoticesStoresContainer $store_factory );
}
