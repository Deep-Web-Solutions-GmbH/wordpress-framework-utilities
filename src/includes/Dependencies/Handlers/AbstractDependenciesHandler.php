<?php

namespace DeepWebSolutions\Framework\Utilities\Dependencies\Handlers;

use DeepWebSolutions\Framework\Utilities\Dependencies\DependenciesHandlerInterface;

\defined( 'ABSPATH' ) || exit;

/**
 * Basic implementation of the dependencies handler interface.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Dependencies\Handlers
 */
abstract class AbstractDependenciesHandler implements DependenciesHandlerInterface {
	// region FIELDS AND CONSTANTS

	/**
	 * Name of the handler.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string
	 */
	protected string $name;

	/**
	 * List of dependencies to check for.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     array
	 */
	protected array $dependencies = array();

	// endregion

	// region MAGIC METHODS

	/**
	 * AbstractDependenciesHandler constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $name           Name of the handler. Should be unique per handler type.
	 * @param   array   $dependencies   List of dependencies to check for.
	 */
	public function __construct( string $name, array $dependencies ) {
		$this->name = $name;
		foreach ( $dependencies as $dependency ) {
			$this->register_dependency( $dependency );
		}
	}

	// endregion

	// region GETTERS

	/**
	 * Returns the name of the handler to differentiate multiple ones of the same type.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * Returns a list of registered dependencies to check for.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array
	 */
	public function get_dependencies(): array {
		return $this->dependencies;
	}

	// endregion

	// region METHODS

	/**
	 * Adds a dependency to the list of dependencies to check for.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   mixed   $dependency     The dependency to check for.
	 *
	 * @return  bool
	 */
	public function register_dependency( $dependency ): bool {
		$dependency = $this->parse_dependency( $dependency );

		if ( \is_null( $dependency ) ) {
			return false;
		} else {
			$this->dependencies = \array_merge( $this->dependencies, $dependency );
			return true;
		}
	}

	/**
	 * Returns whether the dependencies are fulfilled or not.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  bool
	 */
	public function are_dependencies_fulfilled(): bool {
		return empty( $this->get_missing_dependencies() );
	}

	// endregion

	// region HELPERS

	/**
	 * Makes sure the dependency is valid. If that can't be ensured, return null.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   mixed   $dependency     Dependency to parse.
	 *
	 * @return  array|null
	 */
	abstract protected function parse_dependency( $dependency ): ?array;

	// endregion
}
