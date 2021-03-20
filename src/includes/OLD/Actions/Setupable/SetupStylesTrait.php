<?php

namespace DeepWebSolutions\Framework\Utilities\Actions\Setupable;

use DeepWebSolutions\Framework\Foundations\Actions\Setupable\SetupableExtensionTrait;
use DeepWebSolutions\Framework\Foundations\Actions\Setupable\SetupFailureException;
use DeepWebSolutions\Framework\Utilities\Assets\AssetsService;
use DeepWebSolutions\Framework\Utilities\Assets\AssetsServiceAwareInterface;
use DeepWebSolutions\Framework\Utilities\Assets\Handlers\StylesHandlerRegisterTrait;
use DeepWebSolutions\Framework\Utilities\DependencyInjection\ContainerAwareInterface;

\defined( 'ABSPATH' ) || exit;

/**
 * Trait for registering styles of using instances.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Actions\Setupable
 */
trait SetupStylesTrait {
	// region TRAITS

	use StylesHandlerRegisterTrait;
	use SetupableExtensionTrait;

	// endregion

	// region METHODS

	/**
	 * Try to automagically call the styles registration method.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  SetupFailureException|null
	 */
	public function setup_styles(): ?SetupFailureException {
		if ( $this instanceof AssetsServiceAwareInterface ) {
			$handler = $this->get_assets_service()->get_handler( 'default-styles' );
		} elseif ( $this instanceof ContainerAwareInterface ) {
			$service = $this->get_container()->get( AssetsService::class );
			$handler = $service->get_handler( 'default-styles' );
		}

		if ( empty( $handler ) ) {
			return new SetupFailureException( 'Styles registration setup scenario not supported' );
		}

		$this->register_styles( $handler );
		return null;
	}

	// endregion
}
