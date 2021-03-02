<?php

namespace DeepWebSolutions\Framework\Utilities\AdminNotices\Stores;

use DeepWebSolutions\Framework\Utilities\AdminNotices\AdminNoticeInterface;
use DeepWebSolutions\Framework\Utilities\AdminNotices\AdminNoticesStoreInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Store for admin notices stored in the user's meta table.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\AdminNotices\Stores
 */
class UserMetaStoreAdmin implements AdminNoticesStoreInterface {
	// region FIELDS AND CONSTANTS

	/**
	 * The name of the key in the user meta table to store the notices.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string
	 */
	protected string $meta_key;

	// endregion

	// region MAGIC METHODS

	/**
	 * UserMetaStore constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string      $meta_key       The name of the key in the user meta table to store the notices.
	 */
	public function __construct( string $meta_key ) {
		$this->meta_key = $meta_key;
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
		return 'user-meta';
	}

	/**
	 * Returns all the stored notices.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $params     Should contain ID of the user to retrieve the notices for.
	 *
	 * @return  AdminNoticeInterface[]
	 */
	public function get_notices( array $params ): array {
		$params = wp_parse_args( $params, array( 'user_id' => get_current_user_id() ) );
		return (array) get_user_meta( $params['user_id'], $this->meta_key, true );
	}

	// endregion

	// region METHODS

	/**
	 * Adds one or more notices to the store.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array                $params     Should contain ID of the user for which the notices should be added.
	 * @param   AdminNoticeInterface ...$notices Notice(s) to add.
	 *
	 * @return  bool    Whether the operation was successful or not.
	 */
	public function add_notice( array $params, AdminNoticeInterface ...$notices ): bool {
		$params           = wp_parse_args( $params, array( 'user_id' => get_current_user_id() ) );
		$existing_notices = $this->get_notices( $params );

		foreach ( $notices as $notice ) {
			$existing_notices[ $notice->get_handle() ] = $notice;
		}

		return update_user_meta(
			$params['user_id'],
			$this->meta_key,
			$existing_notices
		);
	}

	/**
	 * Removes a notice from the store.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $handle     Handle of the notice to remove.
	 * @param   array   $params     Should contain ID of the user for which the notice should be removed.
	 *
	 * @return  bool    Whether the operation was successful or not.
	 */
	public function remove_notice( string $handle, array $params ): bool {
		$params  = wp_parse_args( $params, array( 'user_id' => get_current_user_id() ) );
		$notices = $this->get_notices( $params );

		if ( isset( $notices[ $handle ] ) ) {
			unset( $notices[ $handle ] );

			return empty( $notices )
				? delete_user_meta( $params['user_id'], $this->meta_key )
				: update_user_meta(
					$params['user_id'],
					$this->meta_key,
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
	 * @param   array   $params     Should contain ID of the user for which the notices should be counted.
	 *
	 * @return  int
	 */
	public function count_notices( array $params ): int {
		$params = wp_parse_args( $params, array( 'user_id' => get_current_user_id() ) );
		return count( $this->get_notices( $params ) );
	}

	// endregion
}
