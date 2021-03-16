<?php

namespace DeepWebSolutions\Framework\Utilities\AdminNotices;

use DeepWebSolutions\Framework\Utilities\AdminNotices\Stores\DynamicStoreAdmin;

\defined( 'ABSPATH' ) || exit;

/**
 * Admin notices store container.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\AdminNotices
 */
class AdminNoticesStoresContainer {
	// region FIELDS AND CONSTANTS

	/**
	 * Collection of instantiated stores.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     AdminNoticesStoreInterface[]
	 */
	protected array $stores = array();

	// endregion

	// region GETTERS

	/**
	 * Returns all instantiated stores.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  AdminNoticesStoreInterface[]
	 */
	public function get_stores(): array {
		return $this->stores;
	}

	// endregion

	// region METHODS

	/**
	 * Registers an already instantiated store with the factory.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string                      $name   The name of the store.
	 * @param   AdminNoticesStoreInterface  $store  The store instance.
	 */
	public function register_store( string $name, AdminNoticesStoreInterface $store ): void {
		$this->stores[ $name ] = $store;
	}

	/**
	 * Returns a store instance.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $name       The name of the store. Must match with the name used when registering the callback.
	 *
	 * @return  AdminNoticesStoreInterface
	 */
	public function get_store( string $name ): AdminNoticesStoreInterface {
		return $this->stores[ $name ] ?? new DynamicStoreAdmin();
	}

	// endregion
}
