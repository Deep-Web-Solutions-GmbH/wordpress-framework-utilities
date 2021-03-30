<?php

namespace DeepWebSolutions\Framework\Utilities\Actions\Setupable;

use DeepWebSolutions\Framework\Foundations\Actions\Setupable\SetupableExtensionTrait;
use DeepWebSolutions\Framework\Foundations\Actions\Setupable\SetupFailureException;
use DeepWebSolutions\Framework\Foundations\Utilities\DependencyInjection\ContainerAwareInterface;
use DeepWebSolutions\Framework\Utilities\Assets\AssetsService;
use DeepWebSolutions\Framework\Utilities\Assets\AssetsServiceAwareInterface;
use DeepWebSolutions\Framework\Utilities\Assets\Handlers\ScriptsStylesHandlerRegisterTrait;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

\defined( 'ABSPATH' ) || exit;

/**
 * Trait for registering scripts and styles of using instances.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Actions\Setupable
 */
trait SetupScriptsStylesTrait {
	// region TRAITS

	use ScriptsStylesHandlerRegisterTrait;
	use SetupableExtensionTrait;

	// endregion

	// region METHODS

	/**
	 * Try to automagically call the scripts and styles registration method.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @throws  NotFoundExceptionInterface      Thrown if the container can't find an entry.
	 * @throws  ContainerExceptionInterface     Thrown if the container encounters some other error.
	 *
	 * @return  SetupFailureException|null
	 */
	public function setup_scripts_styles(): ?SetupFailureException {
		if ( $this instanceof AssetsServiceAwareInterface ) {
			$scripts_handler = $this->get_assets_service()->get_handler( 'default-scripts' );
			$styles_handler  = $this->get_assets_service()->get_handler( 'default-styles' );
		} elseif ( $this instanceof ContainerAwareInterface ) {
			$service         = $this->get_container()->get( AssetsService::class );
			$scripts_handler = $service->get_handler( 'default-scripts' );
			$styles_handler  = $service->get_handler( 'default-styles' );
		}

		if ( empty( $scripts_handler ) || empty( $styles_handler ) ) {
			return new SetupFailureException( 'Scripts and styles registration setup scenario not supported' );
		}

		$this->register_scripts_and_styles( $scripts_handler, $styles_handler );
		return null;
	}

	// endregion
}
