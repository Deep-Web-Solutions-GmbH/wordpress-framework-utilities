<?php

namespace DeepWebSolutions\Framework\Utilities\AdminNotices\Stores;

use DeepWebSolutions\Framework\Utilities\AdminNotices\AdminNoticeInterface;
use DeepWebSolutions\Framework\Utilities\AdminNotices\AdminNoticesStoreInterface;

\defined( 'ABSPATH' ) || exit;

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
		$params = $this->parse_params( $params );
		return \get_user_meta( $params['user_id'], $this->meta_key, true ) ?: array(); // phpcs:ignore
	}

	// endregion

	// region METHODS

	/**
	 * Adds a notice to the store. If a notice with the same handle exists already, it will fail.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   AdminNoticeInterface    $notice     Notice to add.
	 * @param   array                   $params     Should contain ID of the user for which the notices should be added.
	 *
	 * @return  bool    Whether the operation was successful or not.
	 */
	public function add_notice( AdminNoticeInterface $notice, array $params ): bool {
		return \is_null( $this->get_notice( $notice->get_handle(), $params ) )
			? $this->update_notice( $notice, $params )
			: false;
	}

	/**
	 * Retrieves a notice from the store.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $handle     Handle of the notice to retrieve.
	 * @param   array   $params     Any parameters needed to retrieve the notice. Defaults need to pertain to the current user.
	 *
	 * @return  AdminNoticeInterface|null
	 */
	public function get_notice( string $handle, array $params ): ?AdminNoticeInterface {
		return $this->get_notices( $params )[ $handle ] ?? null;
	}

	/**
	 * Updates (or adds if it doesn't exist) a notice in the store.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   AdminNoticeInterface    $notice         Notice to add or update.
	 * @param   array                   $params         Any parameters needed to store the notice. Defaults need to pertain to the current user.
	 *
	 * @return  bool    Whether the operation was successful or not.
	 */
	public function update_notice( AdminNoticeInterface $notice, array $params ): bool {
		$params           = $this->parse_params( $params );
		$existing_notices = $this->get_notices( $params );

		$existing_notices[ $notice->get_handle() ] = $notice;

		return \update_user_meta(
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
		$params  = $this->parse_params( $params );
		$notices = $this->get_notices( $params );

		if ( isset( $notices[ $handle ] ) ) {
			unset( $notices[ $handle ] );

			return empty( $notices )
				? \delete_user_meta( $params['user_id'], $this->meta_key )
				: \update_user_meta(
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
		$params = $this->parse_params( $params );
		return \count( $this->get_notices( $params ) );
	}

	// endregion

	// region HELPERS

	/**
	 * Ensures that the default parameters are set.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $params     Parameters to parse.
	 *
	 * @return  array
	 */
	protected function parse_params( array $params ): array {
		return \wp_parse_args( $params, array( 'user_id' => \get_current_user_id() ) );
	}

	// endregion
}
