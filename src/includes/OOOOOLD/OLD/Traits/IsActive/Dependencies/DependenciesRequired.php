<?php

namespace DeepWebSolutions\Framework\Utilities\Traits\IsActive\Dependencies;

use DeepWebSolutions\Framework\Utilities\Interfaces\States\Traits\IsActive\IsActiveExtensionTrait;
use DeepWebSolutions\Framework\Utilities\Services\Traits\Dependencies\DependenciesAwareTrait as UtilitiesDependencies;

defined( 'ABSPATH' ) || exit;

/**
 * Trait for working with the dependencies service in an activeable environment.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Traits\IsActive\Dependencies
 */
trait DependenciesRequired {
	use UtilitiesDependencies;
	use IsActiveExtensionTrait;

	/**
	 * If the using class is IsActiveExtensionTrait, prevent its activation if the required dependencies are not fulfilled.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  bool
	 */
	public function is_active_dependencies_required(): bool {
		$are_dependencies_fulfilled = true;

		foreach ( $this->get_dependencies_service()->get_dependencies_status() as $dependencies_type => $dependencies_status ) {
			$are_dependencies_fulfilled = $are_dependencies_fulfilled && $dependencies_status['required'];
		}

		return $are_dependencies_fulfilled;
	}
}
