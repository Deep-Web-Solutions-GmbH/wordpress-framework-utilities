<?php

namespace DeepWebSolutions\Framework\Utilities\AdminNotices;

use DeepWebSolutions\Framework\Utilities\AdminNotices\Stores\DynamicStoreAdmin;
use DeepWebSolutions\Framework\Utilities\AdminNotices\Stores\NullStoreAdmin;
use DeepWebSolutions\Framework\Utilities\AdminNotices\Stores\OptionsStoreAdmin;
use DeepWebSolutions\Framework\Utilities\AdminNotices\Stores\UserMetaStoreAdmin;
use DeepWebSolutions\Framework\Utilities\Logging\LoggingService;
use DeepWebSolutions\Framework\Utilities\Logging\LoggingServiceAwareInterface;
use DeepWebSolutions\Framework\Utilities\Logging\LoggingServiceAwareTrait;
use Psr\Log\LogLevel;

defined( 'ABSPATH' ) || exit;

/**
 * Admin notices store factory.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\AdminNotices
 */
class AdminNoticesStoreFactory implements LoggingServiceAwareInterface {
	// region TRAITS

	use LoggingServiceAwareTrait;

	// endregion

	// region FIELDS AND CONSTANTS

	/**
	 * Collection of instantiated stores.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     AdminNoticesStoreInterface[]
	 */
	protected array $stores = array();

	/**
	 * Collection of store-instantiating callables.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     callable[]
	 */
	protected array $callables = array();

	// endregion

	// region MAGIC METHODS

	/**
	 * AdminNoticesStoreFactory constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   LoggingService  $logging_service    Instance of the logging service.
	 */
	public function __construct( LoggingService $logging_service ) {
		$this->set_logging_service( $logging_service );

		$this->stores['null']      = new NullStoreAdmin();
		$this->stores['dynamic']   = new DynamicStoreAdmin();
		$this->stores['options']   = new OptionsStoreAdmin( '_dws_admin_notices_' . $logging_service->get_plugin()->get_plugin_safe_slug() );
		$this->stores['user-meta'] = new UserMetaStoreAdmin( '_dws_admin_notices_' . $logging_service->get_plugin()->get_plugin_safe_slug() );
	}

	// endregion

	// region GETTERS

	/**
	 * Returns all instantiated stores.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  AdminNoticesStoreInterface[]
	 */
	public function get_stores(): array {
		return $this->stores;
	}

	/**
	 * Returns all registered callables.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  callable[]
	 */
	public function get_callables(): array {
		return $this->callables;
	}

	// endregion

	// region METHODS

	/**
	 * Registers a new callback with the stores factory for instantiating a new store.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string      $name       The name of the store.
	 * @param   callable    $callable   The PHP callback required to instantiate it.
	 */
	public function register_callable( string $name, callable $callable ): void {
		$this->callables[ $name ] = $callable;
	}

	/**
	 * Returns a store instance.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $name       The name of the store. Must match with the name used when registering the callback.
	 *
	 * @return  AdminNoticesStoreInterface
	 */
	public function get_store( string $name ): AdminNoticesStoreInterface {
		if ( ! isset( $this->stores[ $name ] ) ) {
			$this->stores[ $name ] = $this->stores['null'];
			if ( is_callable( $this->callables[ $name ] ?? '' ) ) {
				$store = call_user_func( $this->callables[ $name ] );
				if ( $store instanceof AdminNoticesStoreInterface ) {
					$this->stores[ $name ] = $store;
				} else {
					$this->log_event_and_doing_it_wrong(
						__FUNCTION__,
						sprintf( 'Failed to instantiate admin notices store %s', $name ),
						'1.0.0',
						LogLevel::WARNING,
						'framework'
					);
				}
			}
		}

		return $this->stores[ $name ];
	}

	// endregion
}
