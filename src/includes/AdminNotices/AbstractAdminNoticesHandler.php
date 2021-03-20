<?php

namespace DeepWebSolutions\Framework\Utilities\AdminNotices;

use DeepWebSolutions\Framework\Foundations\Actions\Outputtable\OutputFailureException;
use DeepWebSolutions\Framework\Foundations\Utilities\Storage\StoreAwareTrait;
use DeepWebSolutions\Framework\Foundations\Utilities\Storage\StoreInterface;
use DeepWebSolutions\Framework\Foundations\Utilities\Storage\Stores\MemoryStore;
use DeepWebSolutions\Framework\Utilities\AdminNotices\AdminNoticeInterface;
use DeepWebSolutions\Framework\Utilities\AdminNotices\AdminNoticesHandlerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

\defined( 'ABSPATH' ) || exit;

/**
 * Encapsulates the most often needed functionality of a notices handler.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\AdminNotices
 */
abstract class AbstractAdminNoticesHandler implements AdminNoticesHandlerInterface {
	// region TRAITS

	use StoreAwareTrait;

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
	 * AbstractAdminNoticesHandler constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   MemoryStore|null        $admin_notices_stores       Store containing the valid admin notices stores.
	 */
	public function __construct( ?MemoryStore $admin_notices_stores = null ) {
		if ( ! \is_null( $admin_notices_stores ) ) {
			$this->set_store( $admin_notices_stores );
		}
	}

	// endregion

	// region INHERITED METHODS

	/**
	 * Returns the type of the handler.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	public function get_type(): string {
		return 'admin-notices';
	}

	/**
	 * Returns a given notice from a given store as long as it's of the handler's type.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $store_id   The ID of the store holding the notice.
	 * @param   string  $handle     The ID of the notice to retrieve.
	 *
	 * @throws  ContainerExceptionInterface     Error while retrieving the entries.
	 *
	 * @return  AdminNoticeInterface|null
	 */
	public function get_notice( string $store_id, string $handle ): ?AdminNoticeInterface {
		$store = $this->get_store_entry( $store_id );
		if ( ! $store instanceof StoreInterface ) {
			return null;
		}

		$notice = $store->get( $handle );
		return ( \get_class( $notice ) === $this->get_id() )
			? $notice : null;
	}

	/**
	 * Returns all the stored notices of the handler's type within a given store.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $store_id   The name of the store to retrieve the notices from.
	 *
	 * @throws  ContainerExceptionInterface     Error while retrieving the entries.
	 *
	 * @return  AdminNoticeInterface[]
	 */
	public function get_notices( string $store_id ): array {
		$store = $this->get_store_entry( $store_id );

		return ( $store instanceof StoreInterface )
			? \array_filter(
				$store->get_all(),
				function( AdminNoticeInterface $notice ) {
					return \get_class( $notice ) === $this->get_id();
				}
			) : array();
	}

	/**
	 * Output all admin notices handled by the handler instance.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @throws  ContainerExceptionInterface     Error while retrieving the entries.
	 *
	 * @return  OutputFailureException|null
	 */
	public function output(): ?OutputFailureException {
		foreach ( $this->get_store()->get_all() as $admin_notices_store ) {
			if ( $admin_notices_store instanceof StoreInterface ) {
				foreach ( $this->get_notices( $admin_notices_store->get_id() ) as $notice ) {
					$result = $this->output_notice( $notice, $admin_notices_store );
					if ( ! \is_null( $result ) ) {
						return $result;
					}

					$this->has_output = true;
					if ( ! $notice->is_persistent() ) {
						$admin_notices_store->remove( $notice->get_id() );
					}
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
	 * @param   AdminNoticeInterface    $notice     Notice to output.
	 * @param   StoreInterface          $store      Store holding the notice.
	 *
	 * @return  OutputFailureException|null
	 */
	protected function output_notice( AdminNoticeInterface $notice, StoreInterface $store ): ?OutputFailureException {
		return $notice->output();
	}

	// endregion
}
