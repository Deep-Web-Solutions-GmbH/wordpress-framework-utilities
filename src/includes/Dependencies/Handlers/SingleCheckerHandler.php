<?php

namespace DeepWebSolutions\Framework\Utilities\Dependencies\Handlers;

use DeepWebSolutions\Framework\Utilities\Dependencies\AbstractDependenciesHandler;
use DeepWebSolutions\Framework\Utilities\Dependencies\DependenciesCheckerInterface;

\defined( 'ABSPATH' ) || exit;

/**
 * A basic handler implementation for supporting a single checker.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @package DeepWebSolutions\WP-Framework\Utilities\Dependencies\Handlers
 */
class SingleCheckerHandler extends AbstractDependenciesHandler {
	// region FIELDS AND CONSTANTS

	/**
	 * Dependencies checker to use.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     DependenciesCheckerInterface
	 */
	protected DependenciesCheckerInterface $checker;

	// endregion

	// region MAGIC METHODS

	/**
	 * SingleCheckerHandler constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string                              $handler_id             The ID of the handler.
	 * @param   DependenciesCheckerInterface|null   $dependencies_checker   Dependencies checker to use.
	 */
	public function __construct( string $handler_id, ?DependenciesCheckerInterface $dependencies_checker = null ) {
		parent::__construct( $handler_id );

		if ( ! \is_null( $dependencies_checker ) ) {
			$this->set_checker( $dependencies_checker );
		}
	}

	// endregion

	// region GETTERS

	/**
	 * Returns the checker instance.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  DependenciesCheckerInterface
	 */
	public function get_checker(): DependenciesCheckerInterface {
		return $this->checker;
	}

	// endregion

	// region SETTERS

	/**
	 * Sets the checker instance.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   DependenciesCheckerInterface    $dependencies_checker   Checker to use from now on.
	 */
	public function set_checker( DependenciesCheckerInterface $dependencies_checker ): void {
		$this->checker = $dependencies_checker;
	}

	// endregion

	// region INHERITED METHODS

	/**
	 * Returns the dependencies checked for.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array
	 */
	public function get_dependencies(): array {
		return array( $this->checker->get_id() => $this->checker->get_dependencies() );
	}

	/**
	 * Returns the unfulfilled dependencies of the checker.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array
	 */
	public function get_missing_dependencies(): array {
		return array( $this->checker->get_id() => $this->checker->get_missing_dependencies() );
	}

	/**
	 * Returns whether the dependencies are fulfilled or not according to the checker.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  bool[]
	 */
	public function are_dependencies_fulfilled(): array {
		return array( $this->checker->get_id() => $this->checker->are_dependencies_fulfilled() );
	}

	// endregion
}
