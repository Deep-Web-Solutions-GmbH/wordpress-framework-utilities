<?php

namespace DeepWebSolutions\Framework\Utilities\Dependencies\Handlers;

use DeepWebSolutions\Framework\Utilities\Dependencies\AbstractDependenciesHandler;
use DeepWebSolutions\Framework\Utilities\Dependencies\DependenciesCheckerInterface;

\defined( 'ABSPATH' ) || exit;

/**
 * A basic handler implementation for supporting multiple checkers.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @package DeepWebSolutions\WP-Framework\Utilities\Dependencies\Handlers
 */
class MultiCheckerHandler extends AbstractDependenciesHandler {
	// region FIELDS AND CONSTANTS

	/**
	 * Collection of checkers to use.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     DependenciesCheckerInterface[]
	 */
	protected array $checkers;

	// endregion

	// region MAGIC METHODS

	/**
	 * MultiCheckerHandler constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string                          $handler_id             The ID of the handler.
	 * @param   DependenciesCheckerInterface[]  $dependencies_checker   Dependencies checkers to use.
	 */
	public function __construct( string $handler_id, array $dependencies_checker = array() ) {
		parent::__construct( $handler_id );
		$this->set_checkers( $dependencies_checker );
	}

	// endregion

	// region GETTERS

	/**
	 * Returns all the checker instances.
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
	 * Sets the checker instances.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   DependenciesCheckerInterface[]  $checkers   Checkers to use from now on.
	 *
	 * @return  MultiCheckerHandler
	 */
	public function set_checkers( array $checkers ): MultiCheckerHandler {
		$this->checkers = array();

		foreach ( $checkers as $checker ) {
			if ( $checker instanceof DependenciesCheckerInterface ) {
				$this->register_checker( $checker );
			}
		}

		return $this;
	}

	// endregion

	// region METHODS

	/**
	 * Registers a new checker with the handler.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   DependenciesCheckerInterface    $checker    Checker to add.
	 *
	 * @return  MultiCheckerHandler
	 */
	public function register_checker( DependenciesCheckerInterface $checker ): MultiCheckerHandler {
		$this->checkers[] = $checker;
		return $this;
	}

	// endregion

	// region INHERITED METHODS

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function get_dependencies(): array {
		return $this->walk_checkers( 'get_dependencies' );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function get_missing_dependencies(): array {
		return $this->walk_checkers( 'get_missing_dependencies' );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function are_dependencies_fulfilled(): array {
		return $this->walk_checkers( 'are_dependencies_fulfilled' );
	}

	// endregion

	// region HELPERS

	/**
	 * Walks over all registered checkers and compiles an array of results generated by calling a method on each of them.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $method     Method to call on each handler to get the result.
	 *
	 * @return  array
	 */
	protected function walk_checkers( string $method ): array {
		$result = array();

		foreach ( $this->checkers as $checker ) {
			if ( \method_exists( $checker, $method ) ) {
				$result[ $checker->get_type() ][ $checker->get_id() ] = \call_user_func( array( $checker, $method ) );
			}
		}

		return $result;
	}

	// endregion
}
