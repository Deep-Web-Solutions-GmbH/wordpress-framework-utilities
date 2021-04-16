<?php

namespace DeepWebSolutions\Framework\Utilities\AdminNotices;

\defined( 'ABSPATH' ) || exit;

/**
 * Basic implementation of the admin-notices-service-aware interface.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\AdminNotices
 */
trait AdminNoticesServiceAwareTrait {
	// region FIELDS AND CONSTANTS

	/**
	 * Admin notices service for outputting admin notices.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     AdminNoticesService
	 */
	protected AdminNoticesService $admin_notices_service;

	// endregion

	// region GETTERS

	/**
	 * Gets the current admin notices service instance set on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  AdminNoticesService
	 */
	public function get_admin_notices_service(): AdminNoticesService {
		return $this->admin_notices_service;
	}

	// endregion

	// region SETTERS

	/**
	 * Sets an admin notices service instance on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   AdminNoticesService     $notices_service    Admin notices service instance to use from now on.
	 */
	public function set_admin_notices_service( AdminNoticesService $notices_service ) {
		$this->admin_notices_service = $notices_service;
	}

	// endregion

	// region METHODS

	/**
	 * Wrapper around the service's own method.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   AdminNoticeInterface    $notice     Notice to add to the store.
	 * @param   string                  $store      Name of the store to add the notice to.
	 *
	 * @return  bool
	 */
	public function add_notice( AdminNoticeInterface $notice, string $store = 'dynamic' ): bool {
		return $this->get_admin_notices_service()->add_notice( $notice, $store );
	}

	/**
	 * Wrapper around the service's own method.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $handle     Handle of the notice to retrieve.
	 * @param   string  $store      Name of the store to add the notice to.
	 *
	 * @return  AdminNoticeInterface|null
	 */
	public function get_notice( string $handle, string $store = 'dynamic' ): ?AdminNoticeInterface {
		return $this->get_admin_notices_service()->get_notice( $handle, $store );
	}

	/**
	 * Wrapper around the service's own method.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   AdminNoticeInterface    $notice     Notice to add to the store.
	 * @param   string                  $store      Name of the store to add the notice to.
	 *
	 * @return  bool
	 */
	public function update_notice( AdminNoticeInterface $notice, string $store = 'dynamic' ): bool {
		return $this->get_admin_notices_service()->update_notice( $notice, $store );
	}

	/**
	 * Wrapper around the service's own method.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $handle         Handle of the notice to remove.
	 * @param   string  $store          The name of the store to remove the notice from.
	 *
	 * @return  bool    Whether the operation was successful or not.
	 */
	public function remove_notice( string $handle, string $store = 'dynamic' ): bool {
		return $this->get_admin_notices_service()->remove_notice( $handle, $store );
	}

	// endregion
}
