<?php

namespace DeepWebSolutions\Framework\Utilities\Dependencies\Checkers;

use DeepWebSolutions\Framework\Utilities\Dependencies\DependenciesCheckerInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Noop implementation of a dependencies checker.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @package DeepWebSolutions\WP-Framework\Utilities\Dependencies\Checkers
 */
class NullChecker implements DependenciesCheckerInterface {
	/**
	 * Returns an empty array.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array
	 */
	public function get_dependencies(): array {
		return array();
	}

	/**
	 * Returns an empty array.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array
	 */
	public function get_missing_dependencies(): array {
		return array();
	}

	/**
	 * Returns true.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  bool
	 */
	public function are_dependencies_fulfilled(): bool {
		return true;
	}
}
