<?php

namespace DeepWebSolutions\Framework\Utilities\Actions\Initializable;

use DeepWebSolutions\Framework\Foundations\Actions\Initializable\InitializableExtensionTrait;
use DeepWebSolutions\Framework\Foundations\Actions\Initializable\InitializationFailureException;
use DeepWebSolutions\Framework\Foundations\PluginComponent\PluginComponentInterface;
use DeepWebSolutions\Framework\Utilities\Dependencies\DependenciesCheckerInterface;
use DeepWebSolutions\Framework\Utilities\Dependencies\DependenciesService;
use DeepWebSolutions\Framework\Utilities\Dependencies\DependenciesServiceAwareInterface;
use DeepWebSolutions\Framework\Utilities\DependencyInjection\ContainerAwareInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Trait for initializing a dependencies checker on the using instance.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Actions\Initializable
 */
trait InitializeDependenciesCheckerTrait {
	// region TRAITS

	use InitializableExtensionTrait;

	// endregion

	// region METHODS

	/**
	 * Try to automagically register an default instance checker with the dependencies service.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  InitializationFailureException|null
	 */
	public function initialize_dependencies_checker(): ?InitializationFailureException {
		if ( $this instanceof DependenciesServiceAwareInterface ) {
			$service = $this->get_dependencies_service();
		} elseif ( $this instanceof ContainerAwareInterface ) {
			$service = $this->get_container()->get( DependenciesService::class );
		} else {
			return new InitializationFailureException( 'Dependencies checker initialization scenario not supported' );
		}

		$checker_name         = ( $this instanceof PluginComponentInterface ) ? $this->get_instance_id() : get_class( $this );
		$dependencies_checker = $this->get_dependencies_checker();

		$service->register_checker( $checker_name, $dependencies_checker );
		return null;
	}

	/**
	 * Using classes should instantiate a dependencies checker in here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  DependenciesCheckerInterface
	 */
	abstract public function get_dependencies_checker(): DependenciesCheckerInterface;

	// endregion
}
