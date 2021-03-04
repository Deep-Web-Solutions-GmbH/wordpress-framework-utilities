<?php

namespace DeepWebSolutions\Framework\Utilities\Actions\Setupable;

use DeepWebSolutions\Framework\Foundations\Actions\Setupable\SetupableExtensionTrait;
use DeepWebSolutions\Framework\Foundations\Actions\Setupable\SetupFailureException;
use DeepWebSolutions\Framework\Utilities\Assets\AssetsServiceAwareInterface;
use DeepWebSolutions\Framework\Utilities\Assets\Handlers\ScriptsHandler;
use DeepWebSolutions\Framework\Utilities\Assets\Handlers\ScriptsHandlerRegisterTrait;
use DeepWebSolutions\Framework\Utilities\DependencyInjection\ContainerAwareInterface;

defined( 'ABSPATH' ) || exit;

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
	 * @return  SetupFailureException|null
	 */
	public function setup_scripts(): ?SetupFailureException {
		if ( $this instanceof AssetsServiceAwareInterface ) {
			$service = $this->get_assets_service();
			$handler = null;

			foreach ( $service->get_handlers() as $assets_handler ) {
				if ( $assets_handler instanceof ScriptsHandler ) {
					$handler = $assets_handler;
					break;
				}
			}
		} elseif ( $this instanceof ContainerAwareInterface ) {
			$handler = $this->get_container()->get( ScriptsHandler::class );
		}

		if ( empty( $handler ) ) {
			return new SetupFailureException( 'Scripts registration setup scenario not supported' );
		}

		$this->register_scripts( $handler );
		return null;
	}

	// endregion
}
