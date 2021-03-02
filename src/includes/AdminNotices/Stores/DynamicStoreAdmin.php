<?php

namespace DeepWebSolutions\Framework\Utilities\AdminNotices\Stores;

use DeepWebSolutions\Framework\Utilities\AdminNotices\AdminNoticeInterface;
use DeepWebSolutions\Framework\Utilities\AdminNotices\AdminNoticesStoreInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Store for dynamically registered admin notices at runtime.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\AdminNotices\Stores
 */
class DynamicStoreAdmin implements AdminNoticesStoreInterface {
	// region FIELDS AND CONSTANTS

	/**
	 * Collection of all dynamically registered notices.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     AdminNoticeInterface[]
	 */
	protected array $notices = array();

	// endregion

	// region GETTERS

	/**
	 * Returns the store's type.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	public function get_type(): string {
		return 'dynamic';
	}

	/**
	 * Returns all the stored notices.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 *
	 * @param   array   $params     NOT USED BY THIS STORE.
	 *
	 * @return  AdminNoticeInterface[]
	 */
	public function get_notices( array $params = array() ): array {
		return $this->notices;
	}

	// endregion

	// region METHODS

	/**
	 * Adds one or more notices to the store.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 *
	 * @param   array                $params     NOT USED BY THIS STORE.
	 * @param   AdminNoticeInterface ...$notices Notice(s) to add.
	 *
	 * @return  bool    Whether the operation was successful or not.
	 */
	public function add_notice( array $params, AdminNoticeInterface ...$notices ): bool {
		foreach ( $notices as $notice ) {
			$this->notices[ $notice->get_handle() ] = $notice;
		}

		return true;
	}

	/**
	 * Removes a notice from the store.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 *
	 * @param   string  $handle     Handle of the notice to remove.
	 * @param   array   $params     NOT USED BY THIS STORE.
	 *
	 * @return  bool    Whether the operation was successful or not.
	 */
	public function remove_notice( string $handle, array $params = array() ): bool {
		if ( isset( $this->notices[ $handle ] ) ) {
			unset( $this->notices[ $handle ] );
			return true;
		}

		return false;
	}

	/**
	 * Returns the number of notices in the store.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 *
	 * @param   array   $params     NOT USED BY THIS STORE.
	 *
	 * @return  int
	 */
	public function count_notices( array $params = array() ): int {
		return count( $this->get_notices() );
	}

	// endregion
}
