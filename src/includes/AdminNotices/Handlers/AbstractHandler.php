<?php

namespace DeepWebSolutions\Framework\Utilities\AdminNotices\Handlers;

use DeepWebSolutions\Framework\Foundations\Plugin\PluginInterface;
use DeepWebSolutions\Framework\Utilities\AdminNotices\AdminNoticeInterface;
use DeepWebSolutions\Framework\Utilities\AdminNotices\AdminNoticesHandlerInterface;
use DeepWebSolutions\Framework\Utilities\AdminNotices\AdminNoticesStoreFactory;
use DeepWebSolutions\Framework\Utilities\AdminNotices\AdminNoticesStoreFactoryAwareTrait;

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
	 * @param   AdminNoticesStoreFactory    $store_factory      Instance of the amind notices store factory.
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
	 * Output all  user specific admin notices.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   PluginInterface     $plugin     Instance of the plugin.
	 * @param   array               $params     Any parameters needed to retrieve the notices.
	 */
	public function output_notices( PluginInterface $plugin, array $params ): void {
		$stores = $this->get_admin_notices_store_factory()->get_stores();
		foreach ( $stores as $store ) {
			foreach ( $this->get_notices( $store->get_type(), $params ) as $notice ) {
				if ( $this->should_output_notice( $notice ) ) {
					$this->has_output = true;
					$notice->output( $plugin, $store->get_type() );

					if ( ! $notice->is_persistent() ) {
						$store->remove_notice( $notice->get_handle(), $params );
					}
				}
			}
		}
	}

	// endregion

	// region HELPERS

	/**
	 * Checks whether a notice is eligible to be outputted.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   AdminNoticeInterface    $notice     The notice to check for eligibility.
	 *
	 * @return  bool
	 */
	public function should_output_notice( AdminNoticeInterface $notice ): bool {
		return $notice->should_output();
	}

	// endregion
}
