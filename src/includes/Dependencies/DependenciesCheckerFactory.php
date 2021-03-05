<?php

namespace DeepWebSolutions\Framework\Utilities\Dependencies;

use DeepWebSolutions\Framework\Utilities\Dependencies\Checkers\NullChecker;
use DeepWebSolutions\Framework\Utilities\Logging\LoggingService;
use DeepWebSolutions\Framework\Utilities\Logging\LoggingServiceAwareInterface;
use DeepWebSolutions\Framework\Utilities\Logging\LoggingServiceAwareTrait;
use Psr\Log\LogLevel;

defined( 'ABSPATH' ) || exit;

/**
 * Dependencies checker factory to decouple checkers from their using objects.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @package DeepWebSolutions\WP-Framework\Utilities\Dependencies
 */
class DependenciesCheckerFactory implements LoggingServiceAwareInterface {
	// region TRAITS

	use LoggingServiceAwareTrait;

	// endregion

	// region FIELDS AND CONSTANTS

	/**
	 * Collection of instantiated checkers.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     DependenciesCheckerInterface[]
	 */
	protected array $checkers = array();

	/**
	 * Collection of checker-instantiating callables.
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
	 * DependenciesCheckerFactory constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   LoggingService  $logging_service    Instance of the logging service.
	 */
	public function __construct( LoggingService $logging_service ) {
		$this->set_logging_service( $logging_service );
		$this->checkers['NullChecker'] = new NullChecker();
	}

	// endregion

	// region GETTERS

	/**
	 * Returns all instantiated dependencies checkers.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  DependenciesCheckerInterface[]
	 */
	public function get_checkers(): array {
		return $this->checkers;
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
	 * Registers a new callback with the checker factory for instantiating a new checker.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string      $name       The name of the checker.
	 * @param   callable    $callable   The PHP callback required to instantiate it.
	 */
	public function register_callable( string $name, callable $callable ): void {
		$this->callables[ $name ] = $callable;
	}

	/**
	 * Returns a dependencies checker instance.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $name       The name of the checker. Must match with the name used when registering the callback.
	 *
	 * @return  DependenciesCheckerInterface
	 */
	public function get_checker( string $name ): DependenciesCheckerInterface {
		if ( ! isset( $this->checkers[ $name ] ) ) {
			$this->checkers[ $name ] = $this->checkers['NullChecker'];
			if ( is_callable( $this->callables[ $name ] ?? '' ) ) {
				$checker = call_user_func( $this->callables[ $name ] );
				if ( $checker instanceof DependenciesCheckerInterface ) {
					$this->checkers[ $name ] = $checker;
				} else {
					$this->log_event_and_doing_it_wrong(
						__FUNCTION__,
						sprintf( 'Failed to instantiate dependencies checker %s', $name ),
						'1.0.0',
						LogLevel::WARNING,
						'framework'
					);
				}
			}
		}

		return $this->checkers[ $name ];
	}

	// endregion
}
