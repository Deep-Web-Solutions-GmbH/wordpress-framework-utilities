<?php

namespace DeepWebSolutions\Framework\Utilities\Actions\Setupable;

use DeepWebSolutions\Framework\Foundations\Actions\Setupable\SetupableExtensionTrait;
use DeepWebSolutions\Framework\Foundations\Actions\Setupable\SetupFailureException;
use DeepWebSolutions\Framework\Utilities\DependencyInjection\ContainerAwareInterface;
use DeepWebSolutions\Framework\Utilities\Shortcodes\ShortcodesService;
use DeepWebSolutions\Framework\Utilities\Shortcodes\ShortcodesServiceAwareInterface;
use DeepWebSolutions\Framework\Utilities\Shortcodes\ShortcodesServiceRegisterTrait;

defined( 'ABSPATH' ) || exit;

/**
 * Trait for registering shortcodes of using instances.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Actions\Setupable
 */
trait SetupShortcodesTrait {
	// region TRAITS

	use ShortcodesServiceRegisterTrait;
	use SetupableExtensionTrait;

	// endregion

	// region METHODS

	/**
	 * Try to automagically call the shortcodes registration method.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  SetupFailureException|null
	 */
	public function setup_shortcodes(): ?SetupFailureException {
		if ( $this instanceof ShortcodesServiceAwareInterface ) {
			$service = $this->get_shortcodes_service();
		} elseif ( $this instanceof ContainerAwareInterface ) {
			$service = $this->get_container()->get( ShortcodesService::class );
		} else {
			return new SetupFailureException( 'Shortcodes registration setup scenario not supported' );
		}

		$this->register_shortcodes( $service );
		return null;
	}

	// endregion
}
