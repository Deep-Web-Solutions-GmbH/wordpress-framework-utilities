<?php

namespace DeepWebSolutions\Framework\Utilities\AdminNotices;

/**
 * Basic implementation of the admin-notices-stores-container-aware interface.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\AdminNotices
 */
trait AdminNoticesStoresContainerAwareTrait {
	// region FIELDS AND CONSTANTS

	/**
	 * Admin notices store factory instance.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     AdminNoticesStoresContainer
	 */
	protected AdminNoticesStoresContainer $notices_store_factory;

	// endregion

	// region GETTERS

	/**
	 * Gets the current admin notices store factory instance set on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  AdminNoticesStoresContainer
	 */
	public function get_admin_notices_store_factory(): AdminNoticesStoresContainer {
		return $this->notices_store_factory;
	}

	// endregion

	// region SETTERS

	/**
	 * Sets an admin notices store factory instance on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   AdminNoticesStoresContainer $store_factory Admin notices store factory instance to use from now on.
	 */
	public function set_admin_notices_store_factory( AdminNoticesStoresContainer $store_factory ) {
		$this->notices_store_factory = $store_factory;
	}

	// endregion

	// region METHODS

	/**
	 * Wrapper around the factory's own method.
	 *
	 * @param   string  $name   The name of the checker.
	 *
	 * @return  AdminNoticesStoreInterface
	 */
	public function get_admin_notices_store( string $name ): AdminNoticesStoreInterface {
		return $this->get_admin_notices_store_factory()->get_store( $name );
	}

	// endregion
}
