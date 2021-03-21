<?php

namespace DeepWebSolutions\Framework\Utilities\Actions\Initializable;

use DeepWebSolutions\Framework\Foundations\Actions\Initializable\InitializableExtensionTrait;
use DeepWebSolutions\Framework\Foundations\Actions\Initializable\InitializationFailureException;
use DeepWebSolutions\Framework\Foundations\Utilities\DependencyInjection\ContainerAwareInterface;
use DeepWebSolutions\Framework\Utilities\Dependencies\DependenciesHandlerInterface;
use DeepWebSolutions\Framework\Utilities\Dependencies\DependenciesService;
use DeepWebSolutions\Framework\Utilities\Dependencies\DependenciesServiceAwareInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

\defined( 'ABSPATH' ) || exit;

/**
 * Trait for initializing a dependencies handler on the using instance.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Actions\Initializable
 */
trait InitializeDependenciesHandlerTrait {
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
	 * @throws  NotFoundExceptionInterface      Thrown if the container can't find an entry.
	 * @throws  ContainerExceptionInterface     Thrown if the container encounters some other error.
	 *
	 * @return  InitializationFailureException|null
	 */
	public function initialize_dependencies_handler(): ?InitializationFailureException {
		if ( $this instanceof DependenciesServiceAwareInterface ) {
			$service = $this->get_dependencies_service();
		} elseif ( $this instanceof ContainerAwareInterface ) {
			$service = $this->get_container()->get( DependenciesService::class );
		} else {
			return new InitializationFailureException( 'Dependencies handler initialization scenario not supported' );
		}

		$service->register_handler( $this->get_dependencies_handler() );
		return null;
	}

	/**
	 * Using classes should instantiate a dependencies handler in here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  DependenciesHandlerInterface
	 */
	abstract public function get_dependencies_handler(): DependenciesHandlerInterface;

	// endregion
}
