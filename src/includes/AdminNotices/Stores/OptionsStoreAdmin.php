<?php

namespace DeepWebSolutions\Framework\Utilities\AdminNotices\Stores;

use DeepWebSolutions\Framework\Utilities\AdminNotices\AdminNoticeInterface;
use DeepWebSolutions\Framework\Utilities\AdminNotices\AdminNoticesStoreInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Store for admin notices stored in the options table.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\AdminNotices\Stores
 */
class OptionsStoreAdmin implements AdminNoticesStoreInterface {
	// region FIELDS AND CONSTANTS

	/**
	 * The name of the key in the options table to store the notices.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string
	 */
	protected string $option_key;

	// endregion

	// region MAGIC METHODS

	/**
	 * OptionsStore constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string      $option_key     The name of the key in the options table to store the notices.
	 */
	public function __construct( string $option_key ) {
		$this->option_key = $option_key;
	}

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
		return 'options';
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
		return get_option( $this->option_key, array() );
	}

	// endregion

	// region METHODS

	/**
	 * Adds a notice to the store. If a notice with the same handle exists already, it will fail.
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
		return is_null( $this->get_notice( $notice->get_handle() ) )
			? $this->update_notice( $notice, $params )
			: false;
	}

	/**
	 * Retrieves a notice from the store.
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
		return $this->get_notices()[ $handle ] ?? null;
	}

	/**
	 * Updates (or adds if it doesn't exist) a notice in the store.
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
		$existing_notices = $this->get_notices();

		$existing_notices[ $notice->get_handle() ] = $notice;

		return update_option(
			$this->option_key,
			$existing_notices
		);
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
		$notices = $this->get_notices();

		if ( isset( $notices[ $handle ] ) ) {
			unset( $notices[ $handle ] );

			return empty( $notices )
				? delete_option( $this->option_key )
				: update_option(
					$this->option_key,
					$notices
				);
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
