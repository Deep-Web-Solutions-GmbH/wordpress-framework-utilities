<?php

namespace DeepWebSolutions\Framework\Utilities\Actions\Setupable;

use DeepWebSolutions\Framework\Foundations\Actions\Setupable\SetupableExtensionTrait;
use DeepWebSolutions\Framework\Foundations\Actions\Setupable\SetupFailureException;
use DeepWebSolutions\Framework\Foundations\Utilities\DependencyInjection\ContainerAwareInterface;
use DeepWebSolutions\Framework\Utilities\Assets\AssetsService;
use DeepWebSolutions\Framework\Utilities\Assets\AssetsServiceAwareInterface;
use DeepWebSolutions\Framework\Utilities\Assets\Handlers\ScriptsHandlerRegisterTrait;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

\defined( 'ABSPATH' ) || exit;

/**
 * Trait for registering scripts of using instances.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Actions\Setupable
 */
trait SetupScriptsTrait {
	// region TRAITS

	use ScriptsHandlerRegisterTrait;
	use SetupableExtensionTrait;

	// endregion

	// region METHODS

	/**
	 * Try to automagically call the scripts registration method.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @throws  NotFoundExceptionInterface      Thrown if the container can't find an entry.
	 * @throws  ContainerExceptionInterface     Thrown if the container encounters some other error.
	 *
	 * @return  SetupFailureException|null
	 */
	public function setup_scripts(): ?SetupFailureException {
		if ( $this instanceof AssetsServiceAwareInterface ) {
			$handler = $this->get_assets_service()->get_handler( 'default-scripts' );
		} elseif ( $this instanceof ContainerAwareInterface ) {
			$service = $this->get_container()->get( AssetsService::class );
			$handler = $service->get_handler( 'default-scripts' );
		}

		if ( empty( $handler ) ) {
			return new SetupFailureException( 'Scripts registration setup scenario not supported' );
		}

		$this->register_scripts( $handler );
		return null;
	}

	// endregion
}
