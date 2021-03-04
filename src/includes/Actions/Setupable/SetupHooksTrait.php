<?php

namespace DeepWebSolutions\Framework\Utilities\Actions\Setupable;

use DeepWebSolutions\Framework\Foundations\Actions\Setupable\SetupableExtensionTrait;
use DeepWebSolutions\Framework\Foundations\Actions\Setupable\SetupFailureException;
use DeepWebSolutions\Framework\Utilities\DependencyInjection\ContainerAwareInterface;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksService;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksServiceAwareInterface;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksServiceRegisterTrait;

defined( 'ABSPATH' ) || exit;

/**
 * Trait for registering hooks of using instances.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Actions\Setupable
 */
trait SetupHooksTrait {
	// region TRAITS

	use HooksServiceRegisterTrait;
	use SetupableExtensionTrait;

	// endregion

	// region METHODS

	/**
	 * Try to automagically call the hooks registration method.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  SetupFailureException|null
	 */
	public function setup_hooks(): ?SetupFailureException {
		if ( $this instanceof HooksServiceAwareInterface ) {
			$service = $this->get_hooks_service();
		} elseif ( $this instanceof ContainerAwareInterface ) {
			$service = $this->get_container()->get( HooksService::class );
		} else {
			return new SetupFailureException( 'Hooks registration setup scenario not supported' );
		}

		$this->register_hooks( $service );
		return null;
	}

	// endregion
}
