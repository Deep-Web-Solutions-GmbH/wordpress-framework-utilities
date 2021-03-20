<?php

namespace DeepWebSolutions\Framework\Utilities\Dependencies;

use DeepWebSolutions\Framework\Foundations\Plugin\PluginAwareInterface;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginAwareTrait;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginInterface;
use DeepWebSolutions\Framework\Utilities\Logging\LoggingService;
use DeepWebSolutions\Framework\Utilities\Logging\LoggingServiceAwareInterface;
use DeepWebSolutions\Framework\Utilities\Logging\LoggingServiceAwareTrait;

\defined( 'ABSPATH' ) || exit;

/**
 * Checks the status of a set of dependencies.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @package DeepWebSolutions\WP-Framework\Utilities\Dependencies
 */
class DependenciesService implements LoggingServiceAwareInterface, PluginAwareInterface {
	// region TRAITS

	use LoggingServiceAwareTrait;
	use PluginAwareTrait;

	// endregion

	// region FIELDS AND CONSTANTS

	/**
	 * Collection of registered dependencies checkers.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     DependenciesCheckerInterface[]
	 */
	protected array $checkers;

	// endregion

	// region MAGIC METHODS

	/**
	 * DependenciesService constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   PluginInterface                     $plugin             Instance of the plugin.
	 * @param   LoggingService                      $logging_service    Instance of the logging service.
	 * @param   DependenciesCheckerInterface[]      $checkers           Dependencies checkers.
	 */
	public function __construct( PluginInterface $plugin, LoggingService $logging_service, array $checkers = array() ) {
		$this->set_plugin( $plugin );
		$this->set_logging_service( $logging_service );
		$this->set_checkers( $checkers );
	}

	// endregion

	// region GETTERS

	/**
	 * Returns the list of registered checkers.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  DependenciesCheckerInterface[]
	 */
	public function get_checkers(): array {
		return $this->checkers;
	}

	// endregion

	// region SETTERS

	/**
	 * Sets the list of checkers.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $checkers   Collection of dependencies checkers.
	 *
	 * @return  DependenciesService
	 */
	public function set_checkers( array $checkers ): DependenciesService {
		$this->checkers = array();

		foreach ( $checkers as $name => $checker ) {
			if ( $checker instanceof DependenciesCheckerInterface && \is_string( $name ) ) {
				$this->register_checker( $name, $checker );
			}
		}

		return $this;
	}

	// endregion

	// region METHODS

	/**
	 * Adds a checker to the list of dependencies checkers.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string                          $name       Unique name of the checker.
	 * @param   DependenciesCheckerInterface    $checker    Checker to add.
	 *
	 * @return  DependenciesService
	 */
	public function register_checker( string $name, DependenciesCheckerInterface $checker ): DependenciesService {
		if ( $checker instanceof PluginAwareInterface ) {
			$checker->set_plugin( $this->get_plugin() );
		}
		if ( $checker instanceof LoggingServiceAwareInterface ) {
			$checker->set_logging_service( $this->get_logging_service() );
		}

		$this->checkers[ $name ] = $checker;
		return $this;
	}

	/**
	 * Returns a given checker from the list of registered ones.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $name   Unique name of the checker to retrieve.
	 *
	 * @return  DependenciesCheckerInterface|null
	 */
	public function get_checker( string $name ): ?DependenciesCheckerInterface {
		return $this->get_checkers()[ $name ] ?? null;
	}

	/**
	 * Returns the dependencies checked by a given checker.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $checker_name   The checker to retrieve the dependencies from.
	 *
	 * @return  array
	 */
	public function get_dependencies( string $checker_name ): array {
		return $this->get_checker( $checker_name )->get_dependencies();
	}

	/**
	 * Returns the missing dependencies of a given checker.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $checker_name   The checker to retrieve the missing dependencies from.
	 *
	 * @return  array
	 */
	public function get_missing_dependencies( string $checker_name ): array {
		return $this->get_checker( $checker_name )->get_missing_dependencies();
	}

	/**
	 * Returns the dependencies status of a given checker.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $checker_name   The checker to retrieve the dependencies status from.
	 *
	 * @return  mixed
	 */
	public function are_dependencies_fulfilled( string $checker_name ) {
		return $this->get_checker( $checker_name )->are_dependencies_fulfilled();
	}

	// endregion
}
