<?php

namespace DeepWebSolutions\Framework\Utilities\States\Activeable;

use DeepWebSolutions\Framework\Foundations\Exceptions\NotImplementedException;
use DeepWebSolutions\Framework\Foundations\PluginComponent\PluginComponentInterface;
use DeepWebSolutions\Framework\Foundations\States\Activeable\ActiveableExtensionTrait;
use DeepWebSolutions\Framework\Helpers\DataTypes\Arrays;
use DeepWebSolutions\Framework\Utilities\Dependencies\DependenciesCheckerAwareInterface;
use DeepWebSolutions\Framework\Utilities\Dependencies\DependenciesServiceAwareInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Abstract trait for dependent activation of instances with dependencies.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Foundations\WordPress
 */
trait DependenciesTrait {
	use ActiveableExtensionTrait;

	/**
	 * If the using class is IsActiveExtensionTrait, prevent its activation if both optional and required dependencies are not fulfilled.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @throws  NotImplementedException     Thrown when using this function in an unsupported context.
	 *
	 * @return  bool
	 */
	public function is_active_dependencies(): bool {
		if ( $this instanceof DependenciesCheckerAwareInterface ) {
			$are_deps_fulfilled = $this->get_dependencies_checker()->are_dependencies_fulfilled();
		} elseif ( $this instanceof DependenciesServiceAwareInterface && $this instanceof PluginComponentInterface ) {
			$are_deps_fulfilled = $this->get_dependencies_service()->are_dependencies_fulfilled( $this->get_instance_id() );
		} else {
			throw new NotImplementedException( 'Dependency checking scenario not supported' );
		}

		return is_array( $are_deps_fulfilled )
			? is_null( Arrays::search_recursive( $are_deps_fulfilled, false, true ) )
			: $are_deps_fulfilled;
	}
}
