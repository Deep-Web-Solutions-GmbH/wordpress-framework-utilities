<?php

namespace DeepWebSolutions\Framework\Utilities\AdminNotices\Handlers;

use DeepWebSolutions\Framework\Foundations\Actions\Outputtable\OutputFailureException;
use DeepWebSolutions\Framework\Utilities\AdminNotices\AdminNoticeInterface;
use DeepWebSolutions\Framework\Utilities\AdminNotices\AdminNoticesHandlerInterface;
use DeepWebSolutions\Framework\Utilities\AdminNotices\AdminNoticesStoreFactory;
use DeepWebSolutions\Framework\Utilities\AdminNotices\AdminNoticesStoreFactoryAwareTrait;
use DeepWebSolutions\Framework\Utilities\AdminNotices\AdminNoticesStoreInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Encapsulates the most often needed functionality of a notices handler.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\AdminNotices\Handlers
 */
abstract class AbstractHandler implements AdminNoticesHandlerInterface {
	// region TRAITS

	use AdminNoticesStoreFactoryAwareTrait;

	// endregion

	// region FIELDS AND CONSTANTS

	/**
	 * Whether any user notices have been outputted during the current request.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     bool
	 */
	protected bool $has_output = false;

	// endregion

	// region MAGIC METHODS

	/**
	 * AbstractHandler constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   AdminNoticesStoreFactory    $store_factory      Instance of the admin notices store factory.
	 */
	public function __construct( AdminNoticesStoreFactory $store_factory ) {
		$this->set_admin_notices_store_factory( $store_factory );
	}

	// endregion

	// region INHERITED METHODS

	/**
	 * Returns all the stored notices of the handler's type within a given store.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $store      The name of the store to retrieve the notices from.
	 * @param   array   $params     Any parameters needed to retrieve the notices.
	 *
	 * @return  AdminNoticeInterface[]
	 */
	public function get_notices( string $store, array $params ): array {
		$notices = $this->get_admin_notices_store( $store )->get_notices( $params );
		return array_filter(
			$notices,
			function( AdminNoticeInterface $notice ) {
				return is_a( $notice, $this->get_notices_type() );
			}
		);
	}

	/**
	 * Output all admin notices handled by the handler instance.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  OutputFailureException|null
	 */
	public function output(): ?OutputFailureException {
		$stores = $this->get_admin_notices_store_factory()->get_stores();
		foreach ( $stores as $store ) {
			foreach ( $this->get_notices( $store->get_type(), array() ) as $notice ) {
				$result = $this->output_notice( $notice, $store );
				if ( ! is_null( $result ) ) {
					return $result;
				}

				$this->has_output = true;
				if ( ! $notice->is_persistent() ) {
					$store->remove_notice( $notice->get_handle(), array() );
				}
			}
		}

		return null;
	}

	// endregion

	// region HELPERS

	/**
	 * Allows notice output manipulation by inheriting handlers. By default just calls the output method of the notice.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 *
	 * @param   AdminNoticeInterface        $notice     Notice to output.
	 * @param   AdminNoticesStoreInterface  $store      Store holding the notice.
	 *
	 * @return  OutputFailureException|null
	 */
	protected function output_notice( AdminNoticeInterface $notice, AdminNoticesStoreInterface $store ): ?OutputFailureException {
		return $notice->output();
	}

	// endregion
}
