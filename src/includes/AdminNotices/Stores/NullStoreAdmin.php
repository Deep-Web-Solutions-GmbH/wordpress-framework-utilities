<?php

namespace DeepWebSolutions\Framework\Utilities\AdminNotices\Stores;

use DeepWebSolutions\Framework\Utilities\AdminNotices\AdminNoticeInterface;
use DeepWebSolutions\Framework\Utilities\AdminNotices\AdminNoticesStoreInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Noop store.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\AdminNotices\Stores
 */
class NullStoreAdmin implements AdminNoticesStoreInterface {
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
		return 'null';
	}

	/**
	 * Returns an empty array.
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
		return array();
	}

	// endregion

	// region METHODS

	/**
	 * Returns false.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 *
	 * @param   AdminNoticeInterface    $notice     Notice to add.
	 * @param   array                   $params     NOT USED BY THIS STORE.
	 *
	 * @return  bool    Whether the operation was successful or not.
	 */
	public function add_notice( AdminNoticeInterface $notice, array $params = array() ): bool {
		return false;
	}

	/**
	 * Returns null.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 *
	 * @param   string  $handle     Handle of the notice to retrieve.
	 * @param   array   $params     NOT USED BY THIS STORE.
	 *
	 * @return  AdminNoticeInterface|null
	 */
	public function get_notice( string $handle, array $params = array() ): ?AdminNoticeInterface {
		return null;
	}

	/**
	 * Returns false.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 *
	 * @param   AdminNoticeInterface    $notice         Notice to add or update.
	 * @param   array                   $params         NOT USED BY THIS STORE.
	 *
	 * @return  bool    Whether the operation was successful or not.
	 */
	public function update_notice( AdminNoticeInterface $notice, array $params = array() ): bool {
		return false;
	}

	/**
	 * Returns false.
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
		return false;
	}

	/**
	 * Returns 0.
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
		return 0;
	}

	// endregion
}
