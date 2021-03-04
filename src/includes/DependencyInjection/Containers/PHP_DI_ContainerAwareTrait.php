<?php

namespace DeepWebSolutions\Framework\Utilities\DependencyInjection\Containers;

use DeepWebSolutions\Framework\Utilities\DependencyInjection\ContainerAwareTrait;
use DI\Container;
use LogicException;
use Psr\Container\ContainerInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Basic implementation of the PHP_DI-container-aware interface.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\DependencyInjection\Containers
 */
trait PHP_DI_ContainerAwareTrait {
	// region TRAITS

	use ContainerAwareTrait { get_container as get_container_di_aware_trait; }

	// endregion

	// region GETTERS

	/**
	 * Gets an instance of a dependency injection container.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @throws  LogicException  Thrown if the set container is of the wrong type.
	 *
	 * @return  Container
	 */
	public function get_container(): Container {
		$container = $this->get_container_di_aware_trait();
		if ( ! $container instanceof Container ) {
			throw new LogicException( 'Container instance is of the wrong type' );
		}

		return $container;
	}

	// endregion

	// region SETTERS

	/**
	 * Sets a container on the instance.
	 *
	 * @throws  LogicException  Thrown if the container is of the wrong type.
	 *
	 * @param   ContainerInterface      $container      Container instance to use from now on.
	 */
	public function set_container( ContainerInterface $container ): void {
		if ( ! $container instanceof Container ) {
			throw new LogicException( 'DI container is of the wrong type.' );
		}

		$this->di_container = $container;
	}

	// endregion
}
