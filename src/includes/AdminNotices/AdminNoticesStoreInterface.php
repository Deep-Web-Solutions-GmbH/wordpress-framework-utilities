<?php

namespace DeepWebSolutions\Framework\Utilities\AdminNotices;

defined( 'ABSPATH' ) || exit;

/**
 * Describes a way to store and retrieve admin notices.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\AdminNotices
 */
interface AdminNoticesStoreInterface {
	// region GETTERS

	/**
	 * Returns the store's type.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	public function get_type(): string;

	/**
	 * Returns all the stored notices.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $params     Any parameters needed to retrieve the notices. Defaults need to pertain to the current user.
	 *
	 * @return  AdminNoticeInterface[]
	 */
	public function get_notices( array $params ): array;

	// endregion

	// region METHODS

	/**
	 * Adds a notice to the store. If a notice with the same handle exists already, it will fail.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   AdminNoticeInterface    $notice         Notice to add.
	 * @param   array                   $params         Any parameters needed to store the notice. Defaults need to pertain to the current user.
	 *
	 * @return  bool    Whether the operation was successful or not.
	 */
	public function add_notice( AdminNoticeInterface $notice, array $params ): bool;

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
	public function get_notice( string $handle, array $params ): ?AdminNoticeInterface;

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
	public function update_notice( AdminNoticeInterface $notice, array $params ): bool;

	/**
	 * Removes a notice from the store.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $handle     Handle of the notice to remove.
	 * @param   array   $params     Any parameters needed to remove the notice. Defaults need to pertain to the current user.
	 *
	 * @return  bool    Whether the operation was successful or not.
	 */
	public function remove_notice( string $handle, array $params ): bool;

	/**
	 * Returns the number of notices in the store.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $params     Any parameters needed to count the notices. Defaults need to pertain to the current user.
	 *
	 * @return  int
	 */
	public function count_notices( array $params ): int;

	// endregion
}
