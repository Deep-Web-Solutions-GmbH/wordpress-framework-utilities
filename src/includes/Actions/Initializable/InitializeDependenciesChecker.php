<?php

namespace DeepWebSolutions\Framework\Utilities\Actions\Initializable;

use DeepWebSolutions\Framework\Foundations\Actions\Initializable\InitializableExtensionTrait;
use DeepWebSolutions\Framework\Foundations\Actions\Initializable\InitializationFailureException;
use DeepWebSolutions\Framework\Foundations\PluginComponent\PluginComponentInterface;
use DeepWebSolutions\Framework\Utilities\Dependencies\DependenciesCheckerAwareInterface;
use DeepWebSolutions\Framework\Utilities\Dependencies\DependenciesCheckerAwareTrait;
use DeepWebSolutions\Framework\Utilities\Dependencies\DependenciesCheckerFactory;
use DeepWebSolutions\Framework\Utilities\Dependencies\DependenciesCheckerFactoryAwareInterface;
use DeepWebSolutions\Framework\Utilities\Dependencies\DependenciesCheckerInterface;
use DeepWebSolutions\Framework\Utilities\Dependencies\DependenciesServiceAwareInterface;
use DeepWebSolutions\Framework\Utilities\DependencyInjection\ContainerAwareInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Trait for registering admin notices of using instances.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Actions\Initializable
 */
trait InitializeDependenciesChecker {
	// region TRAITS

	use InitializableExtensionTrait;

	// endregion

	// region METHODS

	/**
	 * Try to automagically register a checker with the dependencies checker factory.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  InitializationFailureException|null
	 */
	public function initialize_dependencies_checker(): ?InitializationFailureException {
		if ( $this instanceof DependenciesCheckerFactoryAwareInterface ) {
			$factory = $this->get_dependencies_checker_factory();
		} elseif ( $this instanceof DependenciesServiceAwareInterface ) {
			$factory = $this->get_dependencies_service()->get_dependencies_checker_factory();
		} elseif ( $this instanceof ContainerAwareInterface ) {
			$factory = $this->get_container()->get( DependenciesCheckerFactory::class );
		} else {
			return new InitializationFailureException( 'Dependencies checker initialization scenario not supported' );
		}

		$dependencies_checker = $this->register_dependencies_checker();
		if ( $this instanceof DependenciesCheckerAwareInterface ) {
			$this->set_dependencies_checker( $dependencies_checker );
		}

		$checker_name = ( $this instanceof PluginComponentInterface ) ? $this->get_instance_id() : get_class( $this );
		$factory->register_callable(
			$checker_name,
			function() use ( $dependencies_checker ) {
				return $dependencies_checker;
			}
		);

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
	abstract protected function register_dependencies_checker(): DependenciesCheckerInterface;

	// endregion
}
